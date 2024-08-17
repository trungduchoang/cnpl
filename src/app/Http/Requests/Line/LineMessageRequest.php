<?php

namespace App\Http\Requests\Line;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class LineMessageRequest extends FormRequest
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
        $rules = [
            'messages'       => 'required|array',
            'projectId'      => 'required'
        ];

        if ($this->has('bucket')) $rules['bucket'] = 'required';

        if ($this->has('userNamesPath')) $rules['userNamesPath'] = 'required';

        if ($this->has('userName')) $rules['userName'] = 'required';

        return $rules;
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
     * get query paramator function
     *
     * @return array
     */
    public function getParam(): array
    {
        return [$this->bucket, $this->userNamesPath, $this->userName, $this->messages, $this->projectId];
    }
}
