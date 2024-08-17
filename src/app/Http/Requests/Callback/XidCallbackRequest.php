<?php

namespace App\Http\Requests\Callback;

use Illuminate\Foundation\Http\FormRequest;

class XidCallbackRequest extends FormRequest
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
            $this->code,
            $this->error,
            $this->state,
            $tapcm ? $tapcm : $plateidTapcm,
            $this->ip(),
            $this->header('User-Agent')
        ];
    }
}
