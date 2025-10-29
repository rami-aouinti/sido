<?php

declare(strict_types=1);

namespace App\Domain\Score;

use InvalidArgumentException;

final class ReactionTime
{
    private const MIN_MILLISECONDS = 1.0;
    private const MAX_MILLISECONDS = 10000.0;

    private float $milliseconds;

    public function __construct(float $milliseconds)
    {
        if (!is_finite($milliseconds)) {
            throw new InvalidArgumentException('Reaction time must be a finite number.');
        }

        if ($milliseconds < self::MIN_MILLISECONDS) {
            throw new InvalidArgumentException(sprintf('Reaction time must be at least %.0f milliseconds.', self::MIN_MILLISECONDS));
        }

        if ($milliseconds > self::MAX_MILLISECONDS) {
            throw new InvalidArgumentException(sprintf('Reaction time must not exceed %.0f milliseconds.', self::MAX_MILLISECONDS));
        }

        $this->milliseconds = $milliseconds;
    }

    public function toMilliseconds(): float
    {
        return $this->milliseconds;
    }
}
