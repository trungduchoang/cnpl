<?php
namespace App\Application\AuthApi\UseCase;


use App\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Services\CookieHandleService;
use App\Models\Login;
use App\Models\LoginLog;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\Temp\TempRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmailVerifyUseCase
{
    protected $cognitoClient;
    protected $cookieHandleService;
    protected $login;
    protected $tempRepository;

    public function __construct(LoginRepositoryInterface $login, TempRepositoryInterface $tempRepository)
    {
        $this->cognitoClient = app()->make(CognitoClient::class);
        $this->cookieHandleService = app()->make(CookieHandleService::class);
        $this->login = $login;
        $this->tempRepository = $tempRepository;
    }

    public function index($request)
    {
        DB::beginTransaction();
        try {
            list($uid, $code, $ip, $userAgent) = $request->getParam();

            logger('get file name sign up : ' . $uid . 'json');

            if (Storage::exists('temp/' . $uid . '.json')) {
                $session = Storage::get('temp/' . $uid . '.json');
            } else {
                $session = $this->tempRepository->getTemp($uid);
                if (!$session) throw new \Exception('temp is not found', 400);
                $session = $session->login_data;
            }
            $session = json_decode($session);


            if (!$session) throw new \Exception('temp is not found', 400);
            if (!property_exists($session, 'redirectUrl')) throw new \Exception('temp is not found');


            $this->confirmApiRequest($session->userName, $code);
            $cookie = explode('_', $session->userName)[1];
            $this->setCookie($cookie);
            setcookie('login_time_' . $session->projectId, strtotime('now'), 0x7f000000, '/');

            $expireDate = new DateTime();
            $expireDate->modify('+36 hours');
            if ($session->multiRecord) {
                $this->login->createOrUpdate([
                    'cookie'       => $cookie,
                    'projectId'    => $session->projectId,
                    'userName'     => $session->userName,
                    'expireDate'   => $expireDate->format('Y-m-d H:i:s'),
                    'ip'           => $ip,
                    'userAgent'    => $userAgent
                ]);
            } else {
                $this->login->login([
                    'cookie'    => $cookie,
                    'userName'  => $session->userName,
                    'projectId' => $session->projectId,
                    'ip'        => $ip,
                    'userAgent' => $userAgent
                ]);
            }

            
            $this->upsertLoginLog($cookie, $session->userName);

            $redirectUrlParsed = parse_url($session->redirectUrl);
            if (array_key_exists('query', $redirectUrlParsed)) {
                $session->redirectUrl .= '&success=true';
            } else {
                $session->redirectUrl .= '?success=true';
            }
            Storage::delete('temp/' . $uid . '.json');
            $this->tempRepository->deleteTemp($uid);
        } catch (CognitoIdentityProviderException $e) {
            logger($e);
            if ($e->getAwsErrorMessage() === 'User cannot be confirmed. Current status is CONFIRMED') {
                throw new \Exception('temp is not found', 400);
            } else {
                throw new \Exception($e->getAwsErrorMessage(), $e->getStatusCode());
            }
        } catch (\Exception $e) {
            DB::rollback();
            logger($e);
            if ($e->getMessage() === 'temp is not found') {
                throw new \Exception($e->getMessage(), $e->getCode());
            } else {
                $redirectUrlParsed = parse_url($session->redirectUrl);
                if (array_key_exists('query', $redirectUrlParsed)) {
                    $session->redirectUrl .= '&success=false&errorMessage=' . $e->getMessage();
                } else {
                    $session->redirectUrl .= '?success=false&errorMessage=' . $e->getMessage();
                }
                return $session->redirectUrl;
            }
        }
        DB::commit();
        return $session->redirectUrl;
    }

    private function confirmApiRequest($userName, $confrmationCode)
    {
        $this->cognitoClient->confirm($userName, $confrmationCode);
    }

    private function setCookie($cookie)
    {
        $this->cookieHandleService->setCookie($cookie);
    }

    private function upsertLogin($cookie, $userName, $projectId)
    {
        try {
            $cookie = Login::login($cookie, $userName, $projectId);
            return $cookie;
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }

    private function upsertLoginLog($cookie, $userName)
    {
        try {
            LoginLog::login($cookie, $userName);
        } catch (\Exception $e) {
            throw new \Exception('internal server error', 500);
        }
    }
}