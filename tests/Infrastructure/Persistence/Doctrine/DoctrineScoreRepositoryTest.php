<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence\Doctrine;

use App\Application\Score\ScoreService;
use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;
use App\Domain\Score\ValueObject\DisplayName;
use App\Domain\Score\ValueObject\ReactionTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineScoreRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ScoreRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(ScoreRepository::class);

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        if (isset($this->entityManager)) {
            $this->entityManager->clear();
            $this->entityManager->getConnection()->close();
            unset($this->entityManager);
        }

        parent::tearDown();
    }

    public function testPersistsAndReturnsScoresOrderedByReactionTime(): void
    {
        $scores = [
            new Score(new DisplayName('Alice'), new ReactionTime(180), new DateTimeImmutable('2024-01-01T10:00:00Z')),
            new Score(new DisplayName('Bob'), new ReactionTime(150), new DateTimeImmutable('2024-01-01T09:00:00Z')),
            new Score(new DisplayName('Charlie'), new ReactionTime(150), new DateTimeImmutable('2024-01-01T09:05:00Z')),
            new Score(new DisplayName('Dana'), new ReactionTime(210), new DateTimeImmutable('2024-01-01T08:00:00Z')),
        ];

        foreach ($scores as $score) {
            $this->repository->add($score);
        }

        $results = $this->repository->topScores(10);

        self::assertCount(4, $results);
        self::assertSame('Bob', $results[0]->playerName()->value());
        self::assertSame('Charlie', $results[1]->playerName()->value());
        self::assertSame('Alice', $results[2]->playerName()->value());
        self::assertSame('Dana', $results[3]->playerName()->value());
    }

    public function testLeaderboardLimitIsRespected(): void
    {
        foreach (range(1, 12) as $i) {
            $this->repository->add(
                new Score(
                    new DisplayName('Player '.$i),
                    new ReactionTime(100 + $i),
                    new DateTimeImmutable('2024-01-01T00:00:'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'Z')
                )
            );
        }

        $results = $this->repository->topScores(10);

        self::assertCount(10, $results);
        self::assertSame('Player 1', $results[0]->playerName()->value());
        self::assertSame('Player 10', $results[9]->playerName()->value());
    }

    public function testLeaderboardReturnsEmptyArrayForZeroLimit(): void
    {
        $this->repository->add(
            new Score(
                new DisplayName('Solo'),
                new ReactionTime(123),
                new DateTimeImmutable('2024-01-01T00:00:00Z')
            )
        );

        self::assertSame([], $this->repository->topScores(0));
    }

    public function testScoreServiceUsesDoctrineRepository(): void
    {
        $container = static::getContainer();
        /** @var ScoreService $service */
        $service = $container->get(ScoreService::class);
        $score = $service->submitScore('Integration Player', 145);

        $results = $this->repository->topScores(10);

        self::assertCount(1, $results);
        self::assertSame('Integration Player', $results[0]->playerName()->value());
        self::assertSame(145, $results[0]->reactionTime()->toMilliseconds());
        self::assertEquals($score->recordedAt(), $results[0]->recordedAt());
    }
}
