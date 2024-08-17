<?php

namespace App\Http\Requests\Confirm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Libraries\CryptoQueryUtilInterface;


class ConfirmSigninRequest extends FormRequest
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
            'uid' => [
                'required',
                function ($attribute, $value, $fail) {
                    $value = $this->decrypt($value);
                    return $value ? true : $fail('The ' . $attribute . ' must be a valid' . $attribute . '.');
                },
            ]
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
        $uid = $this->decrypt($this->uid);
        return [
            $uid,
            $this->cookie('TAPCM') ? $this->cookie('TAPCM') : $this->cookie('PLATEID_TAPCM'),
            $this->ip(),
            $this->header('User-Agent')
        ];
    }


    protected function decrypt($encrypted)
    {
        try {
            $encrypted = strtr($encrypted, '-_', '+/');
            $decrypted = app()->make(CryptoQueryUtilInterface::class)->decryptQuery($encrypted);
        } catch (\Exception $e) {
            throw new \Exception('validate error', 400);
        }
        return trim($decrypted);
    }
}
