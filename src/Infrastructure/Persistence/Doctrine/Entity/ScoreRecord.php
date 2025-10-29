<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Score\Score as DomainScore;
use App\Domain\Score\ValueObject\DisplayName;
use App\Domain\Score\ValueObject\ReactionTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'scores')]
#[ORM\Index(columns: ['reaction_time_ms', 'recorded_at', 'id'], name: 'idx_scores_reaction_time_recorded_at')]
class ScoreRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 32)]
    private string $playerName;

    #[ORM\Column(type: 'integer')]
    private int $reactionTimeMs;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $recordedAt;

    public function __construct(string $playerName, int $reactionTimeMs, DateTimeImmutable $recordedAt)
    {
        $this->playerName = $playerName;
        $this->reactionTimeMs = $reactionTimeMs;
        $this->recordedAt = $recordedAt;
    }

    public static function fromDomain(DomainScore $score): self
    {
        return new self(
            $score->playerName()->value(),
            $score->reactionTime()->toMilliseconds(),
            $score->recordedAt()
        );
    }

    public function toDomain(): DomainScore
    {
        return new DomainScore(
            new DisplayName($this->playerName),
            new ReactionTime($this->reactionTimeMs),
            $this->recordedAt
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function recordedAt(): DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function reactionTimeMs(): int
    {
        return $this->reactionTimeMs;
    }
}
