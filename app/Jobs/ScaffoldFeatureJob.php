<?php

namespace App\Jobs;

use App\Models\FeatureUpdate;
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
            $slug   = $this->featureUpdate->slug;
            $model  = Str::studly($slug);                          // e.g. PrayerRequest
            $table  = Str::snake(Str::plural($model));             // prayer_requests
            $datePfx = now()->format('Y_m_d_His');
            $migFile = "{$datePfx}_create_{$table}_table";

            // ── 1) Migration ──────────────────────────────────────
            $migrationPath = database_path("migrations/tenant/{$migFile}.php");
            File::put($migrationPath, $this->migrationStub($table));

            // ── 2) Model ──────────────────────────────────────────
            $modelPath = app_path("Models/{$model}.php");
            if (! File::exists($modelPath)) {
                File::put($modelPath, $this->modelStub($model));
            }

            // ── 3) Factory ────────────────────────────────────────
            $factoryPath = database_path("factories/{$model}Factory.php");
            if (! File::exists($factoryPath)) {
                File::put($factoryPath, $this->factoryStub($model));
            }

            // ── 4) Seeder ─────────────────────────────────────────
            $seederPath = database_path("seeders/{$model}Seeder.php");
            if (! File::exists($seederPath)) {
                File::put($seederPath, $this->seederStub($model));
            }

            // ── 5) Controller ─────────────────────────────────────
            $controllerPath = app_path("Http/Controllers/{$model}Controller.php");
            if (! File::exists($controllerPath)) {
                File::put($controllerPath, $this->controllerStub($model, $table));
            }

            // ── 6) Update payload ─────────────────────────────────
            $this->featureUpdate->update([
                'payload' => [
                    'model'           => $model,
                    'migration_file'  => $migFile,
                    'seeder_class'    => "{$model}Seeder",
                    'factory_class'   => "{$model}Factory",
                    'factory_count'   => 50,
                    'files_generated' => true,
                ],
            ]);

            Log::info("ScaffoldFeatureJob: Successfully scaffolded '{$model}' for feature '{$this->featureUpdate->name}'.");

        } catch (\Throwable $e) {
            Log::error("ScaffoldFeatureJob failed for feature [{$this->featureUpdate->id}]: {$e->getMessage()}");

            $this->featureUpdate->update(['status' => 'scaffold_failed']);

            throw $e;
        }
    }

    /* ── Stub generators ──────────────────────────────────────── */

    private function migrationStub(string $table): string
    {
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
            \$t->string('title');
            \$t->text('description')->nullable();
            \$t->foreignId('user_id')->constrained()->cascadeOnDelete();
            \$t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    private function modelStub(string $model): string
    {
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$model} extends Model
{
    use HasFactory;

    protected \$fillable = ['title', 'description', 'user_id'];
}
PHP;
    }

    private function factoryStub(string $model): string
    {
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
            'title'       => \$this->faker->sentence(4),
            'description' => \$this->faker->paragraph(2),
            'user_id'     => 1,
        ];
    }
}
PHP;
    }

    private function seederStub(string $model): string
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
        {$model}::factory()->count(50)->create();
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
}
