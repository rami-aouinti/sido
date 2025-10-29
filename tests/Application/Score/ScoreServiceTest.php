<?php

declare(strict_types=1);

namespace App\Tests\Application\Score;

use App\Application\Score\Exception\ScoreValidationException;
use App\Application\Score\ScoreService;
use App\Infrastructure\Score\InMemoryScoreRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ScoreServiceTest extends TestCase
{
    private ScoreService $service;
    private InMemoryScoreRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryScoreRepository();
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $this->service = new ScoreService($this->repository, $validator);
    }

    public function testSubmitScorePersistsAndReturnsScore(): void
    {
        $score = $this->service->submitScore('Alice', 200);

        self::assertSame('Alice', $score->playerName()->value());
        self::assertSame(200, $score->reactionTime()->toMilliseconds());
        self::assertCount(1, $this->repository->topScores(10));
    }

    public function testSubmitScoreWithInvalidDataThrowsException(): void
    {
        $this->expectException(ScoreValidationException::class);

        $this->service->submitScore('', 0);
    }

    public function testSubmitScoreWithTooLongNameThrowsException(): void
    {
        $this->expectException(ScoreValidationException::class);

        $this->service->submitScore(str_repeat('A', 256), 150);
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
