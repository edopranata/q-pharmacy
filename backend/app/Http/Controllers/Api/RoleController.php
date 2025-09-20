<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Services\BaseResponseService;
use App\Services\AuditLogger;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Get role statistics for dashboard
     */
    public function stats()
    {
        try {
            // Total roles count
            $totalRoles = Role::count();
            
            // Active roles (roles that have at least one user assigned)
            $activeRoles = DB::table('model_has_roles')
                ->where('model_type', 'App\Models\User')
                ->distinct('role_id')
                ->count();
            
            // Total permissions available in the system
            $totalPermissions = Permission::count();
            
            // Total users that have roles assigned
            $totalAssignedUsers = DB::table('model_has_roles')
                ->where('model_type', 'App\Models\User')
                ->distinct('model_id')
                ->count();
            
            $stats = [
                'total_roles' => $totalRoles,
                'active_roles' => $activeRoles,
                'total_permissions' => $totalPermissions,
                'total_assigned_users' => $totalAssignedUsers
            ];
            
            return BaseResponseService::success(
                $stats,
                'Role statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to retrieve role statistics: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display a listing of roles with search, filter, and pagination
     */
    public function index(Request $request)
    {
        $query = Role::with(['permissions'])
            ->withCount('permissions');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSortFields = ['name', 'guard_name', 'created_at', 'permissions_count'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }

        $roles = $query->paginate($request->get('per_page', 15));
        
        // Manually add users count for each role
        $roles->getCollection()->transform(function ($role) {
            $role->users_count = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', 'App\Models\User')
                ->count();
            return $role;
        });

        return BaseResponseService::paginated(
            $roles,
            'Roles retrieved successfully'
        );
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'sometimes|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $roleData = $validator->validated();
            
            // Remove permissions from role data before creating role
            $permissions = $roleData['permissions'] ?? [];
            unset($roleData['permissions']);
            
            // Set default guard_name if not provided
            if (!isset($roleData['guard_name'])) {
                $roleData['guard_name'] = 'web';
            }
            
            $role = Role::create($roleData);
            
            // Assign permissions if provided
            if (!empty($permissions)) {
                $role->givePermissionTo($permissions);
            }
            
            $role->load('permissions');
            
            return BaseResponseService::success(
                $role,
                'Role created successfully',
                201
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to create role: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['permissions']);
        
        // Get users count for this role
        $usersCount = $role->users()->count();
        $role->users_count = $usersCount;
        
        return BaseResponseService::success(
            $role,
            'Role retrieved successfully'
        );
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'guard_name' => 'sometimes|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $roleData = $validator->validated();
            
            // Handle permissions separately
            $permissions = null;
            if (isset($roleData['permissions'])) {
                $permissions = $roleData['permissions'];
                unset($roleData['permissions']);
            }
            
            $role->update($roleData);
            
            // Update permissions if provided
            if ($permissions !== null) {
                $role->syncPermissions($permissions);
            }
            
            $role->load('permissions');
            
            return BaseResponseService::success(
                $role,
                'Role updated successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to update role: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        try {
            // Prevent deletion of system roles
            $systemRoles = ['Super-Admin', 'admin', 'kasir'];
            if (in_array($role->name, $systemRoles)) {
                return BaseResponseService::error(
                    'Cannot delete system role: ' . $role->name,
                    403
                );
            }
            
            // Check if role has users
            if ($role->users()->count() > 0) {
                return BaseResponseService::error(
                    'Cannot delete role that has assigned users',
                    403
                );
            }
            
            $role->delete();
            
            return BaseResponseService::success(
                null,
                'Role deleted successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to delete role: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get role's permissions
     */
    public function permissions(Role $role)
    {
        $role->load('permissions');
        
        return BaseResponseService::success(
            $role->permissions,
            'Role permissions retrieved successfully'
        );
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $role->syncPermissions($request->permissions);
            $role->load('permissions');
            
            return BaseResponseService::success(
                $role->permissions,
                'Permissions assigned successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to assign permissions: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get all available permissions
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function availablePermissions()
    {
        try {
            Log::info('Starting availablePermissions method', [
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);

            // Check cache first
            $cacheKey = 'available_permissions';
            $permissions = Cache::remember($cacheKey, 3600, function () {
                Log::info('Fetching permissions from database');
                return Permission::orderBy('name')->get();
            });

            Log::info('Retrieved permissions', [
                'count' => $permissions->count(),
                'cached' => Cache::has($cacheKey)
            ]);

            // Format permissions with enhanced metadata
            $formattedData = $this->formatPermissionsResponse($permissions);

            // Log audit trail
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'permissions.list',
                'model_type' => Permission::class,
                'model_id' => null,
                'old_values' => null,
                'new_values' => [
                    'permissions_count' => $permissions->count(),
                    'request_ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Log::info('Successfully retrieved available permissions', [
                'permissions_count' => $permissions->count(),
                'categories_count' => count($formattedData['permissions']),
                'user_id' => Auth::id(),
            ]);

            return BaseResponseService::success(
                $formattedData,
                'Available permissions retrieved successfully'
            );

        } catch (\Exception $e) {
             Log::error('Failed to retrieve available permissions', [
                 'error' => $e->getMessage(),
                 'trace' => $e->getTraceAsString(),
                 'user_id' => Auth::id(),
                 'ip_address' => request()->ip(),
             ]);

            return BaseResponseService::error(
                'Failed to retrieve available permissions: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get permission category based on permission name
     * 
     * @param string $permissionName
     * @return string
     */
    private function getPermissionCategory(string $permissionName): string
    {
        $categories = [
            'app.management.users' => 'User Management',
            'app.management.roles' => 'Role Management', 
            'app.master.categories' => 'Master Data - Categories',
            'app.master.suppliers' => 'Master Data - Suppliers',
            'app.master.units' => 'Master Data - Units',
            'app.master' => 'Master Data',
            'app.audit-logs' => 'Audit Logs',
            'app.products' => 'Product Management',
            'app.inventories.stock-in' => 'Inventory - Stock In',
            'app.inventories.stock-out' => 'Inventory - Stock Out',
            'app.inventories.adjustments' => 'Inventory - Adjustments',
            'app.inventories' => 'Inventory Management',
            'app.sells.pos' => 'Sales - Point of Sale',
            'app.sells.transactions' => 'Sales - Transactions',
            'app.sells' => 'Sales Management',
            'app.reports.sales' => 'Reports - Sales',
            'app.reports.inventory' => 'Reports - Inventory',
            'app.reports.financial' => 'Reports - Financial',
            'app.reports' => 'Reports & Analytics',
        ];

        // Find the most specific match first
        foreach ($categories as $prefix => $category) {
            if (str_starts_with($permissionName, $prefix)) {
                return $category;
            }
        }

        return 'General';
    }

    /**
     * Get users assigned to this role
     */
    public function users(Role $role)
    {
        $users = $role->users()->with('roles')->get();
        
        return BaseResponseService::success(
            $users,
            'Role users retrieved successfully'
        );
    }

    /**
     * Clear permissions cache
     * This method should be called when permissions are modified
     * 
     * @return void
     */
    private function clearPermissionsCache(): void
    {
        Cache::forget('available_permissions');
        
        // Also clear any role-specific permission caches if they exist
        Cache::tags(['permissions', 'roles'])->flush();
    }

    /**
     * Format permissions response with additional metadata
     * 
     * @param \Illuminate\Database\Eloquent\Collection $permissions
     * @return array
     */
    private function formatPermissionsResponse($permissions): array
    {
        $groupedPermissions = [];
        $totalPermissions = $permissions->count();
        $categoryStats = [];
        
        foreach ($permissions as $permission) {
            $category = $this->getPermissionCategory($permission->name);
            $description = $this->getPermissionDescription($permission->name);
            
            if (!isset($groupedPermissions[$category])) {
                $groupedPermissions[$category] = [
                    'category' => $category,
                    'category_label' => $this->getCategoryLabel($category),
                    'permissions' => []
                ];
                $categoryStats[$category] = 0;
            }
            
            $groupedPermissions[$category]['permissions'][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'description' => $description,
                'guard_name' => $permission->guard_name,
                'category' => $category,
                'action' => $this->getPermissionAction($permission->name),
                'module' => $this->getPermissionModule($permission->name),
                'created_at' => $permission->created_at,
                'updated_at' => $permission->updated_at,
            ];
            
            $categoryStats[$category]++;
        }
        
        // Add count to each category
        foreach ($groupedPermissions as $category => &$group) {
            $group['count'] = $categoryStats[$category];
        }
        
        return [
            'permissions' => array_values($groupedPermissions),
            'metadata' => [
                'total_permissions' => $totalPermissions,
                'total_categories' => count($groupedPermissions),
                'category_stats' => $categoryStats,
                'generated_at' => now()->toISOString(),
                'cache_expires_at' => now()->addHour()->toISOString(),
                'version' => '1.0',
                'guard_name' => 'web'
            ]
        ];
    }
    
    /**
     * Get category label for display
     * 
     * @param string $category
     * @return string
     */
    private function getCategoryLabel(string $category): string
    {
        $labels = [
            'app.management.users' => 'User Management',
            'app.management.roles' => 'Role Management',
            'app.audit-logs' => 'Audit Logs',
            'app.master.categories' => 'Product Categories',
            'app.master.suppliers' => 'Suppliers',
            'app.master.units' => 'Units',
            'app.products' => 'Product Management',
            'app.inventories' => 'Inventory Management',
            'app.inventories.stock-in' => 'Stock In',
            'app.inventories.stock-out' => 'Stock Out',
            'app.inventories.adjustments' => 'Inventory Adjustments',
            'app.sells' => 'Sales Management',
            'app.sells.pos' => 'Point of Sale',
            'app.sells.transactions' => 'Sales Transactions',
            'app.reports' => 'Reports',
        ];
        
        return $labels[$category] ?? ucwords(str_replace(['.', '_', '-'], ' ', $category));
    }
    
    /**
     * Get permission action (index, create, edit, delete, etc.)
     * 
     * @param string $permissionName
     * @return string
     */
    private function getPermissionAction(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        $action = end($parts);
        
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'store' => 'create',
            'create' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
            'delete' => 'delete',
            'assign-roles' => 'assign',
            'assign-permissions' => 'assign',
            'pos' => 'access',
        ];
        
        return $actionMap[$action] ?? $action;
    }
    
    /**
     * Get permission module
     * 
     * @param string $permissionName
     * @return string
     */
    private function getPermissionModule(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        
        if (count($parts) >= 3) {
            return $parts[1] . '.' . $parts[2];
        }
        
        return $parts[1] ?? 'general';
    }

    /**
     * Get permission description based on permission name
     * 
     * @param string $permissionName
     * @return string
     */
    private function getPermissionDescription(string $permissionName): string
    {
        $descriptions = [
            // User Management
            'app.management.users.index' => 'View users list and search users',
            'app.management.users.store' => 'Create new users and register accounts',
            'app.management.users.show' => 'View user details and profile information',
            'app.management.users.update' => 'Update user information and profile',
            'app.management.users.destroy' => 'Delete users and deactivate accounts',
            'app.management.users.assign-roles' => 'Assign and manage user roles',
            
            // Role Management
            'app.management.roles.index' => 'View roles list and permissions',
            'app.management.roles.store' => 'Create new roles and define permissions',
            'app.management.roles.show' => 'View role details and assigned permissions',
            'app.management.roles.update' => 'Update role information and permissions',
            'app.management.roles.destroy' => 'Delete roles and remove assignments',
            'app.management.roles.assign-permissions' => 'Assign and manage role permissions',
            
            // Audit Logs
            'app.audit-logs.index' => 'View system audit logs and user activities',
            
            // Master Data - Categories
            'app.master.categories.index' => 'View product categories list',
            'app.master.categories.create' => 'Create new product categories',
            'app.master.categories.edit' => 'Edit and update product categories',
            'app.master.categories.delete' => 'Delete product categories',
            
            // Master Data - Suppliers
            'app.master.suppliers.index' => 'View suppliers list and information',
            'app.master.suppliers.create' => 'Create new supplier records',
            'app.master.suppliers.edit' => 'Edit and update supplier information',
            'app.master.suppliers.delete' => 'Delete supplier records',
            
            // Master Data - Units
            'app.master.units.index' => 'View measurement units list',
            'app.master.units.create' => 'Create new measurement units',
            'app.master.units.edit' => 'Edit and update measurement units',
            'app.master.units.delete' => 'Delete measurement units',
            
            // Product Management
            'app.products.index' => 'View products list and inventory',
            'app.products.create' => 'Create new products and add to inventory',
            'app.products.edit' => 'Edit and update product information',
            'app.products.delete' => 'Delete products from inventory',
            'app.products.show' => 'View detailed product information',
            'app.products.pricing.index' => 'View and manage product pricing',
            
            // Inventory Management
            'app.inventories.index' => 'View inventory overview and stock levels',
            'app.inventories.stock-in.index' => 'View stock in transactions',
            'app.inventories.stock-in.create' => 'Create stock in transactions',
            'app.inventories.stock-out.index' => 'View stock out transactions',
            'app.inventories.adjustments.index' => 'View and manage inventory adjustments',
            
            // Sales & POS
            'app.sells.index' => 'View sales overview and transactions',
            'app.sells.pos' => 'Access point of sale system',
            'app.sells.transactions.index' => 'View sales transactions history',
            'app.sells.transactions.show' => 'View detailed transaction information',
            
            // Reports
            'app.reports.index' => 'View reports dashboard and overview',
            'app.reports.sales' => 'Generate and view sales reports',
            'app.reports.inventory' => 'Generate and view inventory reports',
            'app.reports.financial' => 'Generate and view financial reports',
        ];

        return $descriptions[$permissionName] ?? 'Permission for ' . str_replace(['.', '_', '-'], ' ', $permissionName);
    }
}