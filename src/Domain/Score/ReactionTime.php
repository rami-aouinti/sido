<?php

declare(strict_types=1);

namespace App\Domain\Score;

use InvalidArgumentException;

final class ReactionTime
{
    private int $milliseconds;

    public function __construct(int $milliseconds)
    {
        if ($milliseconds <= 0) {
            throw new InvalidArgumentException('Reaction time must be positive.');
        }

        $this->milliseconds = $milliseconds;
    }

    public function toMilliseconds(): int
    {
        return $this->milliseconds;
    }
}
