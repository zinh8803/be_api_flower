<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->cartService->getCart($request);

        return $this->cartResponse($cart, $newSessionKey, $clearGuestCookie);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'string'],
            'product_id' => ['required', 'integer'],
            'product_size_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'image' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'size' => ['nullable', 'string'],
            'sizes' => ['nullable', 'array'],
        ]);

        [$cart, $newSessionKey, $clearGuestCookie] = $this->cartService->addItem($request, $validated);

        return $this->cartResponse($cart, $newSessionKey, $clearGuestCookie);
    }

    public function update(Request $request, string $itemId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'newSizeId' => ['nullable', 'integer'],
            'newSize' => ['nullable', 'string'],
            'newPrice' => ['nullable', 'numeric'],
        ]);

        [$cart, $newSessionKey, $clearGuestCookie] = $this->cartService->updateItem($request, $itemId, $validated);

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng',
            ], 404);
        }

        return $this->cartResponse($cart, $newSessionKey, $clearGuestCookie);
    }

    public function destroy(Request $request, string $itemId)
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->cartService->removeItem($request, $itemId);

        return $this->cartResponse($cart, $newSessionKey, $clearGuestCookie);
    }

    public function clear(Request $request)
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->cartService->clearCart($request);

        return $this->cartResponse($cart, $newSessionKey, $clearGuestCookie);
    }

    private function cartResponse($cart, ?string $newSessionKey = null, bool $clearGuestCookie = false)
    {
        $response = response()->json([
            'status' => true,
            'message' => 'Cart data',
            'data' => $cart->items ?? [],
        ]);

        if ($newSessionKey) {
            $response->withCookie(make_cookie('cart_session', $newSessionKey, 20160, true));
        }

        if ($clearGuestCookie) {
            $response->withCookie(cookie()->forget('cart_session'));
        }

        return $response;
    }
}
