<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031001017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz_data (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, question_count INT NOT NULL, unknown_weight INT NOT NULL, familiar_weight INT NOT NULL, known_weight INT NOT NULL, mastered_weight INT NOT NULL, failures INT NOT NULL, success INT NOT NULL, UNIQUE INDEX UNIQ_67020F38A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_data_revision_sheet (quiz_data_id INT NOT NULL, revision_sheet_id INT NOT NULL, INDEX IDX_1FE70F3E25AF0C9C (quiz_data_id), INDEX IDX_1FE70F3EC14EC62A (revision_sheet_id), PRIMARY KEY(quiz_data_id, revision_sheet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz_data ADD CONSTRAINT FK_67020F38A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet ADD CONSTRAINT FK_1FE70F3E25AF0C9C FOREIGN KEY (quiz_data_id) REFERENCES quiz_data (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet ADD CONSTRAINT FK_1FE70F3EC14EC62A FOREIGN KEY (revision_sheet_id) REFERENCES revision_sheet (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_data DROP FOREIGN KEY FK_67020F38A76ED395');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet DROP FOREIGN KEY FK_1FE70F3E25AF0C9C');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet DROP FOREIGN KEY FK_1FE70F3EC14EC62A');
        $this->addSql('DROP TABLE quiz_data');
        $this->addSql('DROP TABLE quiz_data_revision_sheet');
    }
}
