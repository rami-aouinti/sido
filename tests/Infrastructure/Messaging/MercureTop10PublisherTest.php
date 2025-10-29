<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Messaging;

use App\Domain\Score\PlayerName;
use App\Domain\Score\ReactionTime;
use App\Domain\Score\Score;
use App\Infrastructure\Messaging\MercureTop10Publisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class MercureTop10PublisherTest extends TestCase
{
    public function testPublishSendsSerializedScoresToMercureHub(): void
    {
        $hub = new class implements HubInterface {
            public ?Update $lastUpdate = null;

            public function getUrl(): string
            {
                return 'http://mercure.local';
            }

            public function publish(Update $update): string
            {
                $this->lastUpdate = $update;

                return '1';
            }
        };

        $publisher = new MercureTop10Publisher($hub);

        $score = new Score(new PlayerName('Alice'), new ReactionTime(150));

        $publisher->publish([$score]);

        self::assertNotNull($hub->lastUpdate);
        self::assertSame('/scores/top', $hub->lastUpdate->getTopic());

        $payload = json_decode($hub->lastUpdate->getData(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($payload);
        self::assertArrayHasKey('scores', $payload);
        self::assertSame('Alice', $payload['scores'][0]['name']);
        self::assertSame(150.0, $payload['scores'][0]['reactionTime']);
        self::assertSame($score->recordedAt()->format(DATE_ATOM), $payload['scores'][0]['recordedAt']);
    }
}
