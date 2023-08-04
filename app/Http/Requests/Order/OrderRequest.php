<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'number' => 'required|string',
            'datestart' => 'required',
            'dateend' => 'required',
            'ipo' => 'required|string',
            'client' => 'required',
            'shipto' => 'required',
            'manager' => 'required',
            'delivery' => '',
            'comission' => '',
            'currency' => '',
            'terms' => '',
            'wd' => ''
        ];
    }
}
