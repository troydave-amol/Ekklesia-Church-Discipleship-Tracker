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
                  ->orWhere('email', 'like', "%{$request->search}%");
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
            'body' => 'required|string|min:5',
        ]);

        $ticket->replies()->create([
            'body' => $validated['body'],
            'replied_by' => 'admin:' . auth('admin')->id(),
            'created_at' => now(),
        ]);

        // Mark as responded if pending
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'responded']);
        }

        \App\Models\VersionHistory::logChange(
            'support_ticket',
            $ticket->id,
            auth('admin')->id(),
            'updated',
            "Admin replied to ticket: {$ticket->subject}",
            ['status' => 'open'],
            ['status' => 'responded']
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
