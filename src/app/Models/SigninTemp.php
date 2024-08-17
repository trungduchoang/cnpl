<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigninTemp extends Model
{
    use HasFactory;

    protected $table = 'signin_temp';
    protected $guarded = [''];
    protected $primaryKey = 'oauth_name';
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;



    /**
     * uid accessor function
     *
     * @param string $value
     * @return string
     */
    public function getUidAttribute(string $value): string
    {
        return $value;
    }

    /**
     * oauth name accessor function
     *
     * @param string $value
     * @return string
     */
    public function getOauthNameAttribute(string $value): string
    {
        return $value;
    }


    /**
     * project id accessor function
     *
     * @param string $value
     * @return string
     */
    public function getProjectIdAttribute(string $value): string
    {
        return $value;
    }


    /**
     * session accessor function
     *
     * @param string $value
     * @return string
     */
    public function getSessionAttribute(string $value): string
    {
        return $value;
    }


    /**
     * redirect url accessor function
     *
     * @param string $value
     * @return string
     */
    public function getRedirectUrlAttribute(string $value): string
    {
        return $value;
    }


    /**
     * secret code accessor function
     *
     * @param string $value
     * @return string
     */
    public function getSecretCodeAttribute(string $value): string
    {
        return $value;
    }

    public static function createSession($uid, $userName, $projectId, $redirectUrl, $session, $secretLoginCode)
    {
        try {
            return SigninTemp::updateOrCreate(['uid' => $uid], ['oauth_name' => $userName, 'project_id' => $projectId, 'redirect_url' => $redirectUrl, 'session' => $session, 'secret_code' => $secretLoginCode]);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    public static function getSession($uid)
    {
        try {
            $data = SigninTemp::where('uid', $uid)->first();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
        return $data;
    }

    public static function deleteSession($userName)
    {
        try {
            SigninTemp::where('oauth_name', $userName)->delete();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }
}
