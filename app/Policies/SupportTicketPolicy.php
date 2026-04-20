<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    /**
     * Determine whether the user can view any support tickets.
     */
    public function viewAny(User $user): bool
    {
        return $user->isChurchAdmin();
    }

    /**
     * Determine whether the user can view the support ticket.
     */
    public function view(User $user, SupportTicket $ticket): bool
    {
        if ($user->isChurchAdmin()) {
            return $ticket->tenant_id === tenant('id');
        }

        return $ticket->user_id === $user->id && $ticket->tenant_id === tenant('id');
    }

    /**
     * Determine whether the user can create support tickets.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create tickets
    }

    /**
     * Determine whether the user can update the support ticket status.
     */
    public function updateStatus(User $user, SupportTicket $ticket): bool
    {
        return $user->isChurchAdmin() && $ticket->tenant_id === tenant('id');
    }

    /**
     * Determine whether the user can delete the support ticket.
     */
    public function delete(User $user, SupportTicket $ticket): bool
    {
        return $user->isChurchAdmin() && $ticket->tenant_id === tenant('id');
    }
}
