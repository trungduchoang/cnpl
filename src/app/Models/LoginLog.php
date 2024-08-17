<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $table = 'login_log';
    protected $guarded = [''];
    const UPDATED_AT = null;

    public static function login($cookie, $userName)
    {
        try {
            LoginLog::create([
                'cookie' =>  $cookie,
                'oauth_username' => $userName,
                'kind' => 'login'
            ]);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    public static function logout($cookie)
    {
        try {
            LoginLog::create([
                'cookie' =>  $cookie,
                'oauth_username' => '',
                'kind' => 'logout'
            ]);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }
}
