<?php

namespace App\Support;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Central audit logger.
 *
 * Used from controllers and middleware to record who-did-what. Writes never
 * throw — a logging failure must not break the user-facing request.
 */
class ActivityLogger
{
    /**
     * Convenience: log an event attributed to the built-in System user.
     * Use from scheduled commands, queue jobs, or any other background work.
     */
    public static function system(string $action, ?string $description = null, array $context = []): void
    {
        self::log(
            action: $action,
            user: User::system(),
            description: $description,
            context: $context,
        );
    }

    public static function log(
        string $action,
        ?User $user = null,
        ?string $description = null,
        array $context = [],
        ?string $email = null,
    ): void {
        try {
            $request = request();

            UserActivity::query()->create([
                'user_id' => $user?->id,
                'email' => $email ?? $user?->email,
                'action' => $action,
                'description' => $description,
                'context' => $context ?: null,
                'ip_address' => $request instanceof Request ? $request->ip() : null,
                'user_agent' => $request instanceof Request
                    ? mb_substr((string) $request->userAgent(), 0, 191)
                    : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to record user activity', [
                'action' => $action,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
