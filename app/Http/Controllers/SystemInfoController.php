<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SystemInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:church_admin');
    }

    public function index()
    {
        $version = config('app.version');
        $latestRelease = $this->getLatestReleaseDate();
        $changelogSections = $this->parseChangelog(3);

        return view('system.info', compact('version', 'latestRelease', 'changelogSections'));
    }

    public function migrations()
    {
        $output = '';
        Artisan::call('tenancy:migration-status', [], $output);

        $rows = $this->parseMigrationOutput($output);
        $pendingCount = collect($rows)->where('status', 'Pending')->count();

        return view('system.migrations', compact('rows', 'pendingCount', 'output'));
    }

    public function changelog()
    {
        $changelogSections = $this->parseChangelog(null);

        return view('system.changelog', compact('changelogSections'));
    }

    private function getLatestReleaseDate()
    {
        $path = base_path('CHANGELOG.md');
        
        if (!File::exists($path)) {
            return null;
        }

        $contents = File::get($path);
        
        // Find first version line (e.g., ## [0.1.0] - 2026-01-15)
        if (preg_match('/##\s+\[[^\]]+\]\s+-\s+(\d{4}-\d{2}-\d{2})/', $contents, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function parseChangelog($limit = null)
    {
        $path = base_path('CHANGELOG.md');
        
        if (!File::exists($path)) {
            return [];
        }

        $contents = File::get($path);
        $sections = [];
        $count = 0;

        // Split by version headers
        $versions = preg_split('/^##\s+/m', $contents);
        array_shift($versions); // Remove content before first version

        foreach ($versions as $versionContent) {
            if ($limit && $count >= $limit) {
                break;
            }

            // Extract version and date
            if (preg_match('/\[([^\]]+)\]\s+-\s+(\d{4}-\d{2}-\d{2})|(\[Unreleased\])/', $versionContent, $versionMatch)) {
                $version = $versionMatch[1] ?? 'Unreleased';
                $date = $versionMatch[2] ?? null;
                
                // Extract sections
                $entries = [
                    'Added' => [],
                    'Changed' => [],
                    'Fixed' => [],
                    'Removed' => [],
                ];

                foreach ($entries as $section => $placeholder) {
                    $pattern = '/###\s+' . $section . '\s*\n(.*?)(?=###\s+|$)/s';
                    if (preg_match($pattern, $versionContent, $matches)) {
                        $lines = array_filter(
                            array_map('trim', explode("\n", $matches[1])),
                            fn($line) => !empty($line) && $line[0] === '-'
                        );
                        $entries[$section] = array_map(
                            fn($line) => ltrim($line, '- '),
                            $lines
                        );
                    }
                }

                $sections[] = [
                    'version' => $version,
                    'date' => $date,
                    'entries' => array_filter($entries),
                ];

                $count++;
            }
        }

        return $sections;
    }

    private function parseMigrationOutput($output)
    {
        // Parse the table output from tenancy:migration-status command
        $rows = [];
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '+' || $line[0] === '|' && strpos($line, 'Tenant') !== false) {
                continue;
            }

            if ($line[0] === '|') {
                $parts = array_map('trim', explode('|', $line));
                $parts = array_filter($parts); // Remove empty parts
                
                if (count($parts) >= 3) {
                    $rows[] = [
                        'tenant' => $parts[0],
                        'file' => $parts[1],
                        'status' => $parts[2],
                    ];
                }
            }
        }

        return $rows;
    }
}
