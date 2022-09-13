<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Ecommerce\Http\Requests\ProductRequest as BaseProductRequest;

class ProductRequest extends BaseProductRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules() + ['image_input' => 'image|mimes:jpg,jpeg,png'];
    }
}
