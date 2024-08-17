<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;

class LineLoginCallbackRequest extends FormRequest
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
        $error = $this->only(['error']);
        $rules = [
            'state' => [
                'required',
                function ($attribute, $value, $fail) use ($error) {
                    try {
                        $value = json_decode($value, true);
                        if ($error) {
                            if (!$value['redirectUrlErr'] || !$value['projectId']) {
                                $fail($attribute . ' object is not valid');
                            }
                        } else {
                            if (!$value['redirectUrl'] || !$value['projectId']) {
                                $fail($attribute . ' object is not valid');
                            }
                        }
                    } catch (\Exception $e) {
                        $fail($attribute . ' object is not valid');
                    }
                }
            ]
        ];
        if (!$error) {
            $rules['code'] = 'required';
        }
        return $rules;
    }


    /**
     * get query paramator function
     *
     * @return array
     */
    public function getParam(): array
    {
        $data = $this->only(['code', 'state', 'error']);
        $state = json_decode($data['state'], true);
        return [array_key_exists('error', $data) ? $data['error']: null, $state, array_key_exists('code', $data) ? $data['code']: null];
    }
}
