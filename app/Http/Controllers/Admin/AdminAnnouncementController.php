<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastAnnouncementJob;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminAnnouncementController extends Controller
{
    /**
     * List all announcements
     */
    public function index(): View
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::latest()->paginate(20),
        ]);
    }

    /**
     * Show create announcement form
     */
    public function create(): View
    {
        return view('admin.announcements.create');
    }

    /**
     * Store new announcement
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
            'audience' => 'required|in:all,admins,tenants',
            'publish_now' => 'boolean',
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'audience' => $validated['audience'],
            'created_by' => auth('admin')->id(),
            'published_at' => $validated['publish_now'] ? now() : null,
        ]);

        \App\Models\AdminAuditLog::logChange(
            'announcement',
            $announcement->id,
            auth('admin')->id(),
            'created',
            "Created announcement: {$announcement->title} (audience: {$validated['audience']})",
            null,
            $announcement->toArray()
        );

        // Dispatch broadcast if publishing immediately
        if (!empty($validated['publish_now'])) {
            BroadcastAnnouncementJob::dispatch($announcement->id);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created' . (!empty($validated['publish_now']) ? ' and published.' : '.'));
    }

    /**
     * Show edit form
     */
    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update announcement
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
            'audience' => 'required|in:all,admins,tenants',
        ]);

        $oldValues = $announcement->toArray();
        $announcement->update($validated);

        \App\Models\AdminAuditLog::logChange(
            'announcement',
            $announcement->id,
            auth('admin')->id(),
            'updated',
            "Updated announcement: {$announcement->title}",
            $oldValues,
            $announcement->toArray()
        );

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated.');
    }

    /**
     * Publish announcement
     */
    public function publish(Announcement $announcement): RedirectResponse
    {
        $announcement->update(['published_at' => now()]);

        // Dispatch broadcast to all tenants
        BroadcastAnnouncementJob::dispatch($announcement->id);

        \App\Models\AdminAuditLog::logChange(
            'announcement',
            $announcement->id,
            auth('admin')->id(),
            'updated',
            "Published announcement: {$announcement->title}"
        );

        return back()->with('success', 'Announcement published.');
    }

    /**
     * Delete announcement
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $title = $announcement->title;
        $announcement->delete();

        \App\Models\AdminAuditLog::logChange(
            'announcement',
            $announcement->id,
            auth('admin')->id(),
            'deleted',
            "Deleted announcement: {$title}"
        );

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}
