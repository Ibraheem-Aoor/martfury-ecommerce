<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ProducttUpdatePriceInTableRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $update_rules = [
            'value' => 'required|numeric',
        ];
        if ($this->input == 'ean')
        {
            $update_rules['value'] .= '|digits:13';
        }
        return $update_rules;
    }
}
