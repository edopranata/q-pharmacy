<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestPasswordResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:reset-test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password reset functionality with email notification via MailHog';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@apotek.com';
        
        try {
            // Find user by email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email '{$email}' not found!");
                return 1;
            }
            
            // Generate new password
            $newPassword = Str::random(12);
            
            // Update user password
            $user->update([
                'password' => Hash::make($newPassword),
            ]);
            
            // Send email notification
            $user->notify(new PasswordResetNotification($newPassword));
            
            $this->info("Password reset successful for user: {$user->name} ({$user->email})");
            $this->info("New password: {$newPassword}");
            $this->info("Email notification sent via MailHog");
            $this->info("Check MailHog at: http://localhost:8025");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to reset password: " . $e->getMessage());
            return 1;
        }
    }
}
