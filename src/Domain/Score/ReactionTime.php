<?php

declare(strict_types=1);

namespace App\Domain\Score;

use InvalidArgumentException;

final class ReactionTime
{
    private float $milliseconds;

    public function __construct(float $milliseconds)
    {
        if ($milliseconds <= 0) {
            throw new InvalidArgumentException('Reaction time must be positive.');
        }

        $this->milliseconds = $milliseconds;
    }

    public function toMilliseconds(): float
    {
        return $this->milliseconds;
    }
}
