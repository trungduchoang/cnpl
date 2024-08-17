<?php
namespace App\UseCases\UserInfo;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;

class UserInfoUseCase
{
    private $cognitoApiRepository;

    public function __construct(CognitoApiRepositoryInterface $cognitoApiRepository)
    {
        $this->cognitoApiRepository = $cognitoApiRepository;
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return array
     */
    public function index(array $params): array
    {

        try {
            list($type, $attribute) = $params;
            
            $cognitoUsers = $this->cognitoApiRepository->getUserInfo($type . " = \"" . $attribute . "\"")['Users'];

            if (!$cognitoUsers) throw new \Exception('User is not find', 400);

            $userData = [];
            foreach ($cognitoUsers as $cognitoUser) {
                $user = [];
                $typeCognito = explode('_', $cognitoUser['Username'])[0];
                $user['username'] = $cognitoUser['Username'];
                foreach ($cognitoUser['Attributes'] as $attribute) {
                    $user[$attribute['Name']] = $attribute['Value'];
                }
                $userData = $user;
                if ($type === $typeCognito) break;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return $userData;
    }
}