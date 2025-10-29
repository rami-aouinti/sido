<?php

declare(strict_types=1);

namespace App\Application\Score;

use App\Domain\Score\Score;

interface TopScoresPublisher
{
    /**
     * @param list<Score> $scores
     */
    public function publish(array $scores): void;
}
