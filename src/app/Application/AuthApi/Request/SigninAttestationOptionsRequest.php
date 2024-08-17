<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class SigninAttestationOptionsRequest extends FormRequest
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
            'origin'    => 'required',
            'projectId' => 'required'
        ];
    }

    /**
     * get input param function
     *
     * @param [type] $request
     * @return array
     */
    public function getParam($request)
    {
        $data = $request->only(['origin', 'projectId']);
        return [$data['origin'], $data['projectId']];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $message = [];
            foreach ($validator->errors()->messages() as $field => $value) {
                $message[$field] = $value[0];
            }
            throw new HttpResponseException(response()->json([
                'error' => [
                    'message'    => $message,
                    'statusCode' => 422
                ]
            ], 422));
        }
    }
}
