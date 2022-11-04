<?php

namespace App\Http\Resources;

use Botble\Media\RvMedia;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "api_id" => $this->id,
            "api_order_id" => $this->order_id,
            "qty" => $this->qty,
            "ean_code" => $this->product->ean_code,
            "image" =>  \RvMedia::getImageUrl($this->product->image),
            "price" => $this->price,
            "tax_amount" => $this->tax_amount,
            "options" => $this->options,
            "product_id" => $this->product_id,
            "product_name" => $this->product_name,
            "weight" => $this->weight,
            "restock_quantity" => $this->restock_quantity,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
