<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251019211146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revision_question (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, answer_id INT NOT NULL, revision_element_id INT NOT NULL, UNIQUE INDEX UNIQ_FCAB89C11E27F6BF (question_id), UNIQUE INDEX UNIQ_FCAB89C1AA334807 (answer_id), INDEX IDX_FCAB89C13860264C (revision_element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE revision_question ADD CONSTRAINT FK_FCAB89C11E27F6BF FOREIGN KEY (question_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_question ADD CONSTRAINT FK_FCAB89C1AA334807 FOREIGN KEY (answer_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_question ADD CONSTRAINT FK_FCAB89C13860264C FOREIGN KEY (revision_element_id) REFERENCES revision_element (id)');
        $this->addSql('ALTER TABLE revision_element DROP ask_first, DROP ask_second');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE revision_question DROP FOREIGN KEY FK_FCAB89C11E27F6BF');
        $this->addSql('ALTER TABLE revision_question DROP FOREIGN KEY FK_FCAB89C1AA334807');
        $this->addSql('ALTER TABLE revision_question DROP FOREIGN KEY FK_FCAB89C13860264C');
        $this->addSql('DROP TABLE revision_question');
        $this->addSql('ALTER TABLE revision_element ADD ask_first TINYINT(1) NOT NULL, ADD ask_second TINYINT(1) NOT NULL');
    }
}
