<?php

namespace App\Http\Requests\UserInfo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class UserInfoReqest extends FormRequest
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
            'type'      => ['required','regex:/(username|email|phone_number)/'],
            'attribute' => 'required'
        ];
    }


    /**
     * failed validation function
     *
     * @param Validator $validator
     * @return void
     */
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
     * get form param function
     *
     * @return array
     */
    public function getParam(): array
    {
        return [$this->type, $this->attribute];
    }
}
