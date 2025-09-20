<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'app.management.roles.index']);
    Permission::create(['name' => 'app.management.users.index']);
    
    // Create role and assign permission
    $role = Role::create(['name' => 'admin']);
    $role->givePermissionTo('app.management.roles.index');
    
    // Create user and assign role
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
});

test('it can get available permissions successfully', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/app/management/roles/available/permissions');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'guard_name',
                        'display_name',
                        'category',
                        'description',
                        'created_at'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Available permissions retrieved successfully'
            ]);

    expect($response->json('data'))->toHaveCount(2);
});

test('it requires authentication', function () {
    $response = $this->getJson('/api/app/management/roles/available/permissions');

    $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
});

test('it requires proper permission', function () {
    // Create user without permission
    $userWithoutPermission = User::factory()->create();
    Sanctum::actingAs($userWithoutPermission);

    $response = $this->getJson('/api/app/management/roles/available/permissions');

    $response->assertStatus(403);
});