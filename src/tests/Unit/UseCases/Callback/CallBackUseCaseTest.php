<?php

namespace Tests\Unit\UseCases\Callback;

use App\Entities\Cognito\CognitoUserEntity;
use App\Entities\Login\LoginEntity;
use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\LoginLog\LoginLogRepositoryInterface;
use App\Services\Cookie\CookieService;
use App\Services\Cookie\CookieServiceInterface;
use App\UseCases\Callback\CallbackUseCase;
use Exception;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\once;

class CallBackUseCaseTest extends TestCase
{
    private CookieServiceInterface $cookieServiceMock;
    private CognitoApiRepositoryInterface $cognitoApirepositoryMock;
    private LoginRepositoryInterface $loginRepositoryMock;
    private LoginLogRepositoryInterface $loginLogRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->cookieServiceMock = Mockery::mock(CookieService::class);
        $this->cognitoApirepositoryMock = Mockery::mock(CognitoApiRepositoryInterface::class);
        $this->loginRepositoryMock = Mockery::mock(LoginRepositoryInterface::class);
        $this->loginLogRepositoryMock = Mockery::mock(LoginLogRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testSeccess()
    {
        $data = ['https://google.com', 99999, 'xxxxxx', '', 'xxxxxx', 'xxxxxx', 'xxxxxx'];
        list($redirectUrl, $projectId, $code, $error, $cookie, $ip, $userAgent) = $data;

        DB::shouldReceive('beginTransaction')->once();
        $cognitoUserEntity = new CognitoUserEntity(
            '',
            '',
            '',
            false,
            'xxxxxx'
        );


        $this->cognitoApirepositoryMock->shouldReceive('getAccessToken')
                                       ->once()
                                       ->with('xxxxxx')
                                       ->andReturn($cognitoUserEntity);
        

        $cognitoUserEntity = new CognitoUserEntity(
            'xxxxxx',
            'xxxxxx',
            'xxxxxx',
            true,
            'xxxxxx'
        );

        $this->cognitoApirepositoryMock->shouldReceive('getUserInfoByAccessToken')
                                       ->once()
                                       ->with($cognitoUserEntity->getAccessToken())
                                       ->andReturn($cognitoUserEntity);



        $this->loginRepositoryMock->shouldReceive('createOrUpdate')
                                  ->with([
                                      'cookie'    => $cookie,
                                      'projectId' => $projectId,
                                      'userName'  => $cognitoUserEntity->getUserName(),
                                      'ip'        => $ip,
                                      'userAgent' => $userAgent
                                  ])
                                  ->once()
                                  ->andReturn(new LoginEntity(
                                      $cookie,
                                      $cognitoUserEntity->getUserName(),
                                      '',
                                      $projectId,
                                      '',
                                      $ip,
                                      $userAgent
                                  ));

        $this->loginLogRepositoryMock->shouldReceive('create')
                                     ->with([
                                        'cookie'   => $cookie,
                                        'userName' => $cognitoUserEntity->getUserName()
                                     ])
                                     ->once()
                                     ->andReturn();

        $this->cookieServiceMock->shouldReceive('setCookie')
                                ->with($cookie)
                                ->once()
                                ->andReturn();

        DB::shouldReceive('commit')->once();
        
        $callbackUsecase = new CallbackUseCase($this->cookieServiceMock, $this->cognitoApirepositoryMock, $this->loginRepositoryMock, $this->loginLogRepositoryMock);
        $response = $callbackUsecase->index($data);

        $this->assertSame($redirectUrl . '?success=true', $response);
    }


    public function testException()
    {

        $this->expectException(\Exception::class);

        $data = ['https://google.com', 99999, '', 'error', 'xxxxxx', 'xxxxxx', 'xxxxxx'];
        list($redirectUrl, $projectId, $code, $error, $cookie, $ip, $userAgent) = $data;

        DB::shouldReceive('beginTransaction')->once();

        DB::shouldReceive('rollBack')->once();

        $callbackUsecase = new CallbackUseCase($this->cookieServiceMock, $this->cognitoApirepositoryMock, $this->loginRepositoryMock, $this->loginLogRepositoryMock);

        $response = $callbackUsecase->index($data);

        $this->assertSame($redirectUrl . '?success=false&errorMessage='. $error, $response);
    }
}
