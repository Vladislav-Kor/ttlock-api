<?php

declare(strict_types=1);

namespace app\entities\user;

class UserName
{
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
