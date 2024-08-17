<?php
namespace App\Entities\Login;

class LoginEntity implements LoginEntityInterface
{
    private $cookie;
    private $userName;
    private $lastLogin;
    private $projectId;
    private $createdAt;
    private $ip;
    private $userAgent;


    public function __construct(
        string $cookie = '',
        string $userName = '',
        string $lastLogin = '',
        string $projectId = '',
        string $createdAt = '',
        string $ip = '',
        string $userAgent = ''
    ) {
        $this->cookie = $cookie;
        $this->userName = $userName;
        $this->lastLogin = $lastLogin;
        $this->projectId = $projectId;
        $this->createdAt = $createdAt;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }


    /**
     * get cookie function
     *
     * @return string
     */
    public function getCookie(): string
    {
        return $this->cookie;
    }

    /**
     * get username function
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * get last login function
     *
     * @return string
     */
    public function getLastLogin(): string
    {
        return $this->lastLogin;
    }

    /**
     * get project id function
     * projectId is team_id in login table
     *
     * @return string
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * get created at function
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * get ip function
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * get user agent function
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

}