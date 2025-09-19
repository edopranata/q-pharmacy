<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unit::query();

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('symbol', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $units = $query->paginate($request->get('per_page', 15));

        return BaseResponseService::paginated(
            $units,
            'Units retrieved successfully',
            UnitResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code',
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $unit = Unit::create($validator->validated());

            return BaseResponseService::success(
                new UnitResource($unit),
                'Unit created successfully',
                201
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to create unit: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return BaseResponseService::success(
            new UnitResource($unit),
            'Unit retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'code')->ignore($unit->id),
            ],
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $unit->update($validator->validated());

            return BaseResponseService::success(
                new UnitResource($unit->fresh()),
                'Unit updated successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to update unit: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            $unit->delete();

            return BaseResponseService::success(
                null,
                'Unit deleted successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to delete unit: '.$e->getMessage(),
                500
            );
        }
    }
}
