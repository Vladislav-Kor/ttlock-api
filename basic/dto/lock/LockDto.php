<?php

declare(strict_types=1);

namespace app\dto\lock;

class LockDto
{
    public $clientId;
    public $lockId;
    public $keyId;
    public $lockData;

    public function load(?array $data = null): void
    {
        if ($data !== null && \is_array($data)) {
            if (isset($data['clientId'])) {
                $this->clientId = $data['clientId'];
            }
            if (isset($data['lockId'])) {
                $this->lockId = (int)$data['lockId'];
            }
            if (isset($data['keyId'])) {
                $this->keyId = (int)$data['keyId'];
            }
            if (isset($data['lockData'])) {
                $this->lockData = $data['lockData'];
            }
        }
    }
}
