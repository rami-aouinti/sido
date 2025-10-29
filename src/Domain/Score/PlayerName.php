<?php

declare(strict_types=1);

namespace App\Domain\Score;

use InvalidArgumentException;

final class PlayerName
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 30;
    private const ALLOWED_PATTERN = "/^[\\p{L}\\p{N}\\s'-]+$/u";

    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Player name cannot be empty.');
        }

        $length = function_exists('mb_strlen') ? mb_strlen($trimmed) : strlen($trimmed);

        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(sprintf('Player name must be at least %d characters long.', self::MIN_LENGTH));
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('Player name must be at most %d characters long.', self::MAX_LENGTH));
        }

        if (preg_match(self::ALLOWED_PATTERN, $trimmed) !== 1) {
            throw new InvalidArgumentException('Player name contains invalid characters.');
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
