<?php

declare(strict_types=1);

namespace App\Tests\Application\Score;

use App\Application\Score\Command\SubmitScoreCommand;
use App\Application\Score\Command\SubmitScoreHandler;
use App\Application\Score\Query\GetTopScoresHandler;
use App\Application\Score\Query\GetTopScoresQuery;
use App\Application\Score\TopScorePublisher;
use App\Infrastructure\Score\InMemoryScoreRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class GetTopScoresHandlerTest extends TestCase
{
    private SubmitScoreHandler $submitHandler;
    private GetTopScoresHandler $queryHandler;

    protected function setUp(): void
    {
        $repository = new InMemoryScoreRepository();
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $publisher = new class() implements TopScorePublisher {
            public array $publishedScores = [];

            public function publish(array $scores): void
            {
                $this->publishedScores = $scores;
            }
        };

        $this->submitHandler = new SubmitScoreHandler($repository, $validator, $publisher);
        $this->queryHandler = new GetTopScoresHandler($repository);
    }

    public function testHandleReturnsScoresSortedAscending(): void
    {
        $this->submitHandler->handle(new SubmitScoreCommand('Bob', 300));
        $this->submitHandler->handle(new SubmitScoreCommand('Alice', 200));
        $this->submitHandler->handle(new SubmitScoreCommand('Charlie', 250));

        $scores = $this->queryHandler->handle(new GetTopScoresQuery());

        self::assertCount(3, $scores);
        self::assertSame('Alice', $scores[0]->playerName()->value());
        self::assertSame('Charlie', $scores[1]->playerName()->value());
        self::assertSame('Bob', $scores[2]->playerName()->value());
    }
}
