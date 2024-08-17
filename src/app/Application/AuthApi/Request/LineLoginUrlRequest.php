<?php

namespace App\Application\AuthApi\Request;

use Illuminate\Foundation\Http\FormRequest;

class LineLoginUrlRequest extends FormRequest
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
            'state' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        if (!$value['redirectUrl'] || !$value['projectId']) {
                            $fail($attribute . ' object is not valid');
                        }
                    } catch (\Exception $e) {
                        $fail($attribute . ' object is not valid');
                    }
                }
            ],
            'botPrompt'   => ['regex:/^normal|aggressive$/']
        ];
    }

    /**
     * get url params function
     *
     * @return array
     */
    public function getParams(): array
    {
        $data = $this->all();
        $state = $data['state'];
        return [isset($data['botPrompt']) ? $data['botPrompt']: null, $state];
    }
}
