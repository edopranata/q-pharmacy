<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign default role
        $user->assignRole('kasir');

        $token = $user->createToken('auth_token')->plainTextToken;

        return BaseResponseService::created([
            'user' => new UserResource($user->load('roles.permissions')),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully');
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return BaseResponseService::unauthorized('Invalid credentials');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update last_login timestamp
        $user->update(['last_login' => now()]);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return BaseResponseService::success([
            'user' => new UserResource($user->load('roles.permissions')),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        
        // Check if it's a real token (not TransientToken from testing)
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return BaseResponseService::success(null, 'Logout successful');
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->load('roles.permissions');
        
        return BaseResponseService::success(
            new UserResource($user),
            'User data retrieved successfully'
        );
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        return BaseResponseService::success(
            new UserResource($user->load('roles.permissions')),
            'Profile retrieved successfully'
        );
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$request->user()->id,
            'avatar' => 'nullable|string|url|max:500',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        $user = $request->user();
        $updateData = array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $request->avatar,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'location' => $request->location,
        ], function($value) {
            return $value !== null;
        });
        
        $user->update($updateData);

        return BaseResponseService::success(
            new UserResource($user->load('roles.permissions')),
            'Profile updated successfully'
        );
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return BaseResponseService::validationError([
                'current_password' => ['Current password is incorrect']
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return BaseResponseService::success(null, 'Password changed successfully');
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048', // 2MB max
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $user = $request->user();
            
            // Delete old avatar if exists
            if ($user->avatar) {
                $this->deleteAvatarFile($user->avatar);
            }

            // Store new avatar
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            
            // Generate full URL
            $avatarUrl = asset('storage/' . $path);
            
            // Update user avatar URL in database
            $user->update(['avatar' => $avatarUrl]);

            return BaseResponseService::success([
                'avatar_url' => $avatarUrl,
                'user' => new UserResource($user->load('roles.permissions'))
            ], 'Avatar uploaded successfully');

        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to upload avatar: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user->avatar) {
                return BaseResponseService::error('No avatar to delete', 404);
            }

            // Delete avatar file from storage
            $this->deleteAvatarFile($user->avatar);
            
            // Remove avatar URL from database
            $user->update(['avatar' => null]);

            return BaseResponseService::success([
                'user' => new UserResource($user->load('roles.permissions'))
            ], 'Avatar deleted successfully');

        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to delete avatar: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Helper method to delete avatar file from storage
     */
    private function deleteAvatarFile($avatarUrl)
    {
        if (!$avatarUrl) {
            return;
        }

        try {
            // Extract file path from URL
            $path = str_replace(asset('storage/'), '', $avatarUrl);
            
            // Delete file from public storage
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            // Log error but don't throw exception to avoid breaking the main operation
            Log::warning('Failed to delete avatar file: ' . $e->getMessage());
        }
    }
}
