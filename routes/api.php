<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');

Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', 'ProductController');
//    Route::apiResource('cart', 'CartController');
    Route::post('carts', 'CartController@addProductToCart')->name('carts.add-product-to-cart');
    Route::get('carts', 'CartController@index')->name('carts.index');
    Route::delete('cart-items/{cartItem}', 'CartController@destroy')->name('cart-items.delete');
});
