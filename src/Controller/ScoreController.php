<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Score\Command\SubmitScoreCommand;
use App\Application\Score\Command\SubmitScoreHandler;
use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\Query\GetTopScoresHandler;
use App\Application\Score\Query\GetTopScoresQuery;
use App\Domain\Score\Score;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/scores')]
final class ScoreController
{
    public function __construct(
        private readonly SubmitScoreHandler $submitScoreHandler,
        private readonly GetTopScoresHandler $getTopScoresHandler
    ) {
    }

    #[Route(path: '', name: 'score_submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['errors' => [['name' => 'body', 'message' => 'Invalid JSON payload.']]], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = $payload['name'] ?? '';
        $reactionTime = $payload['reactionTime'] ?? null;

        if (!is_string($name) || !is_numeric($reactionTime)) {
            return new JsonResponse(
                ['errors' => [['name' => 'payload', 'message' => 'Name must be a string and reaction time must be numeric.']]],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $score = $this->submitScoreHandler->handle(new SubmitScoreCommand($name, (float) $reactionTime));
        } catch (ScoreValidationException $exception) {
            return new JsonResponse(['errors' => $exception->toArray()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(
            [
                'name' => $score->playerName()->value(),
                'reactionTime' => $score->reactionTime()->toMilliseconds(),
                'recordedAt' => $score->recordedAt()->format(DATE_ATOM),
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    #[Route(path: '', name: 'score_leaderboard', methods: ['GET'])]
    public function leaderboard(): JsonResponse
    {
        $scores = $this->getTopScoresHandler->handle(new GetTopScoresQuery());

        $data = array_map(
            static fn (Score $score) => [
                'name' => $score->playerName()->value(),
                'reactionTime' => $score->reactionTime()->toMilliseconds(),
                'recordedAt' => $score->recordedAt()->format(DATE_ATOM),
            ],
            $scores
        );

        return new JsonResponse(['scores' => $data]);
    }
}
