<?php

namespace Modules\Orders\App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use Modules\Orders\App\Traits\ApiResponseTrait;
use Modules\Orders\App\Traits\PaginationMetaTrait;
use Modules\Orders\App\Http\Requests\StoreOrderRequest;
use Modules\Orders\App\Http\Requests\UpdateOrderStatusRequest;
use Modules\Orders\Services\OrderService;
use Modules\Orders\App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Modules\Shared\App\Traits\AuthenticatedUserTrait;

class OrderController extends Controller
{
    use ApiResponseTrait,PaginationMetaTrait,AuthenticatedUserTrait;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the user's orders (with pagination)
     */
    public function index(Request $request)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $orders = $this->orderService->getUserOrders($userId);

            return $this->successResponse([
                'orders'     => OrderResource::collection($orders->items()),
                'pagination' => $this->paginationMeta($orders),
            ], 'Orders retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new order from the cart
     */
    /**
     * Create a new order from the cart
     */
    public function store(Request $request)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $order = $this->orderService->placeOrder($userId);

            event(new OrderCreated($order));
            return $this->successResponse(
                new OrderResource($order),
                'Order placed successfully',
                201
            );
        } catch (Exception $e) {
            $statusCode = $e->getMessage() === 'Cart is empty' ? 422 : 500;
            return $this->errorResponse('Failed to place order: ' . $e->getMessage(), $statusCode);
        }
    }

    /**
     * Display the specified order details
     */
    public function show(Request $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $order = $this->orderService->getOrder($uuid, $userId);

            return $this->successResponse(
                new OrderResource($order),
                'Order details retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Order not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve order details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified order status
     */
    public function update(UpdateOrderStatusRequest $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $order = $this->orderService->updateStatus(
                $uuid,
                $request->status(),
                $userId
            );
            event(new OrderStatusChanged($order, $order->status->value));
            return $this->successResponse(
                new OrderResource($order),
                'Order status updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Order not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to update order status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Request $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $this->orderService->deleteOrder($uuid, $userId);

            return $this->successResponse(
                null,
                'Order deleted successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Order not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete order: ' . $e->getMessage(), 500);
        }
    }
}
