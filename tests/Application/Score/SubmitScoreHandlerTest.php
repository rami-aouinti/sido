<?php

declare(strict_types=1);

namespace App\Tests\Application\Score;

use App\Application\Score\Command\SubmitScoreCommand;
use App\Application\Score\Command\SubmitScoreHandler;
use App\Application\Score\Exception\ScoreValidationException;
use App\Infrastructure\Score\InMemoryScoreRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class SubmitScoreHandlerTest extends TestCase
{
    private SubmitScoreHandler $handler;
    private InMemoryScoreRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryScoreRepository();
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $this->handler = new SubmitScoreHandler($this->repository, $validator);
    }

    public function testHandlePersistsAndReturnsScore(): void
    {
        $command = new SubmitScoreCommand('Alice', 200.5);
        $score = $this->handler->handle($command);

        self::assertSame('Alice', $score->playerName()->value());
        self::assertSame(200.5, $score->reactionTime()->toMilliseconds());
        self::assertCount(1, $this->repository->topScores(10));
    }

    public function testHandleWithInvalidDataThrowsException(): void
    {
        $this->expectException(ScoreValidationException::class);

        $this->handler->handle(new SubmitScoreCommand('', 0));
    }
}
