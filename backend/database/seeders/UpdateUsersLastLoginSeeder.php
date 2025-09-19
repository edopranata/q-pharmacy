<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateUsersLastLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Set random last_login within the last 30 days
            $randomDays = rand(1, 30);
            $randomHours = rand(0, 23);
            $randomMinutes = rand(0, 59);
            
            $lastLogin = Carbon::now()
                ->subDays($randomDays)
                ->setHour($randomHours)
                ->setMinute($randomMinutes)
                ->setSecond(0);
            
            $user->update([
                'last_login' => $lastLogin
            ]);
        }
        
        $this->command->info('Updated last_login for ' . $users->count() . ' users.');
    }
}
