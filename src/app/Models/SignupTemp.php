<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignupTemp extends Model
{
    use HasFactory;


    protected $table = 'signup_temp';
    protected $guarded = [''];
    protected $primaryKey = 'uid';
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;


    public static function createTemp($uid, $userName, $redirectUrl, $projectId)
    {
        try {
            SignupTemp::updateOrCreate(['uid' => $uid], ['oauth_name' => $userName, 'redirect_url' => $redirectUrl, 'project_id' => $projectId]);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    public static function getTemp($uid)
    {
        try {
            $data = SignupTemp::where('uid', $uid)->first();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
        return $data ? $data->attributes : null;
    }

    public static function deleteTemp($uid)
    {
        try {
            SignupTemp::where('uid', $uid)->delete();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }
}
