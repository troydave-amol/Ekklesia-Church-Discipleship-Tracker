<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastAnnouncementJob;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminChangelogController extends Controller
{
    /**
     * Display parsed changelog with broadcast controls.
     */
    public function index(): View
    {
        $sections = $this->parseChangelog();

        // Check which versions have already been broadcast
        $broadcastedTitles = Announcement::where('title', 'like', 'Release v%')
            ->whereNotNull('published_at')
            ->pluck('title')
            ->map(fn($t) => strtolower($t))
            ->toArray();

        return view('admin.changelog.index', compact('sections', 'broadcastedTitles'));
    }

    /**
     * Broadcast a specific changelog version to all tenants as an Announcement.
     */
    public function broadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'notes'   => 'required|string',
        ]);

        $title = "Release v{$validated['version']}";

        // Prevent duplicate broadcasts
        $existing = Announcement::where('title', $title)
            ->whereNotNull('published_at')
            ->first();

        if ($existing) {
            return back()->with('error', "Version {$validated['version']} has already been broadcast.");
        }

        $announcement = Announcement::create([
            'title'        => $title,
            'body'         => $validated['notes'],
            'audience'     => 'tenants',
            'created_by'   => auth('admin')->id(),
            'published_at' => now(),
        ]);

        BroadcastAnnouncementJob::dispatch($announcement->id);

        \App\Models\VersionHistory::logChange(
            'announcement',
            $announcement->id,
            auth('admin')->id(),
            'created',
            "Broadcast changelog release: {$title}"
        );

        return back()->with('success', "Version {$validated['version']} broadcast to all tenants.");
    }

    /**
     * Parse the CHANGELOG.md file into structured sections.
     * Reuses the same logic from SystemInfoController.
     */
    private function parseChangelog(): array
    {
        $path = base_path('CHANGELOG.md');

        if (!File::exists($path)) {
            return [];
        }

        $contents = File::get($path);
        $sections = [];

        // Split by version headers
        $versions = preg_split('/^##\s+/m', $contents);
        array_shift($versions); // Remove content before first version

        foreach ($versions as $versionContent) {
            // Extract version and date
            if (preg_match('/\[([^\]]+)\]\s+-\s+(\d{4}-\d{2}-\d{2})|\[(Unreleased)\]/', $versionContent, $versionMatch)) {
                $version = $versionMatch[1] ?: ($versionMatch[3] ?? 'Unreleased');
                $date = $versionMatch[2] ?? null;

                // Extract sections (Added, Changed, Fixed, Removed)
                $entries = [
                    'Added'   => [],
                    'Changed' => [],
                    'Fixed'   => [],
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
                    'date'    => $date,
                    'entries' => array_filter($entries),
                ];
            }
        }

        return $sections;
    }
}
