<?php

/**
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 14:01:30
 * @modify date 2023-08-01 14:01:30
 * @desc [информация о пользователе/заведении]
 */
declare(strict_types=1);

namespace app\entities\user;

class User
{
    public $id;
    public $access_token;
    public $expires_in;
    public $password;
    public $refresh_token;
    public $scope;
    public $uid;
    public $username;
    public $clientId;
    public $clientSecret;

    public function __construct(
        UserId $id,
        AccessToken $access_token,
        Uid $uid,
        ExpiresIn $expires_in,
        Scope $scope,
        RefreshToken $refresh_token,
        UserPassword $password,
        UserName $username,
        ClientId $clientId,
        ClientSecret $clientSecret
    ) {
        $this->id = $id;
        $this->access_token = $access_token;
        $this->expires_in = $expires_in;
        $this->clientSecret = $clientSecret;
        $this->clientId = $clientId;
        $this->username = $username;
        $this->password = $password;
        $this->refresh_token = $refresh_token;
        $this->scope = $scope;
        $this->uid = $uid;
    }

    public function getName(): UserName
    {
        return $this->username;
    }

    public function getPassword(): UserPassword
    {
        return $this->password;
    }

    public function getClientId(): ClientId
    {
        return $this->clientId;
    }

    public function getClientSecret(): ClientSecret
    {
        return $this->clientSecret;
    }

    public function setName(UserName $username): void
    {
        $this->username = $username;
    }

    public function setPassword(UserPassword $password): void
    {
        $this->password = $password;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getRefreshToken(): RefreshToken
    {
        return $this->refresh_token;
    }

    public function getAccessToken(): AccessToken
    {
        return $this->access_token;
    }

    public function getExpiresIn(): ExpiresIn
    {
        return $this->expires_in;
    }

    public function getUid(): Uid
    {
        return $this->uid;
    }

    public function getScope(): Scope
    {
        return $this->scope	;
    }

    public function setRefreshToken(RefreshToken $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    public function setAccessToken(AccessToken $access_token): void
    {
        $this->access_token = $access_token;
    }

    public function setExpiresIn(ExpiresIn $expires_in): void
    {
        $this->expires_in = $expires_in;
    }

    public function setUid(Uid $uid): void
    {
        $this->uid = $uid;
    }
    public function setId(UserId $id): void
    {
        $this->id = $id;
    }

    public function setscope(Scope $scope): void
    {
        $this->scope	 = $scope	;
    }

    public function setClientId(ClientId $clientId): void
    {
        $this->clientId	 = $clientId	;
    }

    public function setClientSecret(ClientSecret $clientSecret): void
    {
        $this->clientSecret	 = $clientSecret	;
    }
}
