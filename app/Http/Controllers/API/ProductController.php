<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;
use App\Models\ProductCategory;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Product Name"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="sales_count", type="integer", example=150),
 *     @OA\Property(property="rating", type="number", format="float", example=4.5),
 *     @OA\Property(property="description", type="string", example="Product description here"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-10T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-10T12:34:56Z")
 * )
 * @OA\Schema(
 *     schema="ProductCategory",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-10T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-10T12:34:56Z")
 * )
 */
class ProductController extends Controller
{

    /**
     * @OA\Get(
     *     path="/product",
     *     summary="Get all products",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product"))
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
            // Fetch all products and load their related categories (id, name, slug)
            $products = Product::with('categories:id,name,slug')->get();

            // Transform the product data to include the required fields
            $response = $products->map(function ($product) {
                return [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'categories' => $product->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                        ];
                    }),
                ];
            });

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/product/{slug}",
     *     summary="Get a product by slug",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
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
    public function getProduct($slug)
    {
        try {
            // Find the product by slug and load its related categories (id, name, slug)
            $product = Product::with('categories:id,name,slug')->where('slug', $slug)->first();

            // If the product doesn't exist, return a 404 response
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // Format the response
            $response = [
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'categories' => $product->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                }),
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/product",
     *     summary="Create a new product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="New Product"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="sales_count", type="integer", example=0),
     *             @OA\Property(property="rating", type="number", format="float", example=0),
     *             @OA\Property(property="description", type="string", example="Product description here"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
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
                    'status' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'categories' => 'required|array',
                'categories.*' => 'integer|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Generate unique slug for the product
            $name = $request->input('name');
            $slug = Str::slug($name);

            // Retrieve existing slugs to ensure uniqueness
            $products = Product::where('slug', 'LIKE', "{$slug}%")->get();
            $existingSlugs = $products->pluck('slug')->toArray();

            // Ensure slug uniqueness
            $count = 0;
            while (in_array($slug, $existingSlugs)) {
                $slug = Str::slug($name . '-' . ++$count);
            }

            // Prepare product data
            $data = $request->only(['name', 'description', 'price']);
            $data['slug'] = $slug;

            // Create the product
            $product = Product::create($data);

            // Attach categories
            $product->categories()->sync($request->input('categories'));

            // Return response with product and its categories
            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product->load('categories') // Load categories relation
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
     *     path="/product/{slug}",
     *     summary="Update an existing product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="price", type="number", format="float", example=89.99),
     *             @OA\Property(property="sales_count", type="integer", example=200),
     *             @OA\Property(property="rating", type="number", format="float", example=4.8),
     *             @OA\Property(property="description", type="string", example="Updated product description here"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
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
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
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
    public function update(Request $request, $slug)
    {
        try {
            // Check for the token
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'sales_count' => 'nullable|integer|min:0',
                'rating' => 'nullable|numeric|min:0|max:5',
                'description' => 'nullable|string',
                'categories' => 'nullable|array',
                'categories.*' => 'integer|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Find the product by slug
            $product = Product::where('slug', $slug)->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // Get the validated data
            $data = $validator->validated();

            // If the name has been updated, generate a new unique slug
            if (isset($data['name']) && $data['name'] !== $product->name) {
                $newSlug = Str::slug($data['name']);

                // Check for existing slugs to ensure uniqueness
                $existingSlugs = Product::where('slug', 'LIKE', "{$newSlug}%")->pluck('slug')->toArray();

                $count = 0;
                $uniqueSlug = $newSlug;

                // Ensure the slug is unique
                while (in_array($uniqueSlug, $existingSlugs)) {
                    $uniqueSlug = Str::slug($data['name'] . '-' . ++$count);
                }

                // Update the slug in the data array
                $data['slug'] = $uniqueSlug;
            }

            // Update the product details
            $product->update($data);

            // Update categories if provided
            if (isset($data['categories'])) {
                // Sync categories
                $product->categories()->sync($data['categories']);
            }

            // Load categories with id, name, and slug
            $product->load('categories:id,name,slug');

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'categories' => $product->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                        ];
                    }),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }



    /**
     * @OA\Delete(
     *     path="/product/{slug}",
     *     summary="Delete a product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
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
    public function destroy(Request $request, $slug)
    {
        try {
            $token = $request->bearerToken(); // Get token from the request

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Find the product by slug
            $product = Product::where('slug', $slug)->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // Delete the product
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
