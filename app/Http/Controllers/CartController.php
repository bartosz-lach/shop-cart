<?php

namespace App\Http\Controllers;

use App\CartItem;
use App\Http\Requests\CartItemStoreRequest;
use App\Http\Resources\CartItemResource;
use App\Product;
use App\Services\CartService;
use App\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = CartItem::getCartByUser($user);
        $response['total_cost'] = CartItem::calcTotalCost($user);
        $response['items'] = CartItemResource::Collection($cartItems);
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CartItemStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addProductToCart(CartItemStoreRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $cartItem = new CartItem($validated);
        try {
            $cartItem = $cartItem->createNewOrIncreaseQuantity();
            return response(new CartItemResource($cartItem), 201);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CartItem $cartItem
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(CartItem $cartItem, Request $request)
    {
        if ($request->user()->id == $cartItem->user_id) {
            $cartItem->delete();
            return response(null, 204);
        } else {
            return response(['error' => 'Unauthorized '], 401);
        }
    }
}
