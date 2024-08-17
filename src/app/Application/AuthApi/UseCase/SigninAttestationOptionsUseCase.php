<?php

namespace App\Application\AuthApi\UseCase;

use App\Services\CookieHandleService;
use App\Services\Base64UrlService;
use Illuminate\Support\Facades\DB;
use App\Models\AttestationOpstions;
use App\Models\Credentials;
use Dotenv\Util\Str;

class SigninAttestationOptionsUseCase
{

    protected $cookieHandleService;
    protected $base64UrlService;


    public function __construct()
    {
        $this->cookieHandleService = app()->make(CookieHandleService::class);
        $this->base64UrlService = app()->make(Base64UrlService::class);
    }

    /**
     * Undocumented function
     *
     * @param object $request
     * @return array
     */
    public function index(object $request): array
    {
        try {
            DB::beginTransaction();
            list($origin, $projectId) = $request->getParam($request);
            $cookie = $this->getCookie($request);
            $challenge = $this->generateChallenge();
            $credentialId = $this->getCredentialId($cookie, $projectId);
            $this->createAttestationOptions(['challenge' => $challenge, 'origin' => $origin]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return ['challenge' => $challenge, 'credentialId' => $credentialId];
    }

    /**
     * get cookie function
     *
     * @param [type] $request
     * @return string
     */
    private function getCookie($request): string
    {
        $cookie = $this->cookieHandleService->getCookie($request);
        if ($cookie) {
            return $cookie;
        }
        throw new \Exception('cookie not found', 400);
    }

    /**
     * generate challenge param function
     *
     * @return string
     */
    private function generateChallenge(): string
    {
        return sha1(uniqid(mt_rand(), true));
    }

    /**
     * create attestation options function
     *
     * @param array $data
     * @return void
     */
    private function createAttestationOptions(array $data): void
    {
        try {
            AttestationOpstions::createAttestationOptions($data);
        } catch (\Exception $e) {
            throw new \Exception('db error', 500);
        }
    }

    
    private function getCredentialId(string $cookie, mixed $projectId): string
    {
        $data = Credentials::getCredentials($cookie, $projectId);
        if ($data) {
            return $data->credential_id;
        }
        throw new \Exception('User is not found', 400);
    }

}