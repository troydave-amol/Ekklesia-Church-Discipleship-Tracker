<?php

namespace Tests\Unit;

use App\Models\FeatureUpdate;
use App\Services\FeaturePackageInspector;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FeaturePackageInspectorTest extends TestCase
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

    public function test_it_marks_a_package_complete_when_all_selected_components_exist(): void
    {
        $feature = $this->makeFeature([
            'model' => 'InspectorAllPresent',
            'migration_file' => '2099_01_01_000001_create_inspector_all_presents_table',
            'scaffold' => $this->selectedKeys(['model', 'factory', 'controller', 'migration', 'seeder', 'request']),
        ], 'published');

        foreach ($this->inspector->inspect($feature)['selected_components'] as $component) {
            $this->createArtifact($component['relative_path']);
        }

        $inspection = $this->inspector->inspect($feature);

        $this->assertTrue($inspection['is_complete']);
        $this->assertSame(6, $inspection['selected_count']);
        $this->assertSame(6, $inspection['existing_count']);
        $this->assertSame([], $inspection['missing_labels']);
    }

    public function test_it_only_returns_selected_components_for_partial_scaffolds(): void
    {
        $feature = $this->makeFeature([
            'model' => 'InspectorPartial',
            'migration_file' => '2099_01_01_000002_create_inspector_partials_table',
            'scaffold' => $this->selectedKeys(['model', 'controller']),
        ]);

        $inspection = $this->inspector->inspect($feature);
        $labels = array_column($inspection['selected_components'], 'label');

        $this->assertSame(['Model', 'Controller'], $labels);
        $this->assertSame(2, $inspection['selected_count']);
    }

    public function test_it_marks_selected_components_as_missing_when_files_are_absent(): void
    {
        $feature = $this->makeFeature([
            'model' => 'InspectorMissingFile',
            'migration_file' => '2099_01_01_000003_create_inspector_missing_files_table',
            'scaffold' => $this->selectedKeys(['model']),
        ]);

        $inspection = $this->inspector->inspect($feature);

        $this->assertFalse($inspection['is_complete']);
        $this->assertSame(['Model'], $inspection['missing_labels']);
        $this->assertSame('missing_file', $inspection['missing_components'][0]['missing_reason']);
    }

    public function test_it_marks_selected_components_as_missing_metadata_when_paths_cannot_be_resolved(): void
    {
        $feature = $this->makeFeature([
            'scaffold' => $this->selectedKeys(['migration']),
        ]);

        $inspection = $this->inspector->inspect($feature);

        $this->assertFalse($inspection['is_complete']);
        $this->assertSame(['Migration'], $inspection['missing_labels']);
        $this->assertSame('missing_metadata', $inspection['missing_components'][0]['missing_reason']);
    }

    public function test_it_defaults_legacy_packages_to_all_components_when_no_manifest_or_scaffold_exists(): void
    {
        $feature = new FeatureUpdate([
            'name' => 'Legacy Package',
            'slug' => 'legacy-package',
            'version' => '1.0.0',
            'status' => 'draft',
            'payload' => [
                'model' => 'LegacyPackage',
                'migration_file' => '2099_01_01_000004_create_legacy_packages_table',
            ],
        ]);

        $inspection = $this->inspector->inspect($feature);

        $this->assertFalse($inspection['has_manifest']);
        $this->assertSame(6, $inspection['selected_count']);
    }

    public function test_it_detects_deleted_files_even_when_a_manifest_exists(): void
    {
        $feature = $this->makeFeature([
            'model' => 'InspectorDeletedFile',
            'migration_file' => '2099_01_01_000005_create_inspector_deleted_files_table',
            'scaffold' => $this->selectedKeys(['model', 'controller']),
        ], 'published');

        $inspection = $this->inspector->inspect($feature);
        $this->createArtifact($inspection['selected_components'][0]['relative_path']);
        $this->createArtifact($inspection['selected_components'][1]['relative_path']);

        @unlink(base_path($inspection['selected_components'][0]['relative_path']));

        $inspection = $this->inspector->inspect($feature);

        $this->assertFalse($inspection['is_complete']);
        $this->assertSame(['Model'], $inspection['missing_labels']);
        $this->assertTrue($inspection['has_manifest']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function makeFeature(array $payload, string $status = 'draft'): FeatureUpdate
    {
        $payload['artifact_manifest'] = $this->inspector->buildArtifactManifest($payload);

        return new FeatureUpdate([
            'name' => 'Inspector Package',
            'slug' => 'inspector-package',
            'version' => '1.0.0',
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
        File::put($absolutePath, "<?php\n// test artifact\n");

        $this->createdArtifactPaths[] = $absolutePath;
    }
}
