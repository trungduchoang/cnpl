<?php
namespace App\Repositories\Temp;

use App\Models\SignupTemp;

class SignupTempRepository implements SignupTempRepositoryInterface
{
    private $signupTemp;

    public function __construct(SignupTemp $signupTemp)
    {
        $this->signupTemp = $signupTemp;
    }

    public function createTemp(array $data): void
    {
        try {
            $this->signupTemp->updateOrCreate(
                ['uid' => $data['uid']],
                ['oauth_name' => $data['userName'], 'redirect_url' => $data['redirectUrl'], 'project_id' => $data['projectId']]
            );
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
        
    }

    public function getTemp(string $uid)
    {
        
    }

    public function deleteTemp(string $uid): void
    {
        
    }
}