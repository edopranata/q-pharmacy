<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AuditAuthListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle login events
     */
    public function handleLogin(Login $event): void
    {
        AuditLogger::logAuth(
            'login',
            $event->user,
            "User {$event->user->name} logged in successfully"
        );
    }

    /**
     * Handle logout events
     */
    public function handleLogout(Logout $event): void
    {
        AuditLogger::logAuth(
            'logout',
            $event->user,
            "User {$event->user->name} logged out"
        );
    }

    /**
     * Handle failed login attempts
     */
    public function handleFailed(Failed $event): void
    {
        AuditLogger::logAuth(
            'login_failed',
            null,
            'Failed login attempt for: ' . ($event->credentials['email'] ?? 'unknown')
        );
    }

    /**
     * Handle login attempts
     */
    public function handleAttempting(Attempting $event): void
    {
        // Only log if we want to track all attempts (can be noisy)
        // AuditLogger::logAuth(
        //     'login_attempt',
        //     null,
        //     "Login attempt for: {$event->credentials['email'] ?? 'unknown'}"
        // );
    }

    /**
     * Handle user registration
     */
    public function handleRegistered(Registered $event): void
    {
        AuditLogger::logAuth(
            'registered',
            $event->user,
            "New user registered: {$event->user->name}"
        );
    }

    /**
     * Handle email verification
     */
    public function handleVerified(Verified $event): void
    {
        AuditLogger::logAuth(
            'email_verified',
            $event->user,
            "User {$event->user->name} verified their email"
        );
    }

    /**
     * Handle password reset
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        AuditLogger::logAuth(
            'password_reset',
            $event->user,
            "User {$event->user->name} reset their password"
        );
    }

    /**
     * Handle the event (generic handler)
     */
    public function handle(object $event): void
    {
        match (get_class($event)) {
            Login::class => $this->handleLogin($event),
            Logout::class => $this->handleLogout($event),
            Failed::class => $this->handleFailed($event),
            Attempting::class => $this->handleAttempting($event),
            Registered::class => $this->handleRegistered($event),
            Verified::class => $this->handleVerified($event),
            PasswordReset::class => $this->handlePasswordReset($event),
            default => null,
        };
    }
}
