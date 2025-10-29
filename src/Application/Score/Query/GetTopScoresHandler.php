<?php

declare(strict_types=1);

namespace App\Application\Score\Query;

use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;

final class GetTopScoresHandler
{
    public function __construct(private readonly ScoreRepository $repository)
    {
    }

    /**
     * @return list<Score>
     */
    public function handle(GetTopScoresQuery $query): array
    {
        return $this->repository->topScores($query->limit);
    }
}
