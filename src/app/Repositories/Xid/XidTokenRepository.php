<?php

namespace App\Repositories\Xid;

use App\Models\XidToken;



class XidTokenRepository implements XidTokenRepositoryInterface
{

    private $xidToken;

    public function __construct(XidToken $xidToken)
    {
        $this->xidToken = $xidToken;
    }


    /**
     * create token record function
     *
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        try {
            $this->xidToken->updateOrCreate(
                ['cookie' => $data['cookie']],
                [
                    'access_token'  => $data['accessToken'],
                    'project_id'    => $data['projectId'],
                    'created_at'    => $data['createdAt']
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    
    /**
     * get token function
     *
     * @param string $cookie
     * @param integer $projectId
     * @return object
     */
    public function get(string $cookie, int $projectId): object
    {
        try {
            return $this->xidToken->where('cookie', $cookie)->where('project_id', $projectId)->first();
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }
}