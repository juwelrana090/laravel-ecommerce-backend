<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Order;


/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="grand_total", type="number", format="float"),
 *     @OA\Property(property="shipping_cost", type="number", format="float"),
 *     @OA\Property(property="discount", type="number", format="float"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(
 *         property="order_details",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderDetail")
 *     )
 * )
 * @OA\Schema(
 *     schema="CreateOrderRequest",
 *     type="object",
 *     required={"grand_total", "shipping_cost", "user_id", "order_details"},
 *     @OA\Property(property="grand_total", type="number", format="float"),
 *     @OA\Property(property="shipping_cost", type="number", format="float"),
 *     @OA\Property(property="discount", type="number", format="float"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(
 *         property="order_details",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderDetailRequest")
 *     )
 * )
 * @OA\Schema(
 *     schema="UpdateOrderRequest",
 *     type="object",
 *     @OA\Property(property="grand_total", type="number", format="float"),
 *     @OA\Property(property="shipping_cost", type="number", format="float"),
 *     @OA\Property(property="discount", type="number", format="float"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(
 *         property="order_details",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderDetailRequest")
 *     )
 * )
 * @OA\Schema(
 *     schema="OrderDetail",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="order_id", type="integer"),
 *     @OA\Property(property="unit_price", type="number", format="float"),
 *     @OA\Property(property="quantity", type="integer")
 * )
 * @OA\Schema(
 *     schema="OrderDetailRequest",
 *     type="object",
 *     required={"product_id", "unit_price", "quantity"},
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="unit_price", type="number", format="float"),
 *     @OA\Property(property="quantity", type="integer")
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Get a list of orders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function index()
    {
        try {
            // Fetch all orders with associated order details, products, and categories
            $orders = Order::with(['orderDetails.product.categories'])->get();

            // Transform orders to include product and category details
            $orders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'grand_total' => $order->grand_total,
                    'shipping_cost' => $order->shipping_cost,
                    'discount' => $order->discount,
                    'user_id' => $order->user_id,
                    'order_details' => $order->orderDetails->map(function ($detail) {
                        return [
                            'product_id' => $detail->product_id,
                            'product' => [
                                'id' => $detail->product->id,
                                'name' => $detail->product->name,
                                'description' => $detail->product->description,
                                'price' => $detail->product->price,
                                'slug' => $detail->product->slug,
                                'categories' => $detail->product->categories->map(function ($category) {
                                    return [
                                        'id' => $category->id,
                                        'name' => $category->name,
                                        'slug' => $category->slug,
                                    ];
                                }),
                            ],
                            'unit_price' => $detail->unit_price,
                            'quantity' => $detail->quantity,
                        ];
                    }),
                ];
            });

            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     summary="Get a single order by ID",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            // Fetch the specific order by ID with associated order details, products, and categories
            $order = Order::with(['orderDetails.product.categories'])->find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            // Transform the order to include product and category details
            $orderData = [
                'id' => $order->id,
                'grand_total' => $order->grand_total,
                'shipping_cost' => $order->shipping_cost,
                'discount' => $order->discount,
                'user_id' => $order->user_id,
                'order_details' => $order->orderDetails->map(function ($detail) {
                    return [
                        'product_id' => $detail->product_id,
                        'product' => [
                            'id' => $detail->product->id,
                            'name' => $detail->product->name,
                            'description' => $detail->product->description,
                            'price' => $detail->product->price,
                            'slug' => $detail->product->slug,
                            'categories' => $detail->product->categories->map(function ($category) {
                                return [
                                    'id' => $category->id,
                                    'name' => $category->name,
                                    'slug' => $category->slug,
                                ];
                            }),
                        ],
                        'unit_price' => $detail->unit_price,
                        'quantity' => $detail->quantity,
                    ];
                }),
            ];

            return response()->json($orderData, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/CreateOrderRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'grand_total' => 'required|numeric|min:0',
                'shipping_cost' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'user_id' => 'required|integer|exists:users,id',
                'order_details' => 'required|array',
                'order_details.*.product_id' => 'required|integer|exists:products,id',
                'order_details.*.unit_price' => 'required|numeric|min:0',
                'order_details.*.quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->only(['grand_total', 'shipping_cost', 'discount', 'user_id']);
            $order = Order::create($data);

            // Attach order details
            foreach ($request->input('order_details') as $detail) {
                $order->orderDetails()->create($detail);
            }

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => $order,
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
     *     path="/orders/{id}",
     *     summary="Update an existing order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/UpdateOrderRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'grand_total' => 'nullable|numeric|min:0',
                'shipping_cost' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'user_id' => 'nullable|integer|exists:users,id',
                'order_details' => 'nullable|array',
                'order_details.*.product_id' => 'nullable|integer|exists:products,id',
                'order_details.*.unit_price' => 'nullable|numeric|min:0',
                'order_details.*.quantity' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $data = $request->only(['grand_total', 'shipping_cost', 'discount', 'user_id']);
            $order->update($data);

            // Update order details if provided
            if ($request->has('order_details')) {
                $order->orderDetails()->delete(); // Remove existing details
                foreach ($request->input('order_details') as $detail) {
                    $order->orderDetails()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Order updated successfully',
                'data' => $order,
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
     *     path="/orders/{id}",
     *     summary="Delete an order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
