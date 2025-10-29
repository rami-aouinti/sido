<?php

declare(strict_types=1);

namespace App\Tests\Application\Score;

use App\Application\Score\Command\SubmitScoreCommand;
use App\Application\Score\Command\SubmitScoreHandler;
use App\Application\Score\Exception\ScoreValidationException;
use App\Infrastructure\Messaging\MercureTop10Publisher;
use App\Infrastructure\Score\InMemoryScoreRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use const JSON_THROW_ON_ERROR;

final class SubmitScoreHandlerTest extends TestCase
{
    private SubmitScoreHandler $handler;
    private InMemoryScoreRepository $repository;
    private MercureTop10Publisher $publisher;
    private HubInterface $hub;

    protected function setUp(): void
    {
        $this->repository = new InMemoryScoreRepository();
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
        $this->hub = $this->createMock(HubInterface::class);
        $this->publisher = new MercureTop10Publisher($this->hub);

        $this->handler = new SubmitScoreHandler($this->repository, $validator, $this->publisher);
    }

    public function testHandlePersistsAndReturnsScore(): void
    {
        $this->hub
            ->expects(self::once())
            ->method('publish')
            ->with(self::callback(function (Update $update): bool {
                self::assertSame([MercureTop10Publisher::TOPIC], $update->getTopics());

                $payload = json_decode($update->getData(), true, flags: JSON_THROW_ON_ERROR);
                self::assertIsArray($payload);
                self::assertArrayHasKey('scores', $payload);
                self::assertCount(1, $payload['scores']);
                self::assertSame('Alice', $payload['scores'][0]['name']);
                self::assertSame(200.5, $payload['scores'][0]['reactionTime']);

                return true;
            }));

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
