<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\FeatureUpdate;
use App\Services\FeaturePackageInspector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FeatureUpdateAdminTest extends TestCase
{
    use RefreshDatabase;

    private FeaturePackageInspector $inspector;

    /**
     * @var array<int, string>
     */
    private array $createdArtifactPaths = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->inspector = app(FeaturePackageInspector::class);
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->createdArtifactPaths) as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_admin_package_detail_shows_human_component_labels_and_only_selected_items(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'AdminPackageView',
            migrationFile: '2099_01_01_000101_create_admin_package_views_table',
            selectedKeys: ['model', 'controller'],
            status: 'draft',
        );

        foreach ($this->inspector->inspect($feature)['selected_components'] as $component) {
            $this->createArtifact($component['relative_path']);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.feature-updates.show', $feature, false));

        $response->assertOk();
        $response->assertSeeText('Model');
        $response->assertSeeText('Controller');
        $response->assertDontSeeText('Factory');
        $response->assertDontSeeText('Seeder');
        $response->assertDontSeeText('Form Request');
        $response->assertSeeText('2/2 selected files present on disk');
    }

    public function test_publish_is_blocked_when_selected_artifacts_are_missing(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'AdminPublishBlock',
            migrationFile: '2099_01_01_000102_create_admin_publish_blocks_table',
            selectedKeys: ['model', 'controller'],
            status: 'draft',
        );

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.feature-updates.show', $feature, false))
            ->post(route('admin.feature-updates.publish', $feature, false));

        $response->assertRedirect(route('admin.feature-updates.show', $feature, false));
        $response->assertSessionHas('error');

        $feature->refresh();

        $this->assertSame('draft', $feature->status);
        $this->assertNull($feature->published_at);
    }

    private function createAdmin(): AdminUser
    {
        return AdminUser::create([
            'name' => 'Central Admin',
            'email' => 'admin+'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<int, string>  $selectedKeys
     */
    private function createFeatureUpdate(
        string $model,
        string $migrationFile,
        array $selectedKeys,
        string $status,
    ): FeatureUpdate {
        $payload = [
            'model' => $model,
            'migration_file' => $migrationFile,
            'scaffold' => $this->selectedKeys($selectedKeys),
        ];

        $payload['artifact_manifest'] = $this->inspector->buildArtifactManifest($payload);

        return FeatureUpdate::create([
            'name' => $model,
            'slug' => strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $model)),
            'version' => '1.0.0',
            'description' => 'Test package',
            'type' => 'feature',
            'status' => $status,
            'payload' => $payload,
        ]);
    }

    /**
     * @param  array<int, string>  $keys
     * @return array<string, string>
     */
    private function selectedKeys(array $keys): array
    {
        $selected = [];

        foreach ($keys as $key) {
            $selected[$key] = '1';
        }

        return $selected;
    }

    private function createArtifact(?string $relativePath): void
    {
        $this->assertNotNull($relativePath);

        $absolutePath = base_path($relativePath);

        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, "<?php\n// admin feature test artifact\n");

        $this->createdArtifactPaths[] = $absolutePath;
    }
}
