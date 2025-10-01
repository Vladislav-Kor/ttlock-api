<?php

declare(strict_types=1);

namespace app\entities\user;

class UserId
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

    public function isEquals(self $value): bool
    {
        return $this->getValue() === $value->getValue();
    }
}
