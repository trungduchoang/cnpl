<?php
namespace App\UseCases\Signin;

use App\Libraries\CryptoQueryUtilInterface;
use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\Ses\SesApiRepositoryInterface;
use App\Repositories\Temp\TempRepositoryInterface;
use Illuminate\Support\Facades\Storage;


class SigninWithEmailPassWordLessUseCase
{

    private $cognito;
    private $ses;
    private $tempRepository;
    private $cryptQueryUtil;
    private $loginRepository;

    public function __construct(
        CognitoApiRepositoryInterface $cognito,
        SesApiRepositoryInterface $ses,
        TempRepositoryInterface $tempRepository,
        CryptoQueryUtilInterface $cryptQueryUtil,
        LoginRepositoryInterface $loginRepository
    ) {
        $this->cognito = $cognito;
        $this->ses = $ses;
        $this->tempRepository = $tempRepository;
        $this->cryptQueryUtil = $cryptQueryUtil;
        $this->loginRepository = $loginRepository;
    }

    /**
     * signin with enail and password less business logic function
     *
     * @param array $params
     * @return array
     */
   public function index(array $params): array
   {
        try {
            list($email, $redirectUrl, $projectId, $multiRecord, $language) = $params;

            $userInfo = $this->cognito->getUserInfo("email = \"" . $email . "\"");
            if (count($userInfo['Users']) > 0) {
                $count = 0;
                foreach ($userInfo['Users'] as $value) {
                    $count++;
                    list($userName, $provider) = [$value['Username'], explode('_', $value['Username'])[0]];
                    if ($value['UserStatus'] === 'CONFIRMED' && $provider === 'email') {
                        // $userName = $value['Username'];
                        // $loginUser = $this->loginRepository->getUserData([
                        //     'userName'  => $userName,
                        //     'projectId' => $projectId
                        // ]);
                        // if (!$loginUser) {
                        //     break;
                        // } 
                        break;
                        
                    } elseif ($value['UserStatus'] === 'UNCONFIRMED' && $provider === 'email') {
                        throw new \Exception('User is not confirmed', 400);
                    } else {
                        if (count($userInfo['Users']) <= $count) throw new \Exception('User is not found', 400);
                        continue;
                    }
                }
            } else {
                throw new \Exception('User is not found', 400);
            }

            $response = $this->cognito->signin([
                'AuthFlow'       => 'CUSTOM_AUTH',
                'AuthParameters' => [
                    'USERNAME' => $userName,
                    'EMAIL'    => $email
                ],
            ]);

            $uid = uniqid();
            $url = request()->getSchemeAndHttpHost() . '/api/auth/signin/confirm?uid=' . $this->cryptQueryUtil->encryptQuery($uid);

            $session = json_encode([
                'cookie'      => explode('_', $response['ChallengeParameters']['USERNAME'])[1],
                'userName'    => $response['ChallengeParameters']['USERNAME'],
                'projectId'   => $projectId,
                'redirectUrl' => $redirectUrl,
                'session'     => $response['Session'],
                'multiRecord' => $multiRecord,
                'secretCode'  => $response['ChallengeParameters']['code']
            ]);
            logger('put file name sign in : ' . $uid . 'json');
            Storage::put('temp/' . $uid . '.json', $session);
            $this->tempRepository->createTemp($uid, $session);


            $language = $language ? $language : 'ja';
            $text = config('mail.signin.text.' . $language);
            $urlText = config('mail.signin.urlText.' . $language);


            $this->ses->sendEmail([
                'Destination' => [
                    'BccAddresses' => [$email],
                ],
                'Source' => 'login.service@smartplate.pro',
                'Message' => [
                  'Body' => [
                      'Html' => [
                          'Charset' => 'utf-8',
                          'Data' => view('emails/signin_confirm', ['url' => $url, 'text' => $text, 'urlText' => $urlText])->render(),
                      ],
                  ],
                  'Subject' => [
                      'Charset' => 'utf-8',
                      'Data' => 'Verification URL (認証URL)',
                  ],
                ],
            ]);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return [
            'email' => $email,
        ];
   }
}