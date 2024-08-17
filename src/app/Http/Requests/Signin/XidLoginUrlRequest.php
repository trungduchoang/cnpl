<?php

namespace App\Http\Requests\Signin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class XidLoginUrlRequest extends FormRequest
{

    const DEV_URL = 'api/auth/auth-url/xid-develop';
    const PROD_URL = 'api/auth/auth-url/xid';

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
            'redirectUrl' => 'required|url',
            'projectId'   => 'required'
        ];
    }

    /**
     * get input param function
     *
     * @param [type] $request
     * @return array
     */
    public function getParam()
    {
        $tapcm = $this->cookie('TAPCM');
        $plateidTapcm = $this->cookie('PLATEID_TAPCM');
        if ($tapcm && $plateidTapcm) {
            if ($tapcm !== $plateidTapcm) $tapcm = $plateidTapcm;
        }
        return [
            $this->redirectUrl,
            $this->projectId,
            $tapcm ? $tapcm : $plateidTapcm,
            $this->path() === self::PROD_URL ? true : false
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
}
