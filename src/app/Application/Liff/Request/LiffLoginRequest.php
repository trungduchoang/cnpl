<?php

namespace App\Application\Liff\Request;

use Illuminate\Foundation\Http\FormRequest;

class LiffLoginRequest extends FormRequest
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

        ];
    }

    /**
     * get url params function
     *
     * @return array
     */
    public function getParams(): array
    {
        $data = $this->only(['redirectUrl', 'projectId']);
        return [$data['redirectUrl'], $data['projectId']];
    }
}
