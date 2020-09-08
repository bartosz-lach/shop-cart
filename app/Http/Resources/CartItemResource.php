<?php

namespace App\Http\Resources;

use App\CartItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'userId' => $this->user_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,

        ];
    }
}
