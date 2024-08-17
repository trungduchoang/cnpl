<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\CryptoQueryService;
use Illuminate\Contracts\Session\Session;

class EmailVerifyRequest extends FormRequest
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
            'code'  => 'required',
            'uid' => [
                'required',
                function ($attribute, $value, $fail) {
                    $query = $this->decrypt($value);
                },
            ]
        ];
    }

    public function getParam()
    {
        $uid = $this->decrypt($this->uid);
        return [$uid, $this->code, $this->ip(), $this->userAgent()];
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

    protected function decrypt($encrypted)
    {
        try {
            $encrypted = strtr($encrypted, '-_', '+/');
            $decrypted = app()->make(CryptoQueryService::class)->decryptQuery($encrypted);
        } catch (\Exception $e) {
            throw new \Exception('validate error', 400);
        }
        return trim($decrypted);
    }
}
