<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'orders.*.part' => 'required',
            'orders.*.provider' => 'required',
            'orders.*.quantity' => 'required',
            'orders.*.price' => 'required',
            'orders.*.order_number' => 'required'
        ];
    }
}
