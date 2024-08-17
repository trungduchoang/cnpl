<?php
namespace App\Repositories\Temp;

use App\Models\SigninTemp;
use Dflydev\DotAccessData\Data;

class SigninTempRepository implements SigninTempRepositoryInterface
{
    private $signinTemp;

    public function __construct(SigninTemp $signinTemp)
    {
        $this->signinTemp = $signinTemp;
    }

    public function createSession(array $data)
    {
        try {
            return $this->signinTemp->updateOrCreate(
                ['uid' => $data['uid']],
                [
                    'oauth_name'   => $data['userName'],
                    'project_id'   => $data['projectId'],
                    'redirect_url' => $data['redirectUrl'],
                    'session'      => $data['session'],
                    'secret_code'  => $data['secretCode']
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    public function getSession(string $uid)
    {
        try {
            return $this->signinTemp->where('uid', $uid)->first();
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    public function deleteSession(string $uid)
    {
        try {
            return $this->signinTemp->where('uid', $uid)->delete();
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }
}