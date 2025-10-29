<?php

declare(strict_types=1);

namespace App\Domain\Score;

use App\Domain\Score\ValueObject\DisplayName;
use App\Domain\Score\ValueObject\ReactionTime;
use DateTimeImmutable;

final class Score
{
    private DisplayName $playerName;
    private ReactionTime $reactionTime;
    private DateTimeImmutable $recordedAt;

    public function __construct(DisplayName $playerName, ReactionTime $reactionTime, ?DateTimeImmutable $recordedAt = null)
    {
        $this->playerName = $playerName;
        $this->reactionTime = $reactionTime;
        $this->recordedAt = $recordedAt ?? new DateTimeImmutable();
    }

    public function playerName(): DisplayName
    {
        return $this->playerName;
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
