<?php

namespace App\Application\AuthApi\UseCase;

use App\Services\CookieHandleService;
use Illuminate\Support\Facades\DB;
use App\Models\AttestationOpstions;
use App\Models\Login;

class SignupAttestationOptionsUseCase
{

    protected $cookieHandleService;

    public function __construct(CookieHandleService $cookieHandleService)
    {
        $this->cookieHandleService = $cookieHandleService;
    }

    /**
     * put together signup attestation options process function
     *
     * @param object $request
     * @return array
     */
    public function index(object $request): array
    {
        try {
            DB::beginTransaction();
            list($origin, $projectId) = $request->getParam($request);
            // $cookie = $this->getCookie($request);
            // $this->userExistsCheck($cookie, $projectId);
            $challenge = $this->generateChallenge();
            $this->createAttestationOptions(['challenge' => $challenge, 'origin' => $origin]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return ['challenge' => $challenge];
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
     * user exist check login table function
     *
     * @param string $cookie
     * @param string $projectId
     * @return void
     */
    private function userExistsCheck(string $cookie, string $projectId): void
    {
        if (Login::isLogin($cookie, $projectId)) {
            throw new \Exception('User already exists', 400);
        }
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
            throw new \Exception('internal server error', 500);
        }
    }

}