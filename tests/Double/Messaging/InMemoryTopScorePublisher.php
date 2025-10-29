<?php

declare(strict_types=1);

namespace App\Tests\Double\Messaging;

use App\Application\Score\TopScorePublisher;
use App\Domain\Score\Score;

final class InMemoryTopScorePublisher implements TopScorePublisher
{
    /** @var list<Score> */
    public array $lastPayload = [];

    /**
     * @param list<Score> $scores
     */
    public function publish(array $scores): void
    {
        $this->lastPayload = $scores;
    }
}
