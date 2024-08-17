<?php

namespace App\Application\Liff\Request;

use Illuminate\Foundation\Http\FormRequest;

class LiffRedirectRequest extends FormRequest
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

        $rules = [
            'projectId' => 'required',
            'accessToken' => 'required',
            'redirectUrl' => 'url',
            'notFriendUrl' => 'url'
        ];
        return $rules;
    }


    /**
     * get url params function
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->all();
    }
}
