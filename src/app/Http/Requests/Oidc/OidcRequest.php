<?php

namespace App\Http\Requests\Oidc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class OidcRequest extends FormRequest
{

    const SIGNUP_URL = 'api/auth/signup/openid-connect';
    const SIGNIN_URL = 'api/auth/signin/openid-connect';

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
            'type'        => 'required',
            'redirectUrl' => 'required|url',
            'projectId'   => 'required'
        ];
    }


    /**
     * get form param function
     *
     * @return array
     */
    public function getParam(): array
    {
        return [
            $this->type,
            $this->redirectUrl,
            $this->projectId,
            $this->cookie('TAPCM') ? $this->cookie('TAPCM') : $this->cookie('PLATEID_TAPCM'),
            $this->path() === self::SIGNUP_URL ? true : false
        ];
    }
}
