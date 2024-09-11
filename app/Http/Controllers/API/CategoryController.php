<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Models\Category;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="slug", type="string", example="electronics"),
 *     @OA\Property(property="parent_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-10T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-10T12:34:56Z")
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/category",
     *     summary="Get all categories",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: ...")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $categories = Category::latest()->get();
            return response()->json([
                'status' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/category",
     *     summary="Create a new category",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="parent_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="object", additionalProperties=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: ...")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $token = $request->bearerToken(); // Get token from the request

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|integer|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $name = $request->name;
            $slug = Str::slug($name);

            // Retrieve existing slugs to ensure uniqueness
            $categories = Category::where('slug', 'LIKE', "{$slug}%")->get();
            $data = $categories->pluck('slug')->toArray();

            // Ensure slug uniqueness
            $cat_count = 0;
            while (in_array($slug, $data)) {
                $slug = Str::slug($name . '-' . ++$cat_count);
            }

            // Create the new category
            $category = Category::create([
                'name' => $name,
                'slug' => $slug,
                'parent_id' => $request->parent_id ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => "Category created successfully",
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/category/{category}",
     *     summary="Update an existing category",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Electronics"),
     *             @OA\Property(property="parent_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="object", additionalProperties=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: ...")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Category $category)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|integer|exists:categories,id', // Validate parent_id if needed
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Get validated data
            $data = $validator->validated();

            // Update slug if name has changed
            if (isset($data['name']) && $data['name'] !== $category->name) {
                $slug = Str::slug($data['name']);

                // Check for uniqueness
                $categories = Category::where('slug', 'LIKE', "{$slug}%")->where('id', '<>', $category->id)->get();
                $existingSlugs = $categories->pluck('slug')->toArray();

                $cat_count = 0;
                while (in_array($slug, $existingSlugs)) {
                    $slug = Str::slug($data['name'] . '-' . ++$cat_count);
                }

                $data['slug'] = $slug;
            }

            // Update the category
            $category->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            // Log the error if necessary
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/category/{category}",
     *     summary="Delete a category",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Category cannot be deleted because it has associated products",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category cannot be deleted because it has associated products.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: ...")
     *         )
     *     )
     * )
     */
    public function destroy(Category $category)
    {
        try {
            // For example, check if the category has associated products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category cannot be deleted because it has associated products.',
                ], 400);
            }

            // Delete the category
            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Log the error if necessary
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
