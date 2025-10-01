<?php

/**
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 14:01:30
 * @modify date 2023-08-01 14:01:30
 * @desc []
 */
declare(strict_types=1);

namespace app\entities\lock;

use app\entities\user\ClientId;

class LockInit
{
    private $lockId;

    private $keyId;

    private $clientId;

    private $lockData;

    public function __construct(
        lockId $lockId,
        ?KeyId $keyId,
        ClientId $clientId,
        LockData $lockData
    ) {
        $this->lockId = $lockId;
        $this->lockData = $lockData;
        $this->keyId = $keyId;
        $this->clientId = $clientId;
    }

    public function getClientId(): ClientId
    {
        return $this->clientId;
    }

    public function getLockData(): LockData
    {
        return $this->lockData;
    }

    public function getlockId(): lockId
    {
        return $this->lockId;
    }

    public function setClientId(ClientId $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getKeyId(): ?KeyId
    {
        return $this->keyId;
    }
    public function setlockId(lockId $lockId): void
    {
        $this->lockId = $lockId;
    }

    public function setKeyId(KeyId $keyId): void
    {
        $this->keyId = $keyId;
    }

    public function setLockData(LockData $lockData): void
    {
        $this->lockData = $lockData;
    }
}
