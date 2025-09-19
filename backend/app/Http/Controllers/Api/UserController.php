<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, filter, and pagination
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status (email verification status)
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'Active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'Inactive') {
                $query->whereNull('email_verified_at');
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSortFields = ['name', 'email', 'created_at', 'last_login'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate($request->get('per_page', 15));

        return BaseResponseService::paginated(
            $users,
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'nullable|string|url|max:500',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $userData = $validator->validated();
            $userData['password'] = Hash::make($userData['password']);
            
            // Remove roles from user data before creating user
            $roles = $userData['roles'] ?? [];
            unset($userData['roles']);
            
            $user = User::create($userData);
            
            // Assign roles if provided
            if (!empty($roles)) {
                $user->assignRole($roles);
            } else {
                // Assign default role if no roles provided
                $user->assignRole('kasir');
            }
            
            $user->load('roles');
            
            return BaseResponseService::success(
                $user,
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to create user: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions']);
        
        return BaseResponseService::success(
            $user,
            'User retrieved successfully'
        );
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|string|min:8',
            'avatar' => 'sometimes|nullable|string|url|max:500',
            'phone' => 'sometimes|nullable|string|max:20',
            'bio' => 'sometimes|nullable|string|max:1000',
            'location' => 'sometimes|nullable|string|max:255',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,name'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $userData = $validator->validated();
            
            // Hash password if provided
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }
            
            // Handle roles separately
            $roles = null;
            if (isset($userData['roles'])) {
                $roles = $userData['roles'];
                unset($userData['roles']);
            }
            
            $user->update($userData);
            
            // Update roles if provided
            if ($roles !== null) {
                $user->syncRoles($roles);
            }
            
            $user->load('roles');
            
            return BaseResponseService::success(
                $user,
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to update user: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deletion of current user
            if ($user->id === request()->user()->id) {
                return BaseResponseService::error(
                    'You cannot delete your own account',
                    403
                );
            }
            
            $user->delete();
            
            return BaseResponseService::success(
                null,
                'User deleted successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to delete user: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get user's roles
     */
    public function roles(User $user)
    {
        $user->load('roles.permissions');
        
        return BaseResponseService::success(
            $user->roles,
            'User roles retrieved successfully'
        );
    }

    /**
     * Assign roles to user
     */
    public function assignRoles(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $user->syncRoles($request->roles);
            $user->load('roles');
            
            return BaseResponseService::success(
                $user->roles,
                'Roles assigned successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to assign roles: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get user's permissions
     */
    public function permissions(User $user)
    {
        $permissions = $user->getAllPermissions();
        
        return BaseResponseService::success(
            $permissions,
            'User permissions retrieved successfully'
        );
    }

    /**
     * Get user statistics
     */
    public function stats()
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::whereNotNull('email_verified_at')->count();
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['super-admin', 'admin']);
            })->count();
            $cashierUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'kasir');
            })->count();
            
            $stats = [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'admin_users' => $adminUsers,
                'cashier_users' => $cashierUsers
            ];
            
            return BaseResponseService::success(
                $stats,
                'User statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to retrieve user statistics: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Reset user password and send notification via email
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'send_email' => 'boolean'
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            // Generate new random password
            $newPassword = Str::random(12);
            
            // Update user password
            $user->update([
                'password' => Hash::make($newPassword),
            ]);

            // Send email notification if requested (default: true)
            $sendEmail = $request->get('send_email', true);
            if ($sendEmail) {
                $user->notify(new PasswordResetNotification($newPassword));
            }

            return BaseResponseService::success([
                'message' => 'Password reset successfully',
                'new_password' => $newPassword,
                'email_sent' => $sendEmail,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 'Password reset successfully');
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to reset password: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Toggle user status (verify/unverify email)
     */
    public function toggleStatus(User $user)
    {
        try {
            // Only allow toggling for unverified users
            if ($user->email_verified_at !== null) {
                return BaseResponseService::error(
                    'Cannot modify status of verified users',
                    403
                );
            }

            // Toggle verification status
            $user->email_verified_at = $user->email_verified_at ? null : now();
            $user->save();

            $status = $user->email_verified_at ? 'verified' : 'unverified';
            
            return BaseResponseService::success(
                [
                    'user' => $user->load('roles'),
                    'status' => $status
                ],
                "User status updated to {$status}"
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to toggle user status: ' . $e->getMessage(),
                500
            );
        }
    }
}