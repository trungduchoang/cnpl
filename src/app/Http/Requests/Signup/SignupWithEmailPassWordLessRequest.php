<?php
namespace App\Http\Requests\Signup;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class SignupWithEmailPassWordLessRequest extends FormRequest
{

    private $signupUrl = 'api/auth/signup/email/less';
    private $signinUrl = 'api/auth/signin/email/less';

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
            'email' => 'required|email',
            'redirectUrl' => 'required|url',
            'projectId' => 'required'
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
        return [
            $this->email,
            $this->redirectUrl,
            $this->projectId,
            $this->cookie('TAPCM') ? $this->cookie('TAPCM') : $this->cookie('PLATEID_TAPCM'),
            $this->headers->get('X-Language'),
            $this->multiRecord
        ];
    }
}
