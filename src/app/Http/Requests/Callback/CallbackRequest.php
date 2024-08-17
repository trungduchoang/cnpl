<?php

namespace App\Http\Requests\Callback;

use Illuminate\Foundation\Http\FormRequest;

class CallbackRequest extends FormRequest
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
                function ($attribute, $value, $fail) {
                    try {
                        $value = json_decode($value, true);
                        if (!$value['redirect_uri'] || !$value['project_id']) {
                            $fail($attribute . ' object is not valid');
                        }
                    } catch (\Exception $e) {
                        $fail($attribute . ' object is not valid');
                    }
                }
            ]
        ];
    }


    /**
     * get query paramator function
     *
     * @return array
     */
    public function getParam(): array
    {
        $state = json_decode($this->state);
        return [
            $state->redirect_uri,
            $state->project_id,
            $this->code,
            $this->error,
            $this->cookie('TAPCM') ? $this->cookie('TAPCM') : $this->cookie('PLATEID_TAPCM'),
            $this->ip(),
            $this->userAgent()
        ];
    }
}
