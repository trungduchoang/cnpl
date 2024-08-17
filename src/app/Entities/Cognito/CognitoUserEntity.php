<?php

namespace App\Entities\Cognito;


class CognitoUserEntity implements CognitoUserEntityInterface
{
    private $userName;
    private $sub;
    private $email;
    private $phoneNumber;
    private $userStatus;
    private $accessToken;
    private $idToken;
    private $refreshToken;

    public function __construct(
        string $userName = '',
        string $sub = '',
        string $email = '',
        string $phoneNumber = '',
        bool $userStatus = false,
        string $accessToken = '',
        string $idToken = '',
        string $refreshToken = ''
    ) {
        $this->userName = $userName;
        $this->sub = $sub;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->userStatus = $userStatus;
        $this->accessToken = $accessToken;
        $this->idToken = $idToken;
        $this->refreshToken = $refreshToken;
    }

    /**
     * cognito username getter function
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * cognito sub getter function
     *
     * @return string
     */
    public function getSub(): string
    {
        return $this->sub;
    }


    /**
     * email getter function
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * phone number getter function
     *
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * user status getter function
     * sutatus is CONFIRMED or UNCONFIRMED. CONFIRMED is made true UNCONFIRMED is made false.
     *
     * @return boolean
     */
    public function getUserStaus(): bool
    {
        return $this->userStatus;
    }

    /**
     * cognito user access token getter function
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * cognito user id token getter function
     *
     * @return string
     */
    public function getIdToken(): string
    {
        return $this->idToken;
    }
}