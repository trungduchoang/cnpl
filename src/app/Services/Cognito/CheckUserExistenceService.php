<?php
namespace App\Services\Cognito;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;

class CheckUserExistenceService implements CheckUserExistenceServiceInterface
{

    private $cognito;


    public function __construct(CognitoApiRepositoryInterface $cognito)
    {
        $this->cognito = $cognito;
    }


    public function checkUserExistence(string $type, string $attribute): array
    {
        $userName = '';
        $userInfo = $this->cognito->getUserInfo($type . " = \"" . $attribute . "\"");
        if (count($userInfo['Users']) <= 0) {
            return [
                'attribute'     => $attribute,
                'confirmed'     => false,
                'existence'     => false
            ];
        }

        foreach ($userInfo['Users'] as $value) {
            list($userName, $provider) = [$value['Username'], explode('_', $value['Username'])[0]];
            if ($value['UserStatus'] === 'CONFIRMED' && $provider === $type) {
                $userName = $value['Username'];
                break;
            } 
        }

        if (!$userName) {
            return [
                'attribute'     => $attribute,
                'confirmed'     => false,
                'existence'     => true
            ];
        }
        
        return [
            'attribute'     => $attribute,
            'confirmed'     => true,
            'existence'     => true
        ];
    }
}