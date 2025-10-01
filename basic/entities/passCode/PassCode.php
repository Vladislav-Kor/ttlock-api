<?php

/**
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 14:01:30
 * @modify date 2023-08-01 14:01:30
 * @desc []
 */
declare(strict_types=1);

namespace app\entities\passCode;

use app\entities\lock\lockId;

class PassCode
{
    private $keyboardPwd;

    private $keyboardPwdId;

    private $lockId;

    public function __construct(
        KeyboardPwd $keyboardPwd,
        KeyboardPwdId $keyboardPwdId,
        lockId $lockId
    ) {
        $this->keyboardPwd = $keyboardPwd;
        $this->keyboardPwdId = $keyboardPwdId;
        $this->lockId = $lockId;
    }

    public function getlockId(): lockId
    {
        return $this->lockId;
    }

    public function setlockId(lockId $lockId): void
    {
        $this->lockId = $lockId;
    }

    public function getKeyboardPwd(): KeyboardPwd
    {
        return $this->keyboardPwd;
    }

    public function getKeyboardPwdId(): KeyboardPwdId
    {
        return $this->keyboardPwdId;
    }
    public function setkeyboardPwd(KeyboardPwdId $keyboardPwd): void
    {
        $this->keyboardPwd = $keyboardPwd;
    }

    public function setKeyboardPwdId(KeyboardPwdId $keyboardPwdId): void
    {
        $this->keyboardPwdId = $keyboardPwdId;
    }
}
