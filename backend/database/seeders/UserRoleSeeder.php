<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $kasirRole = Role::firstOrCreate(['name' => 'kasir']);

        // Create Admin User
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@apotek.com',
            'email_verified_at' => now(),
            'avatar' => 'https://ui-avatars.com/api/?name=Administrator&background=1976d2&color=fff&size=128',
            'phone' => '+62812345678',
        ]);
        $admin->assignRole($adminRole);

        // Create Manager Users
        $managers = User::factory(2)->create([
            'email_verified_at' => now(),
            'avatar' => 'https://ui-avatars.com/api/?name=Manager&background=4caf50&color=fff&size=128',
            'phone' => '+62812345679',
        ]);
        foreach ($managers as $manager) {
            $manager->assignRole($managerRole);
        }

        // Create Kasir Users
        $kasirs = User::factory(3)->create([
            'email_verified_at' => now(),
            'avatar' => 'https://ui-avatars.com/api/?name=Kasir&background=ff9800&color=fff&size=128',
            'phone' => '+62812345680',
        ]);
        foreach ($kasirs as $kasir) {
            $kasir->assignRole($kasirRole);
        }

        // Create additional users without specific roles (for testing)
        User::factory(5)->create([
            'email_verified_at' => now(),
            'avatar' => 'https://ui-avatars.com/api/?name=User&background=9c27b0&color=fff&size=128',
            'phone' => '+62812345681',
        ]);
    }
}
