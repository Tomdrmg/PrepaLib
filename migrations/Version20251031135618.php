<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031135618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_data ADD subject_id INT NOT NULL, DROP failures, DROP success');
        $this->addSql('ALTER TABLE quiz_data ADD CONSTRAINT FK_67020F3823EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('CREATE INDEX IDX_67020F3823EDC87 ON quiz_data (subject_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quiz_data DROP FOREIGN KEY FK_67020F3823EDC87');
        $this->addSql('DROP INDEX IDX_67020F3823EDC87 ON quiz_data');
        $this->addSql('ALTER TABLE quiz_data ADD success INT NOT NULL, CHANGE subject_id failures INT NOT NULL');
    }
}
