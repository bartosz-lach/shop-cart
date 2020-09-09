<?php

namespace App\Http\Resources;

use App\CartItem;
use GDebrauwer\Hateoas\Traits\HasLinks;
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
            'id' => $this->id,
            'userId' => $this->user_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'links' => [
                'self' => [
                  'type' => 'DELETE',
                  'href' => "http://localhost:8000/cart-items/$this->id"
                ],
                'product' => [
                    "type" => "GET",
                    "href" => "http://localhost:8000/products/$this->product_id",
                ]
            ],

        ];
    }
}
