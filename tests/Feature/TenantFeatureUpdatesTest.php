<?php

namespace Tests\Feature;

use App\Jobs\InstallFeatureUpdateJob;
use App\Models\FeatureUpdate;
use App\Models\TenantFeatureInstallation;
use App\Services\FeaturePackageInspector;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Tests\TenantTestCase;

class TenantFeatureUpdatesTest extends TenantTestCase
{
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

    public function test_updates_page_highlights_only_included_components_and_flags_incomplete_packages(): void
    {
        $admin = $this->createTenantUser('church_admin');
        $feature = $this->createFeatureUpdate(
            model: 'TenantIncompletePackage',
            migrationFile: '2099_01_01_000201_create_tenant_incomplete_packages_table',
            selectedKeys: ['model', 'controller'],
        );

        $selectedComponents = $this->inspector->inspect($feature)['selected_components'];
        $this->createArtifact($selectedComponents[0]['relative_path']);

        $response = $this->actingAs($admin)
            ->get(route('system.updates', [], false));

        $response->assertOk();
        $response->assertSeeText('Included in this update');
        $response->assertSeeText('Model');
        $response->assertSeeText('Controller');
        $response->assertDontSeeText('Factory');
        $response->assertDontSeeText('Seeder');
        $response->assertSeeText('This package is incomplete.');
        $response->assertDontSee(route('system.updates.install', $feature, false));
    }

    public function test_install_creates_a_missing_installation_record_and_dispatches_the_job_for_complete_packages(): void
    {
        Queue::fake();

        $admin = $this->createTenantUser('church_admin');
        $feature = $this->createFeatureUpdate(
            model: 'TenantInstallReady',
            migrationFile: '2099_01_01_000202_create_tenant_install_readies_table',
            selectedKeys: ['model'],
        );

        foreach ($this->inspector->inspect($feature)['selected_components'] as $component) {
            $this->createArtifact($component['relative_path']);
        }

        $page = $this->actingAs($admin)
            ->get(route('system.updates', [], false));

        $page->assertOk();
        $page->assertDontSeeText('This package is incomplete.');
        $page->assertSee(route('system.updates.install', $feature, false), false);

        $response = $this->actingAs($admin)
            ->from(route('system.updates', [], false))
            ->post(route('system.updates.install', $feature, false));

        $response->assertRedirect(route('system.updates', [], false));
        $response->assertSessionHas('success');

        Queue::assertPushed(InstallFeatureUpdateJob::class, function (InstallFeatureUpdateJob $job) use ($feature) {
            return $job->tenant->id === $this->tenant->id
                && $job->featureUpdate->id === $feature->id;
        });

        $this->assertDatabaseHas('tenant_feature_installations', [
            'tenant_id' => $this->tenant->id,
            'feature_update_id' => $feature->id,
            'status' => 'installing',
        ], 'central');
    }

    public function test_install_job_fails_fast_when_selected_files_are_missing(): void
    {
        $feature = $this->createFeatureUpdate(
            model: 'TenantJobGuard',
            migrationFile: '2099_01_01_000203_create_tenant_job_guards_table',
            selectedKeys: ['model'],
        );

        $installation = TenantFeatureInstallation::create([
            'tenant_id' => $this->tenant->id,
            'feature_update_id' => $feature->id,
            'status' => 'pending',
        ]);

        $job = new InstallFeatureUpdateJob($this->tenant, $feature);
        $job->handle($this->inspector);

        $installation->refresh();

        $this->assertSame('failed', $installation->status);
        $this->assertStringContainsString('Model', $installation->error_log);
    }

    /**
     * @param  array<int, string>  $selectedKeys
     */
    private function createFeatureUpdate(
        string $model,
        string $migrationFile,
        array $selectedKeys,
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
            'description' => 'Tenant update package',
            'type' => 'feature',
            'status' => 'published',
            'payload' => $payload,
            'published_at' => now(),
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
        File::put($absolutePath, "<?php\n// tenant feature test artifact\n");

        $this->createdArtifactPaths[] = $absolutePath;
    }
}
