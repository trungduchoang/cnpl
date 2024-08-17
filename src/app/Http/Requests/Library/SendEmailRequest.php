<?php

namespace App\Http\Requests\Library;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class SendEmailRequest extends FormRequest
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
            'bucket'                    => 'required',
            'emailPath'                 => 'required',
            'cookieListPath'            => 'required',
            'exclusionDomainListPath'   => 'required',
            'projectId'                 => 'required',
            'subject'                   => 'required'
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
     * get input param function
     *
     * @param [type] $request
     * @return array
     */
    public function getParam()
    {
        return [
            $this->bucket,
            $this->emailPath,
            $this->cookieListPath,
            $this->exclusionDomainListPath,
            $this->projectId,
            $this->subject
        ];
    }
}
