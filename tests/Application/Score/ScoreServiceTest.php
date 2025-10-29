<?php

declare(strict_types=1);

namespace App\Tests\Application\Score;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\ScoreService;
use App\Infrastructure\Score\InMemoryScoreRepository;
use PHPUnit\Framework\TestCase;
use function str_repeat;

final class ScoreServiceTest extends TestCase
{
    private ScoreService $service;
    private InMemoryScoreRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryScoreRepository();
        $this->service = new ScoreService($this->repository);
    }

    public function testSubmitScorePersistsAndReturnsScore(): void
    {
        $score = $this->service->submitScore(' Alice ', 200);

        self::assertSame('Alice', $score->playerName()->value());
        self::assertSame(200, $score->reactionTime()->toMilliseconds());
        self::assertCount(1, $this->repository->topScores(10));
    }

    public function testSubmitScoreWithInvalidDataThrowsException(): void
    {
        try {
            $this->service->submitScore('', 10);
            self::fail('Expected exception was not thrown.');
        } catch (ScoreValidationException $exception) {
            $errors = $exception->toArray();
            self::assertSame('name', $errors[0]['name']);
            self::assertSame('reactionTime', $errors[1]['name']);
        }

        $this->expectException(ScoreValidationException::class);
        $this->service->submitScore('Valid Name', 10);
    }

    public function testSubmitScoreWithNameLongerThanAllowedThrowsException(): void
    {
        $this->expectException(ScoreValidationException::class);
        $this->service->submitScore(str_repeat('A', 33), 120);
    }

    public function testSubmitScoreWithOutOfRangeReactionTimeThrowsException(): void
    {
        $this->expectException(ScoreValidationException::class);
        $this->service->submitScore('Valid Name', 10001);
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
