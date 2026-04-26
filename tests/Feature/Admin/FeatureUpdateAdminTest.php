<?php

namespace Tests\Feature\Admin;

use App\Models\AdminAuditLog;
use App\Models\AdminUser;
use App\Models\FeatureUpdate;
use App\Models\Tenant;
use App\Models\TenantFeatureInstallation;
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

    public function test_superadmin_can_see_delete_controls_on_feature_package_detail_page(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'DeleteVisibility',
            migrationFile: '2099_01_01_000103_create_delete_visibilities_table',
            selectedKeys: ['model'],
            status: 'draft',
        );

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.feature-updates.show', $feature, false));

        $response->assertOk();
        $response->assertSeeText('Danger Zone');
        $response->assertSeeText('Delete this feature package');
        $response->assertSeeText('Delete Feature Package');
    }

    public function test_non_superadmin_cannot_see_delete_controls_or_delete_feature_packages(): void
    {
        $manager = $this->createAdmin(role: 'manager');
        $feature = $this->createFeatureUpdate(
            model: 'DeleteRestricted',
            migrationFile: '2099_01_01_000104_create_delete_restricteds_table',
            selectedKeys: ['model'],
            status: 'draft',
        );

        $showResponse = $this->actingAs($manager, 'admin')
            ->get(route('admin.feature-updates.show', $feature, false));

        $showResponse->assertOk();
        $showResponse->assertDontSeeText('Delete this feature package');
        $showResponse->assertDontSeeText('Delete Feature Package');

        $destroyResponse = $this->actingAs($manager, 'admin')
            ->delete(route('admin.feature-updates.destroy', $feature, false));

        $destroyResponse->assertForbidden();
        $this->assertDatabaseHas('feature_updates', ['id' => $feature->id], 'central');
    }

    public function test_deleting_a_feature_package_removes_the_row_deletes_existing_artifacts_and_logs_the_action(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'DeleteDraftPackage',
            migrationFile: '2099_01_01_000105_create_delete_draft_packages_table',
            selectedKeys: ['model', 'controller'],
            status: 'draft',
        );

        $selectedComponents = $this->inspector->inspect($feature)['selected_components'];
        $this->createArtifact($selectedComponents[0]['relative_path']);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.feature-updates.destroy', $feature, false));

        $response->assertRedirect(route('admin.feature-updates.index', [], false));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('feature_updates', ['id' => $feature->id], 'central');
        $this->assertFileDoesNotExist(base_path($selectedComponents[0]['relative_path']));
        $this->assertDatabaseHas('admin_audit_logs', [
            'resource_type' => 'feature_update',
            'resource_id' => (string) $feature->id,
            'action' => 'delete',
        ], 'central');

        $auditLog = AdminAuditLog::on('central')
            ->where('resource_type', 'feature_update')
            ->where('resource_id', (string) $feature->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertSame(
            [$selectedComponents[0]['relative_path']],
            $auditLog->changes['new']['cleanup']['deleted_files']
        );
    }

    public function test_deleting_a_published_feature_package_cascades_related_installations(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'DeletePublishedPackage',
            migrationFile: '2099_01_01_000106_create_delete_published_packages_table',
            selectedKeys: ['model'],
            status: 'published',
        );

        $tenant = $this->createTenant();

        TenantFeatureInstallation::create([
            'tenant_id' => $tenant->id,
            'feature_update_id' => $feature->id,
            'status' => 'installed',
            'installed_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.feature-updates.destroy', $feature, false));

        $response->assertRedirect(route('admin.feature-updates.index', [], false));
        $this->assertDatabaseMissing('feature_updates', ['id' => $feature->id], 'central');
        $this->assertDatabaseMissing('tenant_feature_installations', ['feature_update_id' => $feature->id], 'central');
    }

    public function test_deleting_is_blocked_when_a_tenant_installation_is_in_progress(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'DeleteBlockedInstalling',
            migrationFile: '2099_01_01_000107_create_delete_blocked_installings_table',
            selectedKeys: ['model'],
            status: 'published',
        );

        $tenant = $this->createTenant();

        TenantFeatureInstallation::create([
            'tenant_id' => $tenant->id,
            'feature_update_id' => $feature->id,
            'status' => 'installing',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.feature-updates.show', $feature, false))
            ->delete(route('admin.feature-updates.destroy', $feature, false));

        $response->assertRedirect(route('admin.feature-updates.show', $feature, false));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('feature_updates', ['id' => $feature->id], 'central');
    }

    public function test_deleting_is_blocked_when_a_manifest_path_is_outside_managed_roots(): void
    {
        $admin = $this->createAdmin();
        $feature = $this->createFeatureUpdate(
            model: 'DeleteInvalidManifest',
            migrationFile: '2099_01_01_000108_create_delete_invalid_manifests_table',
            selectedKeys: ['model'],
            status: 'draft',
            payloadMutator: function (array $payload): array {
                $payload['artifact_manifest']['components']['model']['relative_path'] = '../routes/web.php';

                return $payload;
            },
        );

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.feature-updates.show', $feature, false))
            ->delete(route('admin.feature-updates.destroy', $feature, false));

        $response->assertRedirect(route('admin.feature-updates.show', $feature, false));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('feature_updates', ['id' => $feature->id], 'central');
    }

    private function createAdmin(string $role = 'superadmin'): AdminUser
    {
        return AdminUser::create([
            'name' => 'Central Admin',
            'email' => 'admin+'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => $role,
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
        ?callable $payloadMutator = null,
    ): FeatureUpdate {
        $payload = [
            'model' => $model,
            'migration_file' => $migrationFile,
            'scaffold' => $this->selectedKeys($selectedKeys),
        ];

        $payload['artifact_manifest'] = $this->inspector->buildArtifactManifest($payload);

        if ($payloadMutator) {
            $payload = $payloadMutator($payload);
        }

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

    private function createTenant(): Tenant
    {
        return Tenant::create([
            'id' => 'tenant-'.uniqid(),
            'name' => 'Tracked Church',
            'email' => 'tenant+'.uniqid().'@example.com',
            'subdomain' => 'tracked-'.uniqid(),
            'db_name' => 'tenant_'.uniqid(),
            'status' => 'active',
            'subscription_plan' => 'basic',
        ]);
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
