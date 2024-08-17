<?php

namespace App\Application\Redirector\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class RedirectorRequest extends FormRequest
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
            'redirectUrl' => 'required|url'
        ];
    }


    public function getParam($reqest)
    {
        $data = $reqest->only(['redirectUrl']);
        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \Exception('redirectUrl field is required', 403);
    }
}
