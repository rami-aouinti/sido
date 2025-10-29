<?php

declare(strict_types=1);

namespace App\Domain\Score;

use App\Domain\Score\ValueObject\DisplayName;
use App\Domain\Score\ValueObject\ReactionTime;
use DateTimeImmutable;

final class Score
{
    private DisplayName $displayName;
    private ReactionTime $reactionTime;
    private DateTimeImmutable $recordedAt;

    public function __construct(DisplayName $displayName, ReactionTime $reactionTime, ?DateTimeImmutable $recordedAt = null)
    {
        $this->displayName = $displayName;
        $this->reactionTime = $reactionTime;
        $this->recordedAt = $recordedAt ?? new DateTimeImmutable();
    }

    public function playerName(): DisplayName
    {
        return $this->displayName;
    }

    public function reactionTime(): ReactionTime
    {
        return $this->reactionTime;
    }

    public function recordedAt(): DateTimeImmutable
    {
        return $this->recordedAt;
    }
}
