<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Score\Score;
use App\Domain\Score\ScoreRepository;
use App\Infrastructure\Persistence\Doctrine\Entity\ScoreRecord;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class DoctrineScoreRepository implements ScoreRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function add(Score $score): void
    {
        $record = ScoreRecord::fromDomain($score);
        $this->entityManager->persist($record);
        $this->entityManager->flush();
    }

    public function topScores(int $limit): array
    {
        $limit = max(0, $limit);

        if ($limit === 0) {
            return [];
        }

        /** @var list<ScoreRecord> $records */
        $records = $this->createLeaderboardQueryBuilder()
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return array_map(static fn (ScoreRecord $record): Score => $record->toDomain(), $records);
    }

    private function createLeaderboardQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('score')
            ->from(ScoreRecord::class, 'score')
            ->orderBy('score.reactionTimeMs', 'ASC')
            ->addOrderBy('score.recordedAt', 'ASC')
            ->addOrderBy('score.id', 'ASC');
    }
}
