<?php

declare(strict_types=1);

namespace App\Domain\Score;

interface ScoreRepository
{
    public function add(Score $score): void;

    /**
     * @return list<Score>
     */
    public function topScores(int $limit): array;
}
