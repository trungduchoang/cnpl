<?php

namespace App\UseCases\Confirm;

use App\Libraries\CryptoQueryUtilInterface;
use Illuminate\Support\Facades\DB;
use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\Temp\TempRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\Storage;

class ConfirmSigninUseCase
{

    private $login;
    private $cognito;
    private $tempRepository;
    private $cryptQueryUtil;

    public function __construct(
        LoginRepositoryInterface $login,
        CognitoApiRepositoryInterface $cognito,
        TempRepositoryInterface $tempRepository,
        CryptoQueryUtilInterface $cryptQueryUtil
    ) {
        $this->login = $login;
        $this->cognito = $cognito;
        $this->tempRepository = $tempRepository;
        $this->cryptQueryUtil = $cryptQueryUtil;
    }

    public function index(array $param)
    {
        DB::beginTransaction();
        logger('log 0 :' . url()->previous());
        try {
            list($uid, $cookie, $ip, $userAgent) = $param;

            logger('get file name sign in : ' . $uid . 'json');

            
            if (Storage::exists('temp/' . $uid . '.json')) {
                $temp = Storage::get('temp/' . $uid . '.json');
            } else {
                $temp = $this->tempRepository->getTemp($uid);
                if (!$temp) throw new \Exception('temp is not found', 400);
                $temp = $temp->login_data;
            }
            $temp = json_decode($temp);

            if (!property_exists($temp, 'redirectUrl')) throw new \Exception('temp is not found', 400);

            logger('log 1 : ' . json_encode($temp) . '   $uid');

            $this->cognito->confirmSignin([
                'ChallengeName'      => 'CUSTOM_CHALLENGE',
                'Session'            => $temp->session,
                'ChallengeResponses' => [
                    'USERNAME' => $temp->userName,
                    'ANSWER'   => $temp->secretCode
                ],
            ]);

            $expireDate = new DateTime();
            $expireDate->modify('+36 hours');
            if ($temp->multiRecord) {
                logger('log 2 : ' . json_encode($temp) . '   $uid');
                $loginEntity = $this->login->createOrUpdate([
                    'cookie'       => explode('_', $temp->userName)[1],
                    'projectId'    => $temp->projectId,
                    'userName'     => $temp->userName,
                    'expireDate'   => $expireDate->format('Y-m-d H:i:s'),
                    'ip'           => $ip,
                    'userAgent'    => $userAgent
                ]);
                $cookie = $cookie === $loginEntity->getCookie() ? $cookie : $loginEntity->getCookie();
            } else {
                logger('log 3 : ' . json_encode($temp) . '   $uid');
                $oldCookie = $this->login->login([
                    'cookie'    => explode('_', $temp->userName)[1],
                    'userName'  => $temp->userName,
                    'projectId' => $temp->projectId,
                    'ip'        => $ip,
                    'userAgent' => $userAgent
                ]);
                $cookie = $oldCookie ? $oldCookie : $cookie;
            }


            setcookie('TAPCM', explode('_', $temp->userName)[1], 0x7f000000, '/');
            setcookie('PLATEID_TAPCM', explode('_', $temp->userName)[1], 0x7f000000, '/', 'plate.id');
            setcookie('login_time_' . $temp->projectId, strtotime('now'), 0x7f000000, '/');
            $redirectUrl = $temp->redirectUrl;
            $redirectUrlParsed = parse_url($redirectUrl);
            if (array_key_exists('query', $redirectUrlParsed)) {
                $redirectUrl .= '&success=true';
            } else {
                $redirectUrl .= '?success=true';
            }
            logger('log 4 : ' . json_encode($temp) . '   $uid');
            Storage::delete('temp/' . $uid . '.json');
            $this->tempRepository->deleteTemp($uid);
        } catch (\Exception $e) {
            DB::rollback();
            logger($e);
            if ($e->getMessage() === 'temp is not found') {
                throw new \Exception($e->getMessage(), $e->getCode());
            } else {
                $redirectUrl = $temp->redirectUrl;
                $redirectUrlParsed = parse_url($redirectUrl);
                if (array_key_exists('query', $redirectUrlParsed)) {
                    $redirectUrl .= '&success=false&errorMessage=' . $e->getMessage();
                } else {
                    $redirectUrl .= '?success=false&errorMessage=' . $e->getMessage();
                }
                return $redirectUrl;
            }
        }
        DB::commit();
        logger('log 5 :' . $redirectUrl);
        return $redirectUrl;
    }
}