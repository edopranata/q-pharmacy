<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'app.management.users.index',
            'app.management.users.store',
            'app.management.users.show',
            'app.management.users.update',
            'app.management.users.destroy',
            'app.management.users.assign-roles',
            
            // Role Management
            'app.management.roles.index',
            'app.management.roles.store',
            'app.management.roles.show',
            'app.management.roles.update',
            'app.management.roles.destroy',
            'app.management.roles.assign-permissions',
            
            // Audit Logs
            'app.audit-logs.index',

            // Master Data
            'app.master.categories.index',
            'app.master.categories.create',
            'app.master.categories.edit',
            'app.master.categories.delete',

            'app.master.suppliers.index',
            'app.master.suppliers.create',
            'app.master.suppliers.edit',
            'app.master.suppliers.delete',

            'app.master.units.index',
            'app.master.units.create',
            'app.master.units.edit',
            'app.master.units.delete',

            // Product Management
            'app.products.index',
            'app.products.create',
            'app.products.edit',
            'app.products.delete',
            'app.products.show',
            'app.products.pricing.index',

            // Inventory Management
            'app.inventories.index',
            'app.inventories.stock-in.index',
            'app.inventories.stock-in.create',
            'app.inventories.stock-out.index',
            'app.inventories.adjustments.index',

            // Sales & POS
            'app.sells.index',
            'app.sells.pos',
            'app.sells.transactions.index',
            'app.sells.transactions.show',

            // Reports
            'app.reports.index',
            'app.reports.sales',
            'app.reports.inventory',
            'app.reports.financial',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin Role - Full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Kasir Role - Limited access
        $kasirRole = Role::create(['name' => 'kasir']);
        $kasirPermissions = [
            'app.products.index',
            'app.products.show',
            'app.inventories.index',
            'app.sells.index',
            'app.sells.pos',
            'app.sells.transactions.index',
            'app.sells.transactions.show',
            'app.master.categories.index',
            'app.master.suppliers.index',
            'app.master.units.index',
        ];
        $kasirRole->givePermissionTo($kasirPermissions);

        // Manager Role - Moderate access
        $managerRole = Role::create(['name' => 'manager']);
        $managerPermissions = [
            'app.management.users.index',
            'app.management.users.store',
            'app.management.users.show',
            'app.management.users.update',
            'app.management.roles.index',
            'app.master.categories.index',
            'app.master.categories.create',
            'app.master.categories.edit',
            'app.master.suppliers.index',
            'app.master.suppliers.create',
            'app.master.suppliers.edit',
            'app.master.units.index',
            'app.master.units.create',
            'app.master.units.edit',
            'app.products.index',
            'app.products.create',
            'app.products.edit',
            'app.products.show',
            'app.products.pricing.index',
            'app.inventories.index',
            'app.inventories.stock-in.index',
            'app.inventories.stock-in.create',
            'app.inventories.stock-out.index',
            'app.inventories.adjustments.index',
            'app.sells.index',
            'app.sells.pos',
            'app.sells.transactions.index',
            'app.sells.transactions.show',
            'app.reports.index',
            'app.reports.sales',
            'app.reports.inventory',
            'app.reports.financial',
        ];
        $managerRole->givePermissionTo($managerPermissions);
    }
}
