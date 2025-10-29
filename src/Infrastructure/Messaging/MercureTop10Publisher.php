<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use App\Application\Score\TopScorePublisher;
use App\Domain\Score\Score;
use JsonException;
use RuntimeException;
use const JSON_THROW_ON_ERROR;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class MercureTop10Publisher implements TopScorePublisher
{
    public const TOPIC = '/scores/top';

    public function __construct(private readonly HubInterface $hub)
    {
    }

    /**
     * @param list<Score> $scores
     */
    public function publish(array $scores): void
    {
        $payload = array_map(
            static fn (Score $score): array => [
                'name' => $score->playerName()->value(),
                'reactionTime' => $score->reactionTime()->toMilliseconds(),
                'recordedAt' => $score->recordedAt()->format(DATE_ATOM),
            ],
            $scores
        );

        try {
            $data = json_encode(['scores' => $payload], JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Unable to encode leaderboard payload.', 0, $exception);
        }

        $this->hub->publish(new Update(self::TOPIC, $data));
    }
}
