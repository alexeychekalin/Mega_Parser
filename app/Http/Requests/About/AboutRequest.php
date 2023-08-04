<?php

namespace App\Http\Requests\About;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
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
            'name' => 'required|string',
            'address' => '',
            'email' => '',
            'phone' => '',
            'licence' => '',
            'inn' => '',
            'code' => '',
            'bank' => '',
            'baddress' => '',
            'iban' => '',
            'swift' => '',
            'branch' => '',
            'accountNumber' => ''
        ];
    }
}
