<?php

/**
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 14:01:42
 * @modify date 2023-08-01 14:01:42
 * @desc [description]
 */
declare(strict_types=1);

namespace app\entities\user;

class Scope
{
    /**
     * @var string
     */
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
