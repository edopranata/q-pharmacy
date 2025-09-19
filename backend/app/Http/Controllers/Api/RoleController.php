<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Get role statistics for dashboard
     */
    public function stats()
    {
        try {
            $totalRoles = Role::count();
            $systemRoles = Role::whereIn('name', ['Super-Admin', 'admin', 'kasir'])->count();
            $customRoles = $totalRoles - $systemRoles;
            
            // Get roles with user counts
            $rolesWithUsers = Role::withCount('users')->get();
            $rolesWithoutUsers = $rolesWithUsers->where('users_count', 0)->count();
            $rolesWithUsers = $rolesWithUsers->where('users_count', '>', 0)->count();
            
            // Get average permissions per role
            $avgPermissions = Role::withCount('permissions')->avg('permissions_count') ?? 0;
            
            $stats = [
                'total_roles' => $totalRoles,
                'system_roles' => $systemRoles,
                'custom_roles' => $customRoles,
                'roles_with_users' => $rolesWithUsers,
                'roles_without_users' => $rolesWithoutUsers,
                'avg_permissions_per_role' => round($avgPermissions, 1)
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
        $query = Role::with(['permissions']);

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
        $allowedSortFields = ['name', 'guard_name', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }

        $roles = $query->paginate($request->get('per_page', 15));

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
     */
    public function availablePermissions()
    {
        $permissions = Permission::orderBy('name')->get();
        
        return BaseResponseService::success(
            $permissions,
            'Available permissions retrieved successfully'
        );
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
}