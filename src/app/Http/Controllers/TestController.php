<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Test\TestRequest;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Services\Cookie\CookieService;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    private $loginRepository;
    private $cookieService;

    public function __construct(LoginRepositoryInterface $loginRepository, CookieService $cookieService)
    {
        $this->loginRepository = $loginRepository;
        $this->cookieService = $cookieService;
    }

    public function test(TestRequest $request)
    {
        $storage = Storage::allFiles('temp/.');
        dump(storage_path());
        dd($storage);

    }

    public function testCallback(TestRequest $request)
    {
        try {
            if ($request->success === 'false') throw new \Exception('auth error');
            $cookie = $request->cookie('TAPCM') ? $request->cookie('TAPCM') : $request->cookie('PLATEID_TAPCM');
            if (!$cookie) throw new \Exception('cookie not found');

            $user = $this->loginRepository->getUserData([
                'cookie'    => $cookie,
                'projectId' => '99999'
            ]);
            if (!$user) throw new \Exception('User not found');
            

        } catch (\Exception $e) {
            return view('test/minapita_callback', [
                'success'  => 'false',
                'message'  => $e->getMessage()
            ]);
        }
        return view('test/minapita_callback', [
            'success'  => 'true',
            'userData' => [
                'userName'   => $user->oauth_username,
                'expireDate' => $user->expire_date,
                'lastLogin'  => $user->last_login
            ]
        ]);
    }
}
