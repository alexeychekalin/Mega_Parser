<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'Type' => '',
            'ClassName' => 'required|string',
            'PurchasePrice' => '',
            'Bonus' => '',
            'CardCash' => '',
            'Monitor' => '',
            'SellPrice' => '',
            'parseDate' => ''
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'parseDate' => date('Y-m-d H:i:s',)
        ]);
    }
}
