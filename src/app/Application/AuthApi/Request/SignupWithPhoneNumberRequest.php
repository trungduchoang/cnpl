<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class SignupWithPhoneNumberRequest extends FormRequest
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
            'phoneNumber' => [
                'required',
                function ($attribute, $value, $fail) {
                    $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
                    $phoneNumber = $phoneNumberUtil->parse($value, 'JP');
                    if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                        $fail('The ' . $attribute . ' must be a valid' . $attribute . '.');
                    }
                },
            ],
            'projectId' => 'required',
            'redirectUrl' => 'required|url',
            'message' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            $message = [];
            foreach ($validator->errors()->messages() as $field => $value) {
                $message[$field] = $value[0];
            }
            throw new \Exception(json_encode($message, JSON_UNESCAPED_SLASHES), 400);
        }
    }



    /**
     * get input param function
     *
     * @param [type] $request
     * @return array
     */
    public function getParam($request)
    {
        $data = $request->only(['phoneNumber', 'projectId', 'redirectUrl', 'message']);
        $data['phoneNumber'] = $this->formatPhoneNumber($data['phoneNumber']);
        return [$data['phoneNumber'], $data['projectId'], $data['redirectUrl'], $data['message']];
    }


    private function formatPhoneNumber($phoneNumber)
    {
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse($phoneNumber, 'JP');
        return $phoneNumberUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
    }
}
