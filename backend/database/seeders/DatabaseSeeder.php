<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Check if we're in production to prevent accidental seeding
        if (app()->environment('production')) {
            $this->command->warn('Seeding in production environment!');
            if (! $this->command->confirm('Are you sure you want to continue?')) {
                $this->command->info('Seeding cancelled.');

                return;
            }
        }

        $this->command->info('Starting database seeding...');

        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);
        $this->command->info('✓ Roles and permissions seeded');

        // Seed master data
        $this->call(MasterDataSeeder::class);
        $this->command->info('✓ Master data seeded');

        // Seed users with roles
        $this->call(UserRoleSeeder::class);
        $this->command->info('✓ Users with roles seeded');

        $this->command->info('Database seeding completed successfully!');
        $this->command->line('');
        $this->command->info('Configuration options (set in .env):');
        $this->command->line('SEED_CATEGORY_COUNT='.env('SEED_CATEGORY_COUNT', 10));
        $this->command->line('SEED_SUPPLIER_COUNT='.env('SEED_SUPPLIER_COUNT', 8));
        $this->command->line('SEED_UNIT_COUNT='.env('SEED_UNIT_COUNT', 10));
    }
}
