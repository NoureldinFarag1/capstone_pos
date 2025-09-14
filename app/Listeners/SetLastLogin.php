<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use App\Models\User as AppUser;

class SetLastLogin
{
    public function handle(Login $event): void
    {
        try {
            $authUser = $event->user;
            if (!$authUser) {
                return;
            }

            $userId = method_exists($authUser, 'getAuthIdentifier') ? $authUser->getAuthIdentifier() : ($authUser->id ?? null);
            if (!$userId) {
                return;
            }

            // Update last_login on the concrete User model without calling save() on the contract instance
            AppUser::whereKey($userId)->update(['last_login' => now()]);
        } catch (\Throwable $e) {
            Log::error('Failed to set last_login: ' . $e->getMessage());
        }
    }
}
