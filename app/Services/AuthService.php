<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function getRoleRedirect(User $user): string
    {
        // 1. Church must be active (tenant safety check)
        if (!$user->church || !$user->church->isActive()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'This church account has been deactivated.',
            ]);
        }

        // 2. Route by role
        return match($user->role) {
            'pastor'   => route('tenant.dashboard'),
            'leader'   => route('tenant.dashboard'),
            'disciple' => route('tenant.dashboard'),
            default    => route('tenant.dashboard'),
        };
    }
}