<?php

namespace App\Services;

use App\Models\FeatureUpdate;

class FeaturePackageInspector
{
    /**
     * Ordered component registry used across admin and tenant UIs.
     *
     * @var array<string, string>
     */
    private const COMPONENT_LABELS = [
        'model' => 'Model',
        'factory' => 'Factory',
        'controller' => 'Controller',
        'migration' => 'Migration',
        'seeder' => 'Seeder',
        'request' => 'Form Request',
    ];

    /**
     * Inspect a feature package and compute live artifact completeness from disk.
     *
     * @return array{
     *     components: array<int, array{
     *         key: string,
     *         label: string,
     *         relative_path: ?string,
     *         absolute_path: ?string,
     *         selected: bool,
     *         exists: bool,
     *         missing_reason: ?string
     *     }>,
     *     selected_components: array<int, array{
     *         key: string,
     *         label: string,
     *         relative_path: ?string,
     *         absolute_path: ?string,
     *         selected: bool,
     *         exists: bool,
     *         missing_reason: ?string
     *     }>,
     *     missing_components: array<int, array{
     *         key: string,
     *         label: string,
     *         relative_path: ?string,
     *         absolute_path: ?string,
     *         selected: bool,
     *         exists: bool,
     *         missing_reason: ?string
     *     }>,
     *     selected_count: int,
     *     existing_count: int,
     *     missing_count: int,
     *     missing_labels: array<int, string>,
     *     is_complete: bool,
     *     has_manifest: bool
     * }
     */
    public function inspect(FeatureUpdate $featureUpdate): array
    {
        $payload = $featureUpdate->payload ?? [];
        $manifest = $payload['artifact_manifest']['components'] ?? [];
        $hasManifest = !empty($manifest);

        $components = [];

        foreach (self::COMPONENT_LABELS as $key => $label) {
            $manifestComponent = $manifest[$key] ?? [];
            $selected = $this->componentIsSelected($key, $payload, $manifestComponent, $hasManifest);
            $relativePath = $this->resolveRelativePath($key, $payload, $manifestComponent);
            $absolutePath = $relativePath ? base_path($relativePath) : null;
            $exists = $selected && $absolutePath ? is_file($absolutePath) : false;
            $missingReason = $this->resolveMissingReason(
                $featureUpdate->status,
                $selected,
                $relativePath,
                $exists,
            );

            $components[] = [
                'key' => $key,
                'label' => $label,
                'relative_path' => $relativePath,
                'absolute_path' => $absolutePath,
                'selected' => $selected,
                'exists' => $exists,
                'missing_reason' => $missingReason,
            ];
        }

        $selectedComponents = array_values(array_filter(
            $components,
            fn (array $component): bool => $component['selected']
        ));

        $missingComponents = array_values(array_filter(
            $selectedComponents,
            fn (array $component): bool => !$component['exists']
        ));

        return [
            'components' => $components,
            'selected_components' => $selectedComponents,
            'missing_components' => $missingComponents,
            'selected_count' => count($selectedComponents),
            'existing_count' => count(array_filter(
                $selectedComponents,
                fn (array $component): bool => $component['exists']
            )),
            'missing_count' => count($missingComponents),
            'missing_labels' => array_values(array_map(
                fn (array $component): string => $component['label'],
                $missingComponents
            )),
            'is_complete' => count($missingComponents) === 0,
            'has_manifest' => $hasManifest,
        ];
    }

    /**
     * Build static artifact metadata for persistence in payload.artifact_manifest.
     *
     * @param  array<string, mixed>  $payload
     * @return array{version:int,components:array<string,array{selected:bool,relative_path:?string,label:string}>}
     */
    public function buildArtifactManifest(array $payload): array
    {
        $components = [];

        foreach (self::COMPONENT_LABELS as $key => $label) {
            $components[$key] = [
                'selected' => $this->componentIsSelected($key, $payload, [], false),
                'relative_path' => $this->resolveRelativePath($key, $payload, []),
                'label' => $label,
            ];
        }

        return [
            'version' => 1,
            'components' => $components,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function componentLabels(): array
    {
        return self::COMPONENT_LABELS;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $manifestComponent
     */
    private function componentIsSelected(
        string $key,
        array $payload,
        array $manifestComponent,
        bool $hasManifest,
    ): bool {
        if ($hasManifest && array_key_exists('selected', $manifestComponent)) {
            return (bool) $manifestComponent['selected'];
        }

        $scaffold = $payload['scaffold'] ?? null;

        if (!is_array($scaffold) || $scaffold === []) {
            return true;
        }

        return array_key_exists($key, $scaffold);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $manifestComponent
     */
    private function resolveRelativePath(string $key, array $payload, array $manifestComponent): ?string
    {
        if (!empty($manifestComponent['relative_path']) && is_string($manifestComponent['relative_path'])) {
            return $manifestComponent['relative_path'];
        }

        $model = $payload['model'] ?? null;
        $migrationFile = $payload['migration_file'] ?? null;

        return match ($key) {
            'model' => is_string($model) && $model !== '' ? "app/Models/{$model}.php" : null,
            'factory' => is_string($model) && $model !== '' ? "database/factories/{$model}Factory.php" : null,
            'controller' => is_string($model) && $model !== '' ? "app/Http/Controllers/{$model}Controller.php" : null,
            'migration' => is_string($migrationFile) && $migrationFile !== '' ? "database/migrations/tenant/{$migrationFile}.php" : null,
            'seeder' => is_string($model) && $model !== '' ? "database/seeders/{$model}Seeder.php" : null,
            'request' => is_string($model) && $model !== '' ? "app/Http/Requests/{$model}Request.php" : null,
            default => null,
        };
    }

    private function resolveMissingReason(
        string $status,
        bool $selected,
        ?string $relativePath,
        bool $exists,
    ): ?string {
        if (!$selected || $exists) {
            return null;
        }

        if ($status === 'scaffold_failed') {
            return 'scaffold_failed';
        }

        return $relativePath ? 'missing_file' : 'missing_metadata';
    }
}
