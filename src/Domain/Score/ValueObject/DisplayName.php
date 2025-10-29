<?php

declare(strict_types=1);

namespace App\Domain\Score\ValueObject;

use InvalidArgumentException;

final class DisplayName
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Display name cannot be empty.');
        }

        $length = mb_strlen($trimmed);
        if ($length < 1 || $length > 32) {
            throw new InvalidArgumentException('Display name must be between 1 and 32 characters.');
        }

        $this->value = $trimmed;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
