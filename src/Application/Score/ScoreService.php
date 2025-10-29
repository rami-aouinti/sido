<?php

declare(strict_types=1);

namespace App\Application\Score;

use App\Application\Score\Exception\ScoreValidationException;
use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;
use App\Domain\Score\ValueObject\DisplayName;
use App\Domain\Score\ValueObject\ReactionTime;
use InvalidArgumentException;

final class ScoreService
{
    public function __construct(private readonly ScoreRepository $repository)
    {
    }

    /**
     * @throws ScoreValidationException
     */
    public function submitScore(string $name, int $reactionTime): Score
    {
        $errors = [];
        /** @var DisplayName|null $displayName */
        $displayName = null;
        /** @var ReactionTime|null $reactionTimeValue */
        $reactionTimeValue = null;

        try {
            $displayName = new DisplayName($name);
        } catch (InvalidArgumentException $exception) {
            $errors[] = ['name' => 'name', 'message' => $exception->getMessage()];
        }

        try {
            $reactionTimeValue = new ReactionTime($reactionTime);
        } catch (InvalidArgumentException $exception) {
            $errors[] = ['name' => 'reactionTime', 'message' => $exception->getMessage()];
        }

        if ($errors !== []) {
            throw new ScoreValidationException($errors);
        }

        $score = new Score($displayName, $reactionTimeValue);
        $this->repository->add($score);

        return $score;
    }

    /**
     * @return list<Score>
     */
    public function leaderboard(int $limit = 10): array
    {
        return $this->repository->topScores($limit);
    }
}
