<?php

namespace App\Repositories\Login;

use App\Entities\Login\LoginEntity;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Models\Login;

class LoginRepository implements LoginRepositoryInterface
{

    public function isLogin(array $data): bool
    {
        try {
            return Login::where('cookie', $data['cookie'])
                            ->where('team_id', $data['projectId'])
                            ->exists();
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    public function login(array $data)
    {

        try {
            if (Login::where('oauth_username', $data['userName'])->where('team_id', $data['projectId'])->exists()) {
                Login::where('oauth_username', $data['userName'])
                            ->where('team_id', $data['projectId'])
                            ->update([
                                'ip'             => array_key_exists('ip', $data) ? $data['ip'] : null,
                                'user_agent'     => array_key_exists('userAgent', $data) ? $data['userAgent']: null
                            ]);
                $oldCookie = Login::where('oauth_username', $data['userName'])->where('team_id', $data['projectId'])->first('cookie')->cookie;
                return $data['cookie'] == $oldCookie ? null : $oldCookie;
            }
            if (Login::where('cookie', $data['cookie'])->where('team_id', $data['projectId'])->exists()) {
                Login::where('cookie', $data['cookie'])
                            ->where('team_id', $data['projectId'])
                            ->update([
                                'oauth_username' => $data['userName'],
                                'ip'             => array_key_exists('ip', $data) ? $data['ip'] : null,
                                'user_agent'     => array_key_exists('userAgent', $data) ? $data['userAgent']: null
                            ]);
                return;
            }
            Login::create([
                'cookie'         => $data['cookie'], 
                'team_id'        => $data['projectId'],
                'oauth_username' => $data['userName'],
                'ip'             => array_key_exists('ip', $data) ? $data['ip'] : null,
                'user_agent'     => array_key_exists('userAgent', $data) ? $data['userAgent']: null
            ]);
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }


    /**
     * create record function
     *
     * @param array $data
     * @return LoginEntity
     */
    public function createOrUpdate(array $data): LoginEntity
    {
        try {
            $existsCheck = Login::where('oauth_username', $data['userName'])
                                ->where('team_id', $data['projectId'])
                                ->exists();
            if ($existsCheck) {
                Login::where('oauth_username', $data['userName'])
                    ->where('team_id', $data['projectId'])
                    ->update([
                        'expire_date'    => array_key_exists('expireDate', $data) ? $data['expireDate'] : null,
                        'ip'             => array_key_exists('ip', $data) ? $data['ip'] : null,
                        'user_agent'     => array_key_exists('userAgent', $data) ? $data['userAgent']: null
                    ]);
                
                $result = Login::where('oauth_username', $data['userName'])
                            ->where('team_id', $data['projectId'])
                            ->first();
                            
            } else {
                $result = Login::create([
                    'cookie'         => $data['cookie'], 
                    'team_id'        => $data['projectId'],
                    'oauth_username' => $data['userName'],
                    'expire_date'    => array_key_exists('expireDate', $data) ? $data['expireDate'] : null,
                    'ip'             => array_key_exists('ip', $data) ? $data['ip'] : null,
                    'user_agent'     => array_key_exists('userAgent', $data) ? $data['userAgent']: null
                ]);
            }
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
        return new LoginEntity(
            $result->cookie,
            $result->oauth_username,
            $result->last_login,
            $result->team_id,
            $result->created_at,
            $result->ip,
            $result->user_agent
        );
    }


    public function getUserData(array $data)
    {
        $query = new Login();
        if (array_key_exists('cookie', $data)) {
            $query = $query->where('cookie', $data['cookie']);
        }
        if (array_key_exists('userName', $data)) {
            $query = $query->where('oauth_username', $data['userName']);
        }
        if (array_key_exists('projectId', $data)) {
            $query = $query->where('team_id', $data['projectId']);
        }
        return $query->orderBy('last_login', 'DESC')->first();
    }


    public function getUsers(array $cookies, int $projectId): array
    {
        try {
            $userList = [];
            $query = Login::query();
            foreach ($cookies as $key => $value) {
                if ($key === 0) {
                    $query->where('team_id', $projectId)->where('cookie', $value);
                } else {
                    $query->orWhere(function ($query) use ($value, $projectId) {
                        $query->where('cookie', $value)->where('team_id', $projectId);
                    });
                }
            }
            $userNames = $query->get('oauth_username');
            $counter = 0;
            foreach ($userNames as $userName) {
                $userList[$counter] = $userName->oauth_username;
                $counter++;
            }
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
        return $userList;
    }

}