<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'firstname'       => 'required|string|max:100',
            'lastname'        => 'required|string|max:100',
            'mobile'          => 'required|string|max:50',
            'email'           => 'required|email',
            'country'         => 'required|string|max:100',
            'address'         => 'required|string|max:500',
            'address_2'       => 'nullable|string|max:500',
            'state'           => 'nullable|string|max:100',
            'city'            => 'required|string|max:100',
            'zip'             => 'required|string|max:20',
            'shipping_method' => 'required|integer|exists:shipping_methods,id',
            'payment_type'    => 'required|integer|in:1,2',
        ];
    }
}
