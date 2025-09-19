<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\BaseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
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

        $categories = $query->paginate($request->get('per_page', 15));

        return BaseResponseService::paginated(
            $categories,
            'Categories retrieved successfully',
            CategoryResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $category = Category::create($validator->validated());

            return BaseResponseService::success(
                new CategoryResource($category),
                'Category created successfully',
                201
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to create category: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return BaseResponseService::success(
            new CategoryResource($category),
            'Category retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'code')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return BaseResponseService::validationError($validator->errors());
        }

        try {
            $category->update($validator->validated());

            return BaseResponseService::success(
                new CategoryResource($category->fresh()),
                'Category updated successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to update category: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return BaseResponseService::success(
                null,
                'Category deleted successfully'
            );
        } catch (\Exception $e) {
            return BaseResponseService::error(
                'Failed to delete category: '.$e->getMessage(),
                500
            );
        }
    }
}
