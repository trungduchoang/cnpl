<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Location;
use Illuminate\Support\Facades\DB;

class Login extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'login';
    protected $guarded = [''];
    protected $primaryKey = 'cookie';
    const UPDATED_AT = 'last_login';


    /**
     * cookie accessor function
     *
     * @param string $value
     * @return string
     */
    public function getCookieAttribute(string $value): string
    {
        return $value;
    }

    /**
     * oauth_username accessor function
     *
     * @param string $value
     * @return string
     */
    public function getOauthUsernameAttribute(string $value): string
    {
        return $value;
    }

    /**
     * oauth_username accessor function
     *
     * @param string $value
     * @return string
     */
    public function getExpireDateAttribute(string $value): string
    {
        return $value;
    }

    public static function login($cookie, $userName, $projectId)
    {
        try {
            if (Login::where('oauth_username', $userName)->where('team_id', $projectId)->exists()) {
                Login::where('oauth_username', $userName)->where('team_id', $projectId)->update([]);
                $oldCookie = Login::where('oauth_username', $userName)->where('team_id', $projectId)->first('cookie')->attributes['cookie'];
                return $cookie == $oldCookie ? null : $oldCookie;
            }
            if (Login::where('cookie', $cookie)->where('team_id', $projectId)->exists()) {
                Login::where('cookie', $cookie)->where('team_id', $projectId)->update(['oauth_username' => $userName]);
                return;
            }
            Login::create(['cookie' => $cookie, 'team_id' => $projectId, 'oauth_username' => $userName]);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    public static function isLogin($cookie, $projectId)
    {
        try {
            $check = Login::where('cookie', $cookie)->where(['team_id' => $projectId])->exists();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
        return $check;
    }

    public static function logout($cookie)
    {
        try {
            Login::where('cookie', $cookie)->delete();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }
}
