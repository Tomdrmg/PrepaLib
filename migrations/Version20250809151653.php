<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250809151653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hint (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, exercise_id INT NOT NULL, lore VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_34C603531F1F2A24 (element_id), INDEX IDX_34C60353E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hint ADD CONSTRAINT FK_34C603531F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE hint ADD CONSTRAINT FK_34C60353E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hint DROP FOREIGN KEY FK_34C603531F1F2A24');
        $this->addSql('ALTER TABLE hint DROP FOREIGN KEY FK_34C60353E934951A');
        $this->addSql('DROP TABLE hint');
    }
}
