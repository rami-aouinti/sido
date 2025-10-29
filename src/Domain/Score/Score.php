<?php

declare(strict_types=1);

namespace App\Domain\Score;

use DateTimeImmutable;

final class Score
{
    private PlayerName $playerName;
    private ReactionTime $reactionTime;
    private DateTimeImmutable $recordedAt;

    public function __construct(PlayerName $playerName, ReactionTime $reactionTime, ?DateTimeImmutable $recordedAt = null)
    {
        $this->playerName = $playerName;
        $this->reactionTime = $reactionTime;
        $this->recordedAt = $recordedAt ?? new DateTimeImmutable();
    }

    public function playerName(): PlayerName
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
