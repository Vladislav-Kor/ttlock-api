<?php

declare(strict_types=1);

namespace app\dto\user;

class UserDto
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

    public function load(?array $data = null): void
    {
        if ($data !== null && \is_array($data)) {
            if (isset($data['id'])) {
                $this->id = (int) $data['id'];
            }
            if (isset($data['username'])) {
                $this->username = $data['username'];
            }
            if (isset($data['password'])) {
                $this->password = $data['password'];
            }
            if (isset($data['clientId'])) {
                $this->clientId = $data['clientId'];
            }
            if (isset($data['clientSecret'])) {
                $this->clientSecret = $data['clientSecret'];
            }
            if (isset($data['access_token'])) {
                $this->access_token = $data['access_token'];
            }
            if (isset($data['expires_in'])) {
                $this->expires_in = $data['expires_in'];
            }
            if (isset($data['refresh_token'])) {
                $this->refresh_token = $data['refresh_token'];
            }
            if (isset($data['scope'])) {
                $this->scope = $data['scope'];
            }
            if (isset($data['uid'])) {
                $this->uid = $data['uid'];
            }
        }
    }
}
