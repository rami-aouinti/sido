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

    /**
     * @dataProvider invalidSubmissionProvider
     */
    public function testHandleWithInvalidDataThrowsException(string $name, float $reactionTime): void
    {
        $this->expectException(ScoreValidationException::class);

        $this->handler->handle(new SubmitScoreCommand($name, $reactionTime));
    }

    /**
     * @return iterable<string, array{string, float}>
     */
    public static function invalidSubmissionProvider(): iterable
    {
        yield 'empty name' => ['', 0.0];
        yield 'name too short' => ['Al', 150.0];
        yield 'name too long' => [str_repeat('a', 31), 150.0];
        yield 'name invalid characters' => ['Alice!', 150.0];
        yield 'reaction time too small' => ['Alice', 0.5];
        yield 'reaction time too large' => ['Alice', 10001.0];
    }
}
