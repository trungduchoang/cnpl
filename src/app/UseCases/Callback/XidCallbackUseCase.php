<?php
namespace App\UseCases\Callback;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\LoginLog\LoginLogRepositoryInterface;
use App\Repositories\S3\S3ApiRepositoryInterface;
use App\Repositories\Xid\XidApiRepositoryInterface;
use App\Repositories\Xid\XidTempRepositoryInterface;
use App\Repositories\Xid\XidTokenRepositoryInterface;
use App\Services\Cognito\CheckUserExistenceServiceInterface;
use Illuminate\Support\Facades\DB;

class XidCallbackUseCase
{

    private $cognito;
    private $s3;
    private $xidApi;
    private $xidTemp;
    private $xidToken;
    private $login;
    private $loginLog;
    private $checkExistence;

    public function __construct(
        CognitoApiRepositoryInterface $cognito,
        S3ApiRepositoryInterface $s3,
        XidApiRepositoryInterface $xidApi,
        XidTempRepositoryInterface $xidTemp,
        XidTokenRepositoryInterface $xidToken,
        LoginRepositoryInterface $login,
        LoginLogRepositoryInterface $loginLog,
        CheckUserExistenceServiceInterface $checkExistence
    ) {
        $this->cognito = $cognito;
        $this->s3 = $s3;
        $this->xidApi = $xidApi;
        $this->xidTemp = $xidTemp;
        $this->xidToken = $xidToken;
        $this->login = $login;
        $this->loginLog = $loginLog;
        $this->checkExistence = $checkExistence;
    }

    /**
     * callback xid login function
     *
     * @param array $params
     * @return string
     */
    public function index(array $params): string
    {
        DB::beginTransaction();
        try {
            list($code, $error, $state, $cookie, $ip, $userAgent) = $params;
            
            $redirectUrl = explode('-', $state)[1];
            $xidTempData = $this->xidTemp->get(['cookie' => $cookie]);
            
            if (!$xidTempData) throw new \Exception('temp is not found.', 401);
            if (!$code && $error) throw new \Exception($error, 401);
            if (!$code && !$error) throw new \Exception('unexpected error');

            $s3Path = $xidTempData->env ? 'prod/' : 'dev/';
            $s3Path = $s3Path . $xidTempData->project_id . '.json';
            $xidConf = $this->s3->getObject('xid-conf', $s3Path)['Body']->getContents();
            $xidConf = json_decode($xidConf);
            
            $tokenResponse = $this->xidApi->getToken($xidTempData->env, $code, $xidConf->client_id, $xidConf->client_secret, config('app.url') . '/auth/callback/xid');
            $tokenResponse = json_decode($tokenResponse->Body());

            if (!$tokenResponse) throw new \Exception('access denied', 403);
            if (property_exists($tokenResponse, 'error')) throw new \Exception($tokenResponse->error, 401);
            
            $xidUserInfo = $this->xidApi->getUserInfo($xidTempData->env, $tokenResponse->access_token)->body();
            $xidUserInfo = json_decode($xidUserInfo);
            if (!$xidUserInfo) throw new \Exception('access denied', 403);
            if (property_exists($xidUserInfo, 'error')) throw new \Exception($xidUserInfo->error, 401);
            
            $email = $xidUserInfo->email;
            $sub = $xidUserInfo->sub;

            $userName = 'xid_' . $sub;
            $password = sha1(uniqid(mt_rand(), true));

            
            $userInfo = $this->checkExistence->checkUserExistence('username', $userName);
            if ($userInfo['existence'] && $userInfo['confirmed']) {
                $response = $this->cognito->signin([
                    'AuthFlow'       => 'CUSTOM_AUTH',
                    'AuthParameters' => [
                        'USERNAME' => $userName,
                        'EMAIL'    => $email
                    ],
                ]);

                $response = $this->cognito->confirmSignin([
                    'ChallengeName'      => 'CUSTOM_CHALLENGE',
                    'Session'            => $response['Session'],
                    'ChallengeResponses' => [
                        'USERNAME' => $response['ChallengeParameters']['USERNAME'],
                        'ANSWER'   => $response['ChallengeParameters']['code']
                    ],
                ]);
            } else {
                $response = $this->cognito->signup([
                    'Username'       => $userName,
                    'Password'       => $password,
                    'UserAttributes' => [
                        [
                            'Name'  => 'email',
                            'Value' => $email
                        ]
                    ],
                    'ClientMetadata' => [
                        'authType'    => 'xid'
                    ]
                ]);
            }



            $oldCookie = $this->login->login([
                'cookie'    => $cookie,
                'userName'  => $userName,
                'projectId' => $xidTempData->project_id,
                'ip'        => $ip,
                'userAgent' => $userAgent
            ]);
            $cookie = $oldCookie ? $oldCookie : $cookie;

            $this->loginLog->create([
                'cookie'    => $cookie,
                'userName'  => $userName,
            ]);

            $this->xidTemp->delete($cookie);

            date_default_timezone_set('Asia/Tokyo');
            $this->xidToken->create([
                'cookie'       => $cookie,
                'accessToken'  => $tokenResponse->access_token,
                'projectId'    => $xidTempData->project_id,
                'createdAt'    => date('Y-m-d H:i:s', strtotime('now'))
            ]);

            setcookie('TAPCM', $cookie, 0x7f000000, '/');
            setcookie('PLATEID_TAPCM', $cookie, 0x7f000000, '/', 'plate.id');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getMessage() === 'temp is not found.') {
                return $redirectUrl. '?' . http_build_query([
                    'succcess'     => 'false',
                    'errorMessage' => $e->getMessage()
                ]);
            }
            return $xidTempData->redirect_url . '?' . http_build_query([
                'succcess'     => 'false',
                'errorMessage' => $e->getMessage()
            ]);
        }
        return $xidTempData->redirect_url . '?' . http_build_query(['success' => 'true']);
    }
}