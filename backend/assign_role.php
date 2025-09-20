<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "Available roles:" . PHP_EOL;
$roles = Role::all();
foreach ($roles as $role) {
    echo "- " . $role->name . PHP_EOL;
}

$user = User::where('email', 'test@example.com')->first();

if ($user) {
    echo PHP_EOL . "User found: " . $user->email . PHP_EOL;
    
    // Try to assign admin role
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        $user->assignRole($adminRole);
        echo "Role 'admin' assigned to user: " . $user->email . PHP_EOL;
    } else {
        echo "Admin role not found" . PHP_EOL;
    }
} else {
    echo "User not found" . PHP_EOL;
}