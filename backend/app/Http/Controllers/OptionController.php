<?php

namespace App\Http\Controllers;

use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class OptionController extends Controller
{
    /**
     * Get roles for dropdown options
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles(Request $request)
    {
        try {
            $query = Role::query();

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Get all roles or apply limit if specified
            $limit = $request->get('limit', null);
            if ($limit) {
                $roles = $query->limit($limit)->get(['id', 'name']);
            } else {
                $roles = $query->get(['id', 'name']);
            }

            // Format for dropdown
            $options = $roles->map(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => ucfirst($role->name),
                    'name' => $role->name
                ];
            });

            return BaseResponseService::success(
                $options,
                'Roles options retrieved successfully'
            );

        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to retrieve roles options',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get categories for dropdown options (placeholder)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        // Placeholder for categories options
        return BaseResponseService::success(
            [],
            'Categories options endpoint - not implemented yet'
        );
    }

    /**
     * Get suppliers for dropdown options (placeholder)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suppliers(Request $request)
    {
        // Placeholder for suppliers options
        return BaseResponseService::success(
            [],
            'Suppliers options endpoint - not implemented yet'
        );
    }

    /**
     * Get units for dropdown options (placeholder)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function units(Request $request)
    {
        // Placeholder for units options
        return BaseResponseService::success(
            [],
            'Units options endpoint - not implemented yet'
        );
    }
}