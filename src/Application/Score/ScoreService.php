<?php

declare(strict_types=1);

namespace App\Application\Score;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\TopScoresPublisher;
use App\Domain\Score\PlayerName;
use App\Domain\Score\ReactionTime;
use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ScoreService
{
    public function __construct(
        private readonly ScoreRepository $repository,
        private readonly ValidatorInterface $validator,
        private readonly TopScoresPublisher $publisher
    ) {
    }

    /**
     * @throws ScoreValidationException
     */
    public function submitScore(string $name, float $reactionTime): Score
    {
        $submission = new ScoreSubmission($name, $reactionTime);
        $violations = $this->validator->validate($submission);

        if (count($violations) > 0) {
            throw new ScoreValidationException($violations);
        }

        $score = new Score(new PlayerName($submission->name), new ReactionTime($submission->reactionTime));
        $this->repository->add($score);

        $this->publisher->publish($this->repository->topScores(10));

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
