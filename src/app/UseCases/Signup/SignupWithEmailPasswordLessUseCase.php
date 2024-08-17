<?php
namespace App\UseCases\Signup;


use App\Libraries\CryptoQueryUtilInterface;
use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\Temp\TempRepositoryInterface;
use App\Services\Cookie\CookieServiceInterface;
use App\UseCases\Signin\SigninWithEmailPassWordLessUseCase;
use Illuminate\Support\Facades\Storage;

class SignupWithEmailPasswordLessUseCase
{


    private $signinUsecase;
    private $login;
    private $cognito;
    private $tempRepository;
    private $cryptQueryUtil;
    private $cookieService;

    public function __construct(
        SigninWithEmailPassWordLessUseCase $signinUsecase,
        LoginRepositoryInterface $login,
        CognitoApiRepositoryInterface $cognito,
        TempRepositoryInterface $tempRepository,
        CryptoQueryUtilInterface $cryptQueryUtil,
        CookieServiceInterface $cookieService
    ) {
        $this->signinUsecase = $signinUsecase;
        $this->login = $login;
        $this->cognito = $cognito;
        $this->tempRepository = $tempRepository;
        $this->cryptQueryUtil = $cryptQueryUtil;
        $this->cookieService = $cookieService;
    }


    public function index(array $params)
    {
        try {

            list ($email, $redirectUrl, $projectId, $cookie, $language, $multiRecord) = $params;

            if (!$cookie) $cookie = $this->cookieService->generateCookie();

            $userInfo = $this->cognito->getUserInfo("email = \"" . $email . "\"");
            $confirmed = false;
            if (count($userInfo['Users']) > 0) {
                $count = 0;
                foreach ($userInfo['Users'] as $user) {
                    $count++;
                    list($userName, $provider) = [$user['Username'], explode('_', $user['Username'])[0]];
                    $confirmed = $user['UserStatus'];
                    if ($confirmed === 'CONFIRMED' && $provider === 'email') {
                        $loginUser = $this->login->getUserData([
                            'userName'  => $userName,
                            'projectId' => $projectId
                        ]);
                        if (!$loginUser && !$multiRecord) {
                            return $this->signinUsecase->index([$email, $redirectUrl, $projectId, $multiRecord, $language]);
                            continue;
                        } elseif ($loginUser && $multiRecord) {
                            throw new \Exception('User already exists', 400);
                        }
                        if (!$multiRecord) return $this->signinUsecase->index([$email, $redirectUrl, $projectId, $multiRecord, $language]);
                        break;
                    } elseif ($confirmed === 'UNCONFIRMED' && $provider === 'email') {
                        throw new \Exception('User is not confirmed', 400);
                    } else {
                        if (count($userInfo['Users']) <= $count) break;
                    }
                }
            }

            $password = sha1(uniqid(mt_rand(), true));
            $uid = uniqid();
            $userName = 'email_' . $cookie;

            $session = json_encode([
                'cookie'      => $cookie,
                'userName'    => $userName,
                'redirectUrl' => $redirectUrl,
                'projectId'   => $projectId,
                'multiRecord' => $multiRecord
            ]);
            logger('put file name sign up : ' . $uid . 'json');

            Storage::put('temp/' . $uid . '.json', $session);
            $this->tempRepository->createTemp($uid, $session);

            $enqryptedUid = $this->cryptQueryUtil->encryptQuery($uid);


            $this->cognito->signup([
                'Username'       => $userName,
                'Password'       => $password,
                'UserAttributes' => [
                    [
                        'Name'  => 'email',
                        'Value' => $email
                    ]
                ],
                'ClientMetadata' => [
                    'callbackUrl' => request()->getSchemeAndHttpHost() . '/api/auth/verify-email/?uid='. $enqryptedUid,
                    'redirectUrl' => $redirectUrl,
                    'language'    => $language
                ]
            ]);
            setcookie('TAPCM', $cookie, 0x7f000000, '/');
            setcookie('PLATEID_TAPCM', $cookie, 0x7f000000, '/', 'plate.id');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return [
            'email' => $email,
        ];
    }
}