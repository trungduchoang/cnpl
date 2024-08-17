<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Services\CryptoQueryService;



class ConfirmRequest extends FormRequest
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
                    $value = $this->decrypt($value);
                    return $value ? true : $fail('The ' . $attribute . ' must be a valid' . $attribute . '.');
                },
            ]
        ];
    }

    public function getParam($request)
    {
        $data = $request->only(['uid', 'code']);
        $uid = $this->decrypt($data['uid']);
        return [$uid, $data['code']];
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
