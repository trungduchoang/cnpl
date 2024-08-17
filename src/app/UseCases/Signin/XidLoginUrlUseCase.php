<?php
namespace App\UseCases\Signin;

use App\Repositories\S3\S3ApiRepositoryInterface;
use App\Repositories\Xid\XidTempRepositoryInterface;
use App\Services\CookieHandleService;
use Illuminate\Support\Facades\DB;

class XidLoginUrlUseCase
{
    const BASE_URL_DEV = 'https://oidc-uat.x-id.io/oauth2/auth';
    const BASE_URL_PROD = 'https://oidc.x-id.io/oauth2/auth';

    private $xidTemp;
    private $s3;
    private $cookieHandler;

    public function __construct(
        XidTempRepositoryInterface $xidTemp,
        S3ApiRepositoryInterface $s3,
        CookieHandleService $cookieHandler
    ) {
        $this->xidTemp = $xidTemp;
        $this->s3 = $s3;
        $this->cookieHandler = $cookieHandler;
    }


    /**
     * xid login url create function
     *
     * @param array $params
     * @return array
     */
    public function index(array $params): array
    {
        DB::beginTransaction();
        try {
            list($redirectUrl, $projectId, $cookie, $env) = $params;

            $s3Path = $env ? 'prod/' : 'dev/';
            $s3Path = $s3Path . $projectId . '.json';

            if (!$cookie) {
                $cookie = $this->cookieHandler->generateCookie();
                setcookie('TAPCM', $cookie, 0x7f000000, '/');
                setcookie('PLATEID_TAPCM', $cookie, 0x7f000000, '/', 'plate.id');
            }

            $xidConf = $this->s3->getObject('xid-conf', $s3Path)['Body']->getContents();
            $xidConf = json_decode($xidConf);

            $state = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8) . '-' . $redirectUrl;
            $nonce = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8);

            $this->xidTemp->create([
                'cookie'      => $cookie,
                'redirectUrl' => $redirectUrl,
                'projectId'   => $projectId,
                'env'         => $env
            ]);

            $baseUrl = $env ? self::BASE_URL_PROD : self::BASE_URL_DEV;
            $redirectUrl = $baseUrl . '?' . http_build_query([
                'client_id'     => $xidConf->client_id,
                'scope'         => $xidConf->scope,
                'redirect_uri'  => config('app.url') . '/auth/callback/xid',
                'response_type' => 'code',
                'response_mode' => 'query',
                'state'         => $state,
                'nonce'         => $nonce
            ]);
            
            $redirectUrl = urldecode($redirectUrl);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return [
            'statusCode' => '200',
            'url'        => $redirectUrl
        ];
   }
}