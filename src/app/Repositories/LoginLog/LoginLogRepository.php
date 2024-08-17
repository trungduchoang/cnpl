<?php
namespace App\Repositories\LoginLog;

use App\Models\LoginLog;

class LoginLogRepository implements LoginLogRepositoryInterface
{

    private $loginLog;

    public function __construct(LoginLog $loginLog)
    {
        $this->loginLog = $loginLog;
    }

    /**
     * create login log function
     *
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        try {
            $this->loginLog->create([
                'cookie'         => $data['cookie'],
                'oauth_username' => $data['userName'],
                'kind'           => 'login'
            ]);
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }
}