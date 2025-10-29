<?php

declare(strict_types=1);

namespace App\Infrastructure\Score;

use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;

final class InMemoryScoreRepository implements ScoreRepository
{
    /**
     * @var list<Score>
     */
    private array $scores = [];

    public function add(Score $score): void
    {
        $this->scores[] = $score;
    }

    public function topScores(int $limit): array
    {
        $scores = $this->scores;
        usort(
            $scores,
            static fn (Score $a, Score $b): int => $a->reactionTime()->toMilliseconds() <=> $b->reactionTime()->toMilliseconds()
        );

        return array_slice($scores, 0, max($limit, 0));
    }

    public function clear(): void
    {
        $this->scores = [];
    }
}
