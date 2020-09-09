<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CartItem extends Model
{
    protected $fillable = ['quantity', 'product_id', 'user_id'];
    private int $limitUnitsPerProduct = 10;
    private int $limitProductsPerCart = 3;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function product()
    {
        return $this->hasOne('App\Product');
    }

    public function countProductUnitsInCart(int $userId, int $productId)
    {
        return DB::table('cart_items')->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->count();
    }

    private function isAllowedAddUnitProduct(int $quantity): bool
    {
        return ($this->quantity + $quantity) <= $this->limitUnitsPerProduct;
    }

    private function isAllowedAddNewProduct(): bool
    {
        return ($this->countProductInCart() + 1) <= $this->limitProductsPerCart;
    }

    private function getCartItem()
    {
        return $this::where('product_id', $this->product_id)
            ->where('user_id', $this->user_id)
            ->first();
    }


    public function createNewOrIncreaseQuantity()
    {
        $cartItemFromDb = $this->getCartItem();
        if ($cartItemFromDb) {
            if ($this->isAllowedAddUnitProduct($cartItemFromDb->quantity)) {
                $cartItemFromDb->quantity += $this->quantity;
                $cartItemFromDb->save();
                return $cartItemFromDb;
            } else {
                throw new \Exception("You cannot add more than $this->limitUnitsPerProduct units per product.");
            }
        } else
            if ($this->isAllowedAddNewProduct()) {
                $this->save();
                return $this;
            } else {
                throw new \Exception("You cannot add more than $this->limitProductsPerCart product per cart.");
            }
    }


    private function countProductInCart()
    {
        return $this->where('user_id', $this->user_id)->count();
    }

    public static function getCartByUser(User $user)
    {
        return $user->cartItems()->get();
    }

    public static function calcTotalCost(User $user)
    {
        $query = DB::select("select sum(cart_items.quantity * products.price) as total
                                    from cart_items join products on cart_items.product_id = products.id
                                    where cart_items.user_id = :id", ['id' => $user->id]);

        return $query[0]->total;
    }


}
