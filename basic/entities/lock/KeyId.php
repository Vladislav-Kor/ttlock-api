<?php

/**
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 14:01:30
 * @modify date 2023-08-01 14:01:30
 * @desc [период выдачи]
 */

declare(strict_types=1);

namespace app\entities\lock;

class KeyId
{
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
