<?php

namespace App\Jobs;

use App\Models\FeatureUpdate;
use App\Services\FeaturePackageInspector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScaffoldFeatureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public FeatureUpdate $featureUpdate) {}

    public function handle(): void
    {
        try {
            $slug    = $this->featureUpdate->slug;
            $model   = Str::studly($slug);                          // e.g. PrayerRequest
            $table   = Str::snake(Str::plural($model));             // prayer_requests
            $datePfx = now()->format('Y_m_d_His');
            $migFile = "{$datePfx}_create_{$table}_table";

            // Resolve scaffold options from payload (all enabled by default)
            $scaffold    = $this->featureUpdate->payload['scaffold'] ?? [];
            $doModel      = empty($scaffold) || isset($scaffold['model']);
            $doMigration  = empty($scaffold) || isset($scaffold['migration']);
            $doFactory    = empty($scaffold) || isset($scaffold['factory']);
            $doSeeder     = empty($scaffold) || isset($scaffold['seeder']);
            $doController = empty($scaffold) || isset($scaffold['controller']);
            $doRequest    = empty($scaffold) || isset($scaffold['request']);
            $seedCount    = (int) ($this->featureUpdate->payload['seed_count'] ?? 50);

            // Resolve custom fields (fall back to default title/description)
            $fields = $this->featureUpdate->payload['fields'] ?? [];
            if (empty($fields)) {
                $fields = [
                    ['name' => 'title',       'type' => 'string', 'nullable' => false],
                    ['name' => 'description',  'type' => 'text',   'nullable' => true],
                ];
            }

            // ── 1) Migration ──────────────────────────────────────
            if ($doMigration) {
                $migrationPath = database_path("migrations/tenant/{$migFile}.php");
                File::put($migrationPath, $this->migrationStub($table, $fields));
            }

            // ── 2) Model ──────────────────────────────────────────
            if ($doModel) {
                $modelPath = app_path("Models/{$model}.php");
                if (! File::exists($modelPath)) {
                    File::put($modelPath, $this->modelStub($model, $fields));
                }
            }

            // ── 3) Factory ────────────────────────────────────────
            if ($doFactory) {
                $factoryPath = database_path("factories/{$model}Factory.php");
                if (! File::exists($factoryPath)) {
                    File::put($factoryPath, $this->factoryStub($model, $fields));
                }
            }

            // ── 4) Seeder ─────────────────────────────────────────
            if ($doSeeder) {
                $seederPath = database_path("seeders/{$model}Seeder.php");
                if (! File::exists($seederPath)) {
                    File::put($seederPath, $this->seederStub($model, $seedCount));
                }
            }

            // ── 5) Controller ─────────────────────────────────────
            if ($doController) {
                $controllerPath = app_path("Http/Controllers/{$model}Controller.php");
                if (! File::exists($controllerPath)) {
                    File::put($controllerPath, $this->controllerStub($model, $table));
                }
            }

            // ── 6) Form Request ───────────────────────────────────
            if ($doRequest) {
                $requestPath = app_path("Http/Requests/{$model}Request.php");
                if (! File::exists($requestPath)) {
                    File::ensureDirectoryExists(app_path('Http/Requests'));
                    File::put($requestPath, $this->requestStub($model, $fields));
                }
            }

            // ── 7) Update payload ─────────────────────────────────
            $updatedPayload = array_merge($this->featureUpdate->payload ?? [], [
                'model'           => $model,
                'migration_file'  => $migFile,
                'seeder_class'    => "{$model}Seeder",
                'factory_class'   => "{$model}Factory",
                'factory_count'   => $seedCount,
                'request_class'   => "{$model}Request",
                'request_file'    => "app/Http/Requests/{$model}Request.php",
                'controller_file' => "app/Http/Controllers/{$model}Controller.php",
                'files_generated' => true,
            ]);

            $updatedPayload['artifact_manifest'] = app(FeaturePackageInspector::class)
                ->buildArtifactManifest($updatedPayload);

            $this->featureUpdate->update([
                'payload' => $updatedPayload,
            ]);

            Log::info("ScaffoldFeatureJob: Successfully scaffolded '{$model}' for feature '{$this->featureUpdate->name}'.");

        } catch (\Throwable $e) {
            Log::error("ScaffoldFeatureJob failed for feature [{$this->featureUpdate->id}]: {$e->getMessage()}");

            $this->featureUpdate->update(['status' => 'scaffold_failed']);

            throw $e;
        }
    }

    /* ── Stub generators ──────────────────────────────────────── */

    private function migrationStub(string $table, array $fields): string
    {
        $columns = '';
        foreach ($fields as $field) {
            $nullable = isset($field['nullable']) && $field['nullable'];
            $col = "\$t->{$field['type']}('{$field['name']}')";
            if ($nullable) {
                $col .= '->nullable()';
            }
            $columns .= "            {$col};\n";
        }
        $columns .= "            \$t->foreignId('user_id')->constrained()->cascadeOnDelete();\n";

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$t) {
            \$t->id();
{$columns}            \$t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    private function modelStub(string $model, array $fields): string
    {
        $fillableNames = array_column($fields, 'name');
        $fillableNames[] = 'user_id';
        $fillableStr = "'" . implode("', '", $fillableNames) . "'";

        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$model} extends Model
{
    use HasFactory;

    protected \$fillable = [{$fillableStr}];
}
PHP;
    }

    private function factoryStub(string $model, array $fields): string
    {
        $fakeLines = '';
        foreach ($fields as $field) {
            $fake = match($field['type']) {
                'string'    => "\$this->faker->sentence(4)",
                'text'      => "\$this->faker->paragraph(2)",
                'integer'   => "\$this->faker->numberBetween(1, 100)",
                'boolean'   => "\$this->faker->boolean()",
                'date'      => "\$this->faker->date()",
                'foreignId' => "1",
                default     => "\$this->faker->word()",
            };
            $fakeLines .= "            '{$field['name']}' => {$fake},\n";
        }
        $fakeLines .= "            'user_id' => 1,\n";

        return <<<PHP
<?php

namespace Database\Factories;

use App\Models\\{$model};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$model}Factory extends Factory
{
    protected \$model = {$model}::class;

    public function definition(): array
    {
        return [
{$fakeLines}        ];
    }
}
PHP;
    }

    private function seederStub(string $model, int $count): string
    {
        return <<<PHP
<?php

namespace Database\Seeders;

use App\Models\\{$model};
use Illuminate\Database\Seeder;

class {$model}Seeder extends Seeder
{
    public function run(): void
    {
        {$model}::factory()->count({$count})->create();
    }
}
PHP;
    }

    private function controllerStub(string $model, string $table): string
    {
        $varPlural = Str::camel(Str::plural($model));

        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\{$model};
use Illuminate\Http\Request;

class {$model}Controller extends Controller
{
    public function index()
    {
        return view('{$table}.index', ['{$varPlural}' => {$model}::all()]);
    }

    public function show({$model} \$record)
    {
        return view('{$table}.show', compact('record'));
    }
}
PHP;
    }

    private function requestStub(string $model, array $fields): string
    {
        $rules = '';
        foreach ($fields as $field) {
            $nullable = isset($field['nullable']) && $field['nullable'];
            $rule = $nullable ? "'nullable'" : "'required'";
            $typeRule = match($field['type']) {
                'string'    => "'string', 'max:255'",
                'text'      => "'string'",
                'integer'   => "'integer'",
                'boolean'   => "'boolean'",
                'date'      => "'date'",
                'foreignId' => "'integer'",
                default     => "'string'",
            };
            $rules .= "            '{$field['name']}' => [{$rule}, {$typeRule}],\n";
        }
        $rules .= "            'user_id' => ['required', 'integer', 'exists:users,id'],\n";

        return <<<PHP
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$model}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
{$rules}        ];
    }
}
PHP;
    }
}
