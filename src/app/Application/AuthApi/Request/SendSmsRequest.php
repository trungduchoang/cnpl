<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class SendSmsRequest extends FormRequest
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
            'userName' => 'nullable',
            'phoneNumber' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
                    $phoneNumber = $phoneNumberUtil->parse($value, 'JP');
                    if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                        $fail('The ' . $attribute . ' must be a valid' . $attribute . '.');
                    }
                },
            ],
            'user' => 'required_without_all:userName,phoneNumber',
            'message' => 'required',
            'subject' => 'nullable|regex:/^[a-zA-Z0-9-]+$/|min:1|max:11',
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
        $data = $request->only(['userName' ,'phoneNumber', 'message', 'subject']);
        $data['phoneNumber'] = $data['phoneNumber'] ? $this->formatPhoneNumber($data['phoneNumber']) : null;
        return [$data['userName'], $data['phoneNumber'], $data['message'], $data['subject'] ? $data['subject'] : 'SmartPlate'];
    }

    private function formatPhoneNumber($phoneNumber)
    {
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse($phoneNumber, 'JP');
        return $phoneNumberUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
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
}
