<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminTicketController extends Controller
{
    /**
     * List all central support tickets from tenants
     */
    public function index(Request $request): View
    {
        $query = SupportTicket::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('author_name', 'like', "%{$request->search}%")
                  ->orWhere('author_email', 'like', "%{$request->search}%");
            });
        }

        return view('admin.tickets.index', [
            'tickets' => $query->latest()->paginate(20),
            'status' => $request->status ?? 'all',
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Show ticket details and replies
     */
    public function show(SupportTicket $ticket): View
    {
        return view('admin.tickets.show', [
            'ticket' => $ticket->load('replies'),
        ]);
    }

    /**
     * Add reply to ticket
     */
    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5',
        ]);

        $ticket->replies()->create([
            'message' => $validated['message'],
            'admin_id' => auth('admin')->id(),
        ]);

        // Mark as in_progress if still open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        \App\Models\VersionHistory::logChange(
            'support_ticket',
            $ticket->id,
            auth('admin')->id(),
            'updated',
            "Admin replied to ticket: {$ticket->subject}",
            ['status' => 'open'],
            ['status' => 'in_progress']
        );

        return back()->with('success', 'Reply sent.');
    }

    /**
     * Close ticket
     */
    public function close(SupportTicket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'closed']);

        \App\Models\VersionHistory::logChange(
            'support_ticket',
            $ticket->id,
            auth('admin')->id(),
            'updated',
            "Closed ticket: {$ticket->subject}"
        );

        return back()->with('success', 'Ticket closed.');
    }
}
