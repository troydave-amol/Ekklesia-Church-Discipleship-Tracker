<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role->slug === 'church_admin') {
            $tickets = SupportTicket::where('tenant_id', tenant('id'))
                ->with('user')
                ->latest()
                ->paginate(20);
        } else {
            $tickets = SupportTicket::where('tenant_id', tenant('id'))
                ->where('user_id', $user->id)
                ->with('user')
                ->latest()
                ->paginate(20);
        }
        
        return view('support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string|max:5000',
        ]);

        $ticket = SupportTicket::create([
            'tenant_id' => tenant('id'),
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'message' => $validated['message'],
        ]);

        return redirect()->route('support.show', $ticket)->with('success', 'Support ticket created successfully.');
    }

    public function show(SupportTicket $ticket)
    {
        $this->authorize('view', $ticket);
        
        $ticket->load('user', 'replies.user');
        
        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $this->authorize('view', $ticket);

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $this->authorize('updateStatus', $ticket);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'status' => $ticket->status]);
    }

    public function adminIndex()
    {
        $this->authorize('viewAny', SupportTicket::class);

        $status = request('status');
        $priority = request('priority');

        $query = SupportTicket::where('tenant_id', tenant('id'))->with('user');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority && $priority !== 'all') {
            $query->where('priority', $priority);
        }

        $tickets = $query->latest()->paginate(20);

        return view('support.admin', compact('tickets', 'status', 'priority'));
    }
}
