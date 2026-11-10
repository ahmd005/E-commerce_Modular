<?php

namespace Modules\Orders\App\Http\Controllers;

use Modules\Orders\Events\ItemAddedToCart;
use App\Http\Controllers\Controller;
use Modules\Orders\App\Traits\ApiResponseTrait;
use Modules\Orders\App\Http\Requests\AddToCartRequest;
use Modules\Orders\App\Http\Requests\UpdateCartRequest;
use Modules\Orders\Services\CartService;
use Modules\Orders\App\Http\Resources\CartItemResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Modules\Shared\App\Traits\AuthenticatedUserTrait;

class CartController extends Controller
{
    use ApiResponseTrait,AuthenticatedUserTrait;

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * عرض محتويات السلة الخاصة بالمستخدم الحالي
     */
    public function index(Request $request)
    {
        try {
            // ملاحظة: تم استخدام معرف المستخدم الحالي من الـ auth بدل الرقم الثابت 1
            $userId = $this->getAuthenticatedUserId(); 
            $cart = $this->cartService->getCart($userId);

            return $this->successResponse([
                'items'      => CartItemResource::collection($cart['items']),
                'cart_total' => $cart['cart_total'],
            ], 'The cart has been successfully retrieved');

        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * إضافة منتج إلى السلة
     */
    public function store(AddToCartRequest $request)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $cartItem = $this->cartService->addItem(
                $userId,
                $request->validated()
            );
            event(new ItemAddedToCart($cartItem));
            return $this->successResponse(
                new CartItemResource($cartItem), 
                'The product has been successfully added to the cart', 
                201
            );

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to add item to cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function update(UpdateCartRequest $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $cartItem = $this->cartService->updateItemQuantity(
                $uuid,
                $request->validated()['quantity'],
                $userId
            );

            return $this->successResponse(
                new CartItemResource($cartItem), 
                'The cart has been successfully updated'
            );

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Cart item or product not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to update cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * حذف عنصر معين من السلة
     */
    public function destroy(Request $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $this->cartService->deleteItem($uuid, $userId);

            return $this->successResponse(null, 'The cart item has been successfully deleted');

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Cart item not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete cart item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * تفريغ السلة بالكامل للمستخدم الحالي
     */
    public function clear()
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $this->cartService->emptyCart($userId);

            return $this->successResponse(null, 'Cart emptied successfully');

        } catch (Exception $e) {
            return $this->errorResponse('Failed to empty cart: ' . $e->getMessage(), 500);
        }
    }







     public function index2(Request $request)
    {
        try {
            // ملاحظة: تم استخدام معرف المستخدم الحالي من الـ auth بدل الرقم الثابت 1
            $userId = $this->getAuthenticatedUserId(); 
            $cart = $this->cartService->getCart($userId);

            return $this->successResponse([
                'items'      => CartItemResource::collection($cart['items']),
                'cart_total' => $cart['cart_total'],
            ], 'The cart has been successfully retrieved');

        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * إضافة منتج إلى السلة
     */
    public function store2(AddToCartRequest $request)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $cartItem = $this->cartService->addItem(
                $userId,
                $request->validated()
            );
            event(new ItemAddedToCart($cartItem));
            return $this->successResponse(
                new CartItemResource($cartItem), 
                'The product has been successfully added to the cart', 
                201
            );

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Product not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to add item to cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function update2(UpdateCartRequest $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $cartItem = $this->cartService->updateItemQuantity(
                $uuid,
                $request->validated()['quantity'],
                $userId
            );

            return $this->successResponse(
                new CartItemResource($cartItem), 
                'The cart has been successfully updated'
            );

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Cart item or product not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to update cart: ' . $e->getMessage(), 500);
        }
    }

    /**
     * حذف عنصر معين من السلة
     */
    public function destroy2(Request $request, $uuid)
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $this->cartService->deleteItem($uuid, $userId);

            return $this->successResponse(null, 'The cart item has been successfully deleted');

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Cart item not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete cart item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * تفريغ السلة بالكامل للمستخدم الحالي
     */
    public function clear2()
    {
        try {
            $userId = $this->getAuthenticatedUserId();
            $this->cartService->emptyCart($userId);

            return $this->successResponse(null, 'Cart emptied successfully');

        } catch (Exception $e) {
            return $this->errorResponse('Failed to empty cart: ' . $e->getMessage(), 500);
        }
    }
}