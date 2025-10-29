<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\ScoreService;
use App\Domain\Score\Score;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/scores')]
final class ScoreController
{
    public function __construct(private readonly ScoreService $scoreService)
    {
    }

    #[Route(path: '', name: 'score_submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['errors' => [['name' => 'body', 'message' => 'Invalid JSON payload.']]], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = $payload['name'] ?? null;
        $reactionTime = $payload['reactionTime'] ?? null;

        $errors = [];

        if (!is_string($name)) {
            $errors[] = ['name' => 'name', 'message' => 'Name must be a string.'];
        }

        if (!is_int($reactionTime)) {
            $errors[] = ['name' => 'reactionTime', 'message' => 'Reaction time must be an integer.'];
        }

        if ($errors !== []) {
            return new JsonResponse(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        \assert(is_string($name));
        \assert(is_int($reactionTime));

        try {
            $score = $this->scoreService->submitScore($name, $reactionTime);
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
        $scores = $this->scoreService->leaderboard();

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
