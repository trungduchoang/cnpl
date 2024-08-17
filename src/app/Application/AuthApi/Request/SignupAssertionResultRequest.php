<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\Base64UrlService;
use App\CBOR\CBOREncoder;
use Illuminate\Http\Exceptions\HttpResponseException;


class SignupAssertionResultRequest extends FormRequest
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
            'clientDataJson' => 'required',
            'attestationObject' => 'required',
            'projectId' => 'required',
            'cognito' => 'required|boolean'
        ];
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

    /**
     * get request param function
     *
     * @param object $reqest
     * @return array
     */
    public function getParam(object $reqest): array
    {
        $data = $reqest->only(['id', 'clientDataJson', 'attestationObject', 'projectId', 'cognito']);
        return [$data['id'], $data['clientDataJson'], $data['attestationObject'], $data['projectId'], $data['cognito']];
    }
}
