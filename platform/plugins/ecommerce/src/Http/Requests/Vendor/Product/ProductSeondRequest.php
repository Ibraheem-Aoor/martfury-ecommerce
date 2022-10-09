<?php

namespace Botble\Ecommerce\Http\Requests\Vendor\Product;

use Botble\Support\Http\Requests\Request;

class ProductSeondRequest extends Request
{
    public function rules()
    {
        return [
            'price'      => 'numeric|required|min:1|max:100000000',
            'delivery_time' => 'required' ,
            'quantity'   => 'numeric|required|min:1|max:100000000',
            'content' => 'required',
            'deliverables' => 'nullable',
        ];
    }
}
