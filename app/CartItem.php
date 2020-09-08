<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CartItem extends Model
{
    protected $fillable = ['quantity', 'product_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function product()
    {
        return $this->hasOne('App\Product');
    }

    public function countProductUnitsInCart(int $user_id, int $product_id){
        return DB::table('cart_items')->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->count();
    }


}
