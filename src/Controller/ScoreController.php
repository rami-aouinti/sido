<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\ScoreService;
use App\Domain\Score\Score;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function ctype_digit;

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

        $name = $payload['name'] ?? '';
        $reactionTime = $payload['reactionTime'] ?? null;

        if (!is_string($name)) {
            return new JsonResponse(
                ['errors' => [['name' => 'payload', 'message' => 'Name must be a string.']]],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if (is_string($reactionTime) && ctype_digit($reactionTime)) {
            $reactionTime = (int) $reactionTime;
        }

        if (!is_int($reactionTime)) {
            return new JsonResponse(
                ['errors' => [['name' => 'payload', 'message' => 'Reaction time must be an integer.']]],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

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
