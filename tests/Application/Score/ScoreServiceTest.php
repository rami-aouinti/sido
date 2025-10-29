<?php

declare(strict_types=1);

namespace App\Tests\Application\Score;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\ScoreService;
use App\Infrastructure\Messaging\InMemoryHub;
use App\Infrastructure\Messaging\MercureTop10Publisher;
use App\Infrastructure\Score\InMemoryScoreRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use const JSON_THROW_ON_ERROR;
use function json_decode;

final class ScoreServiceTest extends TestCase
{
    private ScoreService $service;
    private InMemoryScoreRepository $repository;
    private InMemoryHub $hub;

    protected function setUp(): void
    {
        $this->repository = new InMemoryScoreRepository();
        $this->hub = new InMemoryHub();
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $publisher = new MercureTop10Publisher($this->hub);

        $this->service = new ScoreService($this->repository, $validator, $publisher);
    }

    public function testSubmitScorePersistsAndReturnsScore(): void
    {
        $score = $this->service->submitScore('Alice', 200.5);

        self::assertSame('Alice', $score->playerName()->value());
        self::assertSame(200.5, $score->reactionTime()->toMilliseconds());
        self::assertCount(1, $this->repository->topScores(10));
    }

    public function testSubmitScorePublishesTopScores(): void
    {
        $score = $this->service->submitScore('Alice', 200.5);

        $updates = $this->hub->updates();

        self::assertCount(1, $updates);
        $update = $updates[0];

        self::assertSame('/scores/top', $update->getTopic());

        $payload = json_decode($update->getData(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($payload);
        self::assertArrayHasKey('scores', $payload);
        self::assertSame('Alice', $payload['scores'][0]['name']);
        self::assertSame(200.5, $payload['scores'][0]['reactionTime']);
        self::assertSame($score->recordedAt()->format(DATE_ATOM), $payload['scores'][0]['recordedAt']);
    }

    public function testSubmitScoreWithInvalidDataThrowsException(): void
    {
        $this->expectException(ScoreValidationException::class);

        $this->service->submitScore('', 0);
    }

    public function testLeaderboardReturnsScoresSortedAscending(): void
    {
        $this->service->submitScore('Bob', 300);
        $this->service->submitScore('Alice', 200);
        $this->service->submitScore('Charlie', 250);

        $scores = $this->service->leaderboard();

        self::assertCount(3, $scores);
        self::assertSame('Alice', $scores[0]->playerName()->value());
        self::assertSame('Charlie', $scores[1]->playerName()->value());
        self::assertSame('Bob', $scores[2]->playerName()->value());
    }
}
