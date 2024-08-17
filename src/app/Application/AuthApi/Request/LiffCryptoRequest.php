<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;

class LiffCryptoRequest extends FormRequest
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
            'accessToken' => 'required'
        ];
    }


    /**
     * get url params function
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->validated();
    }
}
