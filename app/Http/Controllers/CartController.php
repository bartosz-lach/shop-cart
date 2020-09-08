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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $cartService = new CartService($user_id);
        return response($cartService->getFullCartView(), 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CartItemStoreRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $cartItemExists = $user->cartItems->firstWhere('product_id', $validated['product_id']);
        $cartService = new CartService($user->id, $validated['product_id']);

        if($cartService->isAllowedAddUnitProduct($validated['quantity']) ){
            if($cartService)

            if($cartService->isAllowedAddNewProduct($validated['quantity']) || $cartItemExists){
                $validated['user_id'] = $user->id;
                $cartItem = CartItem::create($validated);
                return response(new CartItemResource($cartItem), 201);
            }
            else{
                return response(['error' => "You cannot add more than $cartService->limitProductsPerCart product per cart."], 422);
            }
        }
        else{
            return response(['error' => "You cannot add more than $cartService->limitUnitsPerProduct units per product."], 422);

        }

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $productId, Request $request)
    {
        $cartService = new CartService($request->user()->id, $productId);
        $cartService->removeItemFromCart();
    }
}
