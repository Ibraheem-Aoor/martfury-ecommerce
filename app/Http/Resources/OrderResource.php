<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'api_id' => $this->id,
            'code' => get_order_code($this->id),
            'basic_attributes' => [
                "user_id" => $this->user_id,
                "shipping_option" => $this->shipping_option,
                "shipping_method" => $this->getShippingMethodNameAttribute(),
                "status" => $this->status,
                "amount" => $this->amount,
                "tax_amount" => $this->tax_amount,
                "shipping_amount" => $this->shipping_amount,
                "description" => $this->description,
                "coupon_code" =>  $this->coupon_code,
                "discount_amount" => $this->discount_amount,
                "sub_total" => $this->sub_total,
                "is_confirmed" => $this->is_confirmed,
                "discount_description" => $this->discount_description,
                "is_finished" => $this->is_finished,
                "token" => $this->token,
                "payment_id" => $this->payment_id,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at,
                "store_id" => $this->store_id,
                "shipping_company_name" => $this->shipping_company_name,
                "shipping_tracking_id" => $this->shipping_tracking_id,
                "shipping_tracking_link" => $this->shipping_tracking_link,
                "estimate_arrival_date" => $this->estimate_arrival_date,
            ],
            'order_items' => ProductResource::collection($this->whenLoaded('products')),
            'shipment' => $this->whenLoaded('shipment'),
            'address' => $this->whenLoaded('address'),
            'payment' => $this->whenLoaded('payment'),
        ];
    }
}
