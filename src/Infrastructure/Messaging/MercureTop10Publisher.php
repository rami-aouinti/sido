<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use App\Application\Score\TopScoresPublisher;
use App\Domain\Score\Score;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use const JSON_THROW_ON_ERROR;
use function json_encode;

final class MercureTop10Publisher implements TopScoresPublisher
{
    public function __construct(private readonly HubInterface $hub)
    {
    }

    /**
     * @param list<Score> $scores
     */
    public function publish(array $scores): void
    {
        $payload = array_map(
            static fn (Score $score) => [
                'name' => $score->playerName()->value(),
                'reactionTime' => $score->reactionTime()->toMilliseconds(),
                'recordedAt' => $score->recordedAt()->format(DATE_ATOM),
            ],
            $scores
        );

        $data = json_encode(['scores' => $payload], JSON_THROW_ON_ERROR);

        $this->hub->publish(new Update('/scores/top', $data));
    }
}
