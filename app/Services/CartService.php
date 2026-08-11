<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartService extends BaseService
{
    public function getCart(Request $request): array
    {
        return $this->resolveCart($request);
    }

    public function addItem(Request $request, array $validated): array
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->resolveCart($request);

        $items = $cart->items ?? [];
        $existingIndex = $this->findItemIndexById($items, $validated['id']);

        if ($existingIndex !== -1) {
            $items[$existingIndex]['quantity'] = (int) ($items[$existingIndex]['quantity'] ?? 0) + (int) $validated['quantity'];
            $items[$existingIndex] = array_merge($items[$existingIndex], $validated);
        } else {
            $items[] = $validated;
        }

        $cart->items = array_values($items);
        $cart->save();

        return [$cart, $newSessionKey, $clearGuestCookie];
    }

    public function updateItem(Request $request, string $itemId, array $validated): array
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->resolveCart($request);

        $items = $cart->items ?? [];
        $itemIndex = $this->findItemIndexById($items, $itemId);

        if ($itemIndex === -1) {
            return [null, null, false];
        }

        $items[$itemIndex]['quantity'] = (int) $validated['quantity'];

        if (
            isset($validated['newSizeId']) &&
            isset($validated['newSize']) &&
            array_key_exists('newPrice', $validated)
        ) {
            $items[$itemIndex]['product_size_id'] = (int) $validated['newSizeId'];
            $items[$itemIndex]['size'] = $validated['newSize'];
            $items[$itemIndex]['price'] = (float) $validated['newPrice'];
            $items[$itemIndex]['id'] = ($items[$itemIndex]['product_id'] ?? '') . '-' . $validated['newSizeId'];
        }

        $cart->items = array_values($items);
        $cart->save();

        return [$cart, $newSessionKey, $clearGuestCookie];
    }

    public function removeItem(Request $request, string $itemId): array
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->resolveCart($request);

        $items = $cart->items ?? [];
        $items = array_values(array_filter($items, fn($item) => (string) ($item['id'] ?? '') !== $itemId));

        $cart->items = $items;
        $cart->save();

        return [$cart, $newSessionKey, $clearGuestCookie];
    }

    public function clearCart(Request $request): array
    {
        [$cart, $newSessionKey, $clearGuestCookie] = $this->resolveCart($request);

        $cart->items = [];
        $cart->save();

        return [$cart, $newSessionKey, $clearGuestCookie];
    }

    public function resolveCart(Request $request): array
    {
        $user = $this->resolveUserFromAccessToken($request);
        $sessionKey = $request->cookie('cart_session');
        $newSessionKey = null;
        $clearGuestCookie = false;

        if ($user) {
            $userCart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['items' => []]
            );

            if ($sessionKey) {
                $guestCart = Cart::where('session_key', $sessionKey)->whereNull('user_id')->first();
                if ($guestCart && !empty($guestCart->items)) {
                    $userCart->items = $this->mergeItems($userCart->items ?? [], $guestCart->items ?? []);
                    $userCart->save();
                    $guestCart->delete();
                    $clearGuestCookie = true;
                }
            }

            return [$userCart, null, $clearGuestCookie];
        }

        if (!$sessionKey) {
            $sessionKey = (string) Str::uuid();
            $newSessionKey = $sessionKey;
        }

        $guestCart = Cart::firstOrCreate(
            ['session_key' => $sessionKey, 'user_id' => null],
            ['items' => []]
        );

        return [$guestCart, $newSessionKey, false];
    }

    public function resolveUserFromAccessToken(Request $request)
    {
        if ($user = auth('api')->user()) {
            return $user;
        }

        if ($user = auth()->user()) {
            return $user;
        }

        $token = $request->cookie('access_token') ?? $request->bearerToken();
        if (!$token) {
            return null;
        }

        try {
            return JWTAuth::setToken($token)->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function mergeItems(array $baseItems, array $incomingItems): array
    {
        $indexed = [];

        foreach ($baseItems as $item) {
            if (!isset($item['id'])) {
                continue;
            }
            $indexed[(string) $item['id']] = $item;
        }

        foreach ($incomingItems as $item) {
            if (!isset($item['id'])) {
                continue;
            }

            $itemId = (string) $item['id'];
            if (isset($indexed[$itemId])) {
                $indexed[$itemId]['quantity'] = (int) ($indexed[$itemId]['quantity'] ?? 0) + (int) ($item['quantity'] ?? 0);
                $indexed[$itemId] = array_merge($indexed[$itemId], $item);
                continue;
            }

            $indexed[$itemId] = $item;
        }

        return array_values($indexed);
    }

    protected function findItemIndexById(array $items, string $itemId): int
    {
        foreach ($items as $index => $item) {
            if ((string) ($item['id'] ?? '') === $itemId) {
                return $index;
            }
        }

        return -1;
    }
}
