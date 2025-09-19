<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
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

        $suppliers = $query->paginate($request->get('per_page', 15));

        return BaseResponseService::paginated(
            $suppliers,
            'Suppliers retrieved successfully',
            SupplierResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $supplier = Supplier::create($validator->validated());

            return BaseResponseService::success(
                new SupplierResource($supplier),
                'Supplier created successfully',
                201
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to create supplier: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return BaseResponseService::success(
            new SupplierResource($supplier),
            'Supplier retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'code')->ignore($supplier->id),
            ],
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $supplier->update($validator->validated());

            return BaseResponseService::success(
                new SupplierResource($supplier->fresh()),
                'Supplier updated successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to update supplier: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();

            return BaseResponseService::success(
                null,
                'Supplier deleted successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to delete supplier: '.$e->getMessage(),
                500
            );
        }
    }
}
