<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240213000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create scores table for leaderboard storage';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('scores');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('player_name', 'string', ['length' => 255]);
        $table->addColumn('reaction_time_ms', 'float');
        $table->addColumn('recorded_at', 'datetime_immutable');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['reaction_time_ms', 'recorded_at', 'id'], 'idx_scores_reaction_time_recorded_at');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('scores');
    }
}
