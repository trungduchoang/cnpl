<?php
namespace App\UseCases\IsLogin;

use App\Repositories\Login\LoginRepositoryInterface;
use DateTime;

class IsLoginUseCase
{
    private $login;

    public function __construct(LoginRepositoryInterface $login)
    {
        $this->login = $login;
    }

    /**
     * is login usecase function
     *
     * @param array $data
     * @return array
     */
    public function index(array $data): array
    {
        try {
            list($projectId, $cookie) = $data;
            if (!$cookie) throw new \Exception('cookie is not found', 400);

            $userData = $this->login->getUserData([
                'cookie'    => $cookie,
                'projectId' => $projectId
            ]);

            if (!$userData) throw new \Exception('User is not found', 400);

            $responseData = [
                'islogin'    => true,
                'userName'   => $userData->oauth_username,
                'expireDate' => $userData->expire_date
            ];
            $currentDate = new DateTime();
            $currentDate = $currentDate->format('Y-m-d H:i:s');
            if ($userData->expire_date <= $currentDate) {
                $responseData['islogin'] = false;
            }
            

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $responseData;
    }
}