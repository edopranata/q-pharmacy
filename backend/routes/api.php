<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserActivityController;
use App\Http\Controllers\OptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes (public with rate limiting)
Route::prefix('auth')->middleware(['throttle:60,1'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('auth.verify-email');
    Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('auth.resend-verification');
});

// Protected application routes
Route::middleware(['auth:sanctum'])->prefix('app')->group(function () {
    
    // Authentication management (authenticated users)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('app.auth.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('app.auth.user');
        Route::get('/profile', [AuthController::class, 'profile'])->name('app.auth.profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('app.auth.profile.update');
        Route::put('/password', [AuthController::class, 'changePassword'])->name('app.auth.password.update');
        Route::post('/avatar', [AuthController::class, 'uploadAvatar'])->name('app.auth.avatar.upload');
        Route::delete('/avatar', [AuthController::class, 'deleteAvatar'])->name('app.auth.avatar.delete');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('app.auth.refresh');
    });
    
    // Master Data Management
    Route::prefix('master')->group(function () {
        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('app.master.categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('app.master.categories.store');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('app.master.categories.show');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('app.master.categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('app.master.categories.destroy');
        Route::post('/categories/{category}/restore', [CategoryController::class, 'restore'])->name('app.master.categories.restore');
        
        // Suppliers
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('app.master.suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('app.master.suppliers.store');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('app.master.suppliers.show');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('app.master.suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('app.master.suppliers.destroy');
        Route::post('/suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('app.master.suppliers.restore');
        
        // Units
        Route::get('/units', [UnitController::class, 'index'])->name('app.master.units.index');
        Route::post('/units', [UnitController::class, 'store'])->name('app.master.units.store');
        Route::get('/units/{unit}', [UnitController::class, 'show'])->name('app.master.units.show');
        Route::put('/units/{unit}', [UnitController::class, 'update'])->name('app.master.units.update');
        Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('app.master.units.destroy');
        Route::post('/units/{unit}/restore', [UnitController::class, 'restore'])->name('app.master.units.restore');
    });
    
    // Product Management (placeholder routes - controllers to be created)
    Route::prefix('products')->group(function () {
        // TODO: Implement ProductController
        // Route::get('/', [ProductController::class, 'index'])->name('app.products.index');
        // Route::post('/', [ProductController::class, 'store'])->name('app.products.store');
        // Route::get('/{product}', [ProductController::class, 'show'])->name('app.products.show');
        // Route::put('/{product}', [ProductController::class, 'update'])->name('app.products.update');
        // Route::delete('/{product}', [ProductController::class, 'destroy'])->name('app.products.destroy');
    });
    
    // Inventory Management (placeholder routes - controllers to be created)
    Route::prefix('inventories')->group(function () {
        // TODO: Implement InventoryController
        // Route::get('/', [InventoryController::class, 'index'])->name('app.inventories.index');
        // Route::post('/adjustments', [InventoryController::class, 'adjustment'])->name('app.inventories.adjustments.store');
    });
    
    // Sales Management (placeholder routes - controllers to be created)
    Route::prefix('sells')->group(function () {
        // TODO: Implement SalesController
        // Route::get('/', [SalesController::class, 'index'])->name('app.sells.index');
        // Route::post('/', [SalesController::class, 'store'])->name('app.sells.store');
    });
    
    // Statistics and Reports (placeholder routes - controllers to be created)
    Route::prefix('stats')->group(function () {
        // TODO: Implement StatsController
        // Route::get('/dashboard', [StatsController::class, 'dashboard'])->name('app.stats.dashboard.index');
        // Route::get('/sales', [StatsController::class, 'sales'])->name('app.stats.sales.index');
    });
    
    // Audit Logs (Admin only)
    Route::middleware(['permission:app.audit-logs.index'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('app.audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('app.audit-logs.show');
    });
    
    // User Activity Management
    Route::prefix('user-activity')->group(function () {
        Route::get('/my-activity', [UserActivityController::class, 'myActivity'])->name('app.user-activity.my-activity');
        Route::get('/my-history', [UserActivityController::class, 'myHistory'])->name('app.user-activity.my-history');
        
        // Admin only routes
        Route::middleware(['permission:app.management.users.index'])->group(function () {
            Route::get('/online-users', [UserActivityController::class, 'onlineUsers'])->name('app.user-activity.online-users');
            Route::get('/statistics', [UserActivityController::class, 'statistics'])->name('app.user-activity.statistics');
            Route::get('/inactive-users', [UserActivityController::class, 'inactiveUsers'])->name('app.user-activity.inactive-users');
        });
    });
    
    // User Management
    Route::prefix('management/users')->middleware(['permission:app.management.users.index'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('app.management.users.index');
        Route::get('/stats', [UserController::class, 'stats'])->name('app.management.users.stats');
        Route::post('/', [UserController::class, 'store'])->middleware(['permission:app.management.users.store'])->name('app.management.users.store');
        Route::get('/{user}', [UserController::class, 'show'])->middleware(['permission:app.management.users.show'])->name('app.management.users.show');
        Route::put('/{user}', [UserController::class, 'update'])->middleware(['permission:app.management.users.update'])->name('app.management.users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware(['permission:app.management.users.destroy'])->name('app.management.users.destroy');
        
        // User status toggle (email verification)
        Route::patch('/{user}/status', [UserController::class, 'toggleStatus'])->middleware(['permission:app.management.users.update'])->name('app.management.users.toggle-status');
        
        // Password reset
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware(['permission:app.management.users.update'])->name('app.management.users.reset-password');
        
        // User roles and permissions
        Route::get('/{user}/roles', [UserController::class, 'roles'])->name('app.management.users.roles');
        Route::post('/{user}/roles', [UserController::class, 'assignRoles'])->middleware(['permission:app.management.users.assign-roles'])->name('app.management.users.assign-roles');
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])->name('app.management.users.permissions');
    });
    
    // Role Management
    Route::prefix('management/roles')->middleware(['permission:app.management.roles.index'])->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('app.management.roles.index');
        Route::post('/', [RoleController::class, 'store'])->middleware(['permission:app.management.roles.store'])->name('app.management.roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware(['permission:app.management.roles.show'])->name('app.management.roles.show');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware(['permission:app.management.roles.update'])->name('app.management.roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware(['permission:app.management.roles.destroy'])->name('app.management.roles.destroy');
        
        // Role permissions and users
        Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('app.management.roles.permissions');
        Route::post('/{role}/permissions', [RoleController::class, 'assignPermissions'])->middleware(['permission:app.management.roles.assign-permissions'])->name('app.management.roles.assign-permissions');
        Route::get('/{role}/users', [RoleController::class, 'users'])->name('app.management.roles.users');
        
        // Available permissions for role assignment
        Route::get('/available/permissions', [RoleController::class, 'availablePermissions'])->name('app.management.roles.available-permissions');
    });
});

// Options/Dropdown endpoints with serverside filtering
Route::middleware(['auth:sanctum'])->prefix('options')->group(function () {
    Route::get('/roles', [OptionController::class, 'roles'])->name('options.roles.index');
    Route::get('/categories', [OptionController::class, 'categories'])->name('options.categories.index');
    Route::get('/suppliers', [OptionController::class, 'suppliers'])->name('options.suppliers.index');
    Route::get('/units', [OptionController::class, 'units'])->name('options.units.index');
});

// Test endpoint for security headers testing
Route::get('/test-headers', function () {
    return response()->json(['message' => 'Security headers test']);
})->name('test.headers');
