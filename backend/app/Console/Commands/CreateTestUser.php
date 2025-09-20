<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-user {--token : Generate API token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for API testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->info("Test user created/found: {$user->email}");

        if ($this->option('token')) {
            // Delete existing tokens
            $user->tokens()->delete();
            
            // Create new token
            $token = $user->createToken('test-token')->plainTextToken;
            
            $this->info("API Token: {$token}");
            $this->info("Use this token in Authorization header: Bearer {$token}");
        }

        return 0;
    }
}
