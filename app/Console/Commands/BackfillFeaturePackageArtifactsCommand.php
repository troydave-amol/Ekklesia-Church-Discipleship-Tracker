<?php

namespace App\Console\Commands;

use App\Models\FeatureUpdate;
use App\Services\FeaturePackageInspector;
use Illuminate\Console\Command;

class BackfillFeaturePackageArtifactsCommand extends Command
{
    protected $signature = 'feature-updates:backfill-artifact-manifests {--force : Overwrite existing artifact manifests}';

    protected $description = 'Populate payload.artifact_manifest for existing feature packages.';

    public function handle(FeaturePackageInspector $inspector): int
    {
        $force = (bool) $this->option('force');
        $updatedCount = 0;
        $skippedCount = 0;

        FeatureUpdate::on('central')
            ->orderBy('id')
            ->chunkById(100, function ($featureUpdates) use ($inspector, $force, &$updatedCount, &$skippedCount): void {
                foreach ($featureUpdates as $featureUpdate) {
                    $payload = $featureUpdate->payload ?? [];

                    if (!$force && !empty($payload['artifact_manifest'])) {
                        $skippedCount++;
                        continue;
                    }

                    $payload['artifact_manifest'] = $inspector->buildArtifactManifest($payload);
                    $featureUpdate->update(['payload' => $payload]);
                    $updatedCount++;
                }
            });

        $this->info("Backfill complete. Updated {$updatedCount} feature package(s), skipped {$skippedCount}.");

        return self::SUCCESS;
    }
}
