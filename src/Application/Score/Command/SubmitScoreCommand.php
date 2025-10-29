<?php

declare(strict_types=1);

namespace App\Application\Score\Command;

final class SubmitScoreCommand
{
    public function __construct(
        public readonly string $name,
        public readonly float $reactionTime
    ) {
    }
}
