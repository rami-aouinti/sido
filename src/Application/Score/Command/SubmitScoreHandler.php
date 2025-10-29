<?php

declare(strict_types=1);

namespace App\Application\Score\Command;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\ScoreSubmission;
use App\Application\Score\TopScorePublisher;
use App\Domain\Score\PlayerName;
use App\Domain\Score\ReactionTime;
use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SubmitScoreHandler
{
    public function __construct(
        private readonly ScoreRepository $repository,
        private readonly ValidatorInterface $validator,
        private readonly TopScorePublisher $publisher
    ) {
    }

    /**
     * @throws ScoreValidationException
     */
    public function handle(SubmitScoreCommand $command): Score
    {
        $submission = new ScoreSubmission($command->name, $command->reactionTime);
        $violations = $this->validator->validate($submission);

        if (count($violations) > 0) {
            throw new ScoreValidationException($violations);
        }

        $score = new Score(new PlayerName($submission->name), new ReactionTime($submission->reactionTime));
        $this->repository->add($score);
        $this->publisher->publish($this->repository->topScores(10));

        return $score;
    }
}
