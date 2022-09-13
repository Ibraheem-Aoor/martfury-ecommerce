<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Support\Http\Requests\Request;
use MarketplaceHelper;

class VendorWithdrawalRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $fee = MarketplaceHelper::getSetting('fee_withdrawal', 0);

        return [
            'amount'      => 'required|numeric|min:1|max:' . (auth('customer')->user()->balance - $fee),
            'description' => 'nullable|max:400',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.max' => __('The balance is not enough for withdrawal'),
        ];
    }
}
