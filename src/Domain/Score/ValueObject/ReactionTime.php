<?php

declare(strict_types=1);

namespace App\Domain\Score\ValueObject;

use InvalidArgumentException;

final class ReactionTime
{
    private int $milliseconds;

    public function __construct(int $milliseconds)
    {
        if ($milliseconds < 50 || $milliseconds > 10000) {
            throw new InvalidArgumentException('Reaction time must be between 50 and 10000 milliseconds.');
        }

        $this->milliseconds = $milliseconds;
    }

    public function toMilliseconds(): int
    {
        return $this->milliseconds;
    }
}
