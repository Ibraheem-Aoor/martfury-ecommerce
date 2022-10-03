<?php

namespace Botble\Ecommerce\Http\Requests\Vendor\Product;

use Botble\Support\Http\Requests\Request;

class ProductFirstStepRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'parent_id' => 'required',
            'sub_1_id'  => 'sometimes',
            'sub_2_id' => 'sometimes',
        ];
    }
}
