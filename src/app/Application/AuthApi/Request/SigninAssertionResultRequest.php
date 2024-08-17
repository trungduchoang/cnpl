<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;

class SigninAssertionResultRequest extends FormRequest
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
            'id' => 'required',
            'clientDataJson' => 'required|array',
            'authenticatorData' => 'required|array',
            'signature' => 'required|array',
            'projectId' => 'required',
            'cognito'   => 'required|boolean'
        ];
    }


    /**
     * get request param function
     *
     * @param object $reqest
     * @return array
     */
    public function getParam(object $reqest): array
    {
        $data = $reqest->only(['id', 'clientDataJson', 'authenticatorData', 'signature', 'projectId', 'cognito']);
        return [$data['id'], $data['clientDataJson'], $data['authenticatorData'], $data['signature'], $data['projectId'], $data['cognito']];
    }
}
