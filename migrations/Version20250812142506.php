<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812142506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element ADD exercise_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E394E44EED FOREIGN KEY (exercise_answer_id) REFERENCES exercise (id)');
        $this->addSql('CREATE INDEX IDX_41405E394E44EED ON element (exercise_answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E394E44EED');
        $this->addSql('DROP INDEX IDX_41405E394E44EED ON element');
        $this->addSql('ALTER TABLE element DROP exercise_answer_id');
    }
}
