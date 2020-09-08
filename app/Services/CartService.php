<?php


namespace App\Services;


use App\CartItem;
use App\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public int $userId;
    public ?int $productId;
    public int $limitUnitsPerProduct;
    public int $limitProductsPerCart;

    public function __construct(int $userId, ?int $productId = null, int $limitUnitsPerProduct = 10, int $limitProductsPerCart = 3)
    {
        $this->userId = $userId;
        $this->productId = $productId;
        $this->limitUnitsPerProduct = $limitUnitsPerProduct;
        $this->limitProductsPerCart = $limitProductsPerCart;
    }

    private function countProductUnitsInCart(): int
    {
        return DB::table('cart_items')->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('user_id', $this->userId)
            ->where('product_id', $this->productId)
            ->count();
    }

    private function countProductInCart(): int
    {
        return DB::table('cart_items')->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('user_id', $this->userId)
            ->count();
    }

    public function isAllowedAddUnitProduct(int $quantity): bool
    {
        return ($this->countProductUnitsInCart() + $quantity) <= $this->limitUnitsPerProduct;
    }

    public function isAllowedAddNewProduct(int $quantity): bool
    {
        return ($this->countProductInCart() + $quantity) <= $this->limitProductsPerCart;
    }

    public function calcTotalCostCart()
    {
        return DB::table('cart_items')->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('user_id', $this->userId)
            ->sum('products.price');
    }

    public function getCartView()
    {
        return DB::table('cart_items')->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('user_id', $this->userId)
            ->groupBy('products.id', 'products.title', 'products.price')
            ->select('products.id as product_id', 'products.title as title', 'products.price as price',
                DB::raw('count(products.id )as quantity'),
                DB::raw('sum(products.price) as total') )
            ->get();
    }

    public function getFullCartView(){
        return [
            'products' => $this->getCartView(),
            'total_cost' => $this->calcTotalCostCart()
        ];
    }

    public function removeItemFromCart(){
        CartItem::where('product_id', $this->productId)
            ->where('user_id', $this->userId)
            ->delete();

    }

}
