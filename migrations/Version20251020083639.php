<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020083639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE revision_element ADD revision_sheet_id INT NOT NULL');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905DC14EC62A FOREIGN KEY (revision_sheet_id) REFERENCES revision_sheet (id)');
        $this->addSql('CREATE INDEX IDX_C225905DC14EC62A ON revision_element (revision_sheet_id)');
        $this->addSql('ALTER TABLE revision_question CHANGE revision_element_id revision_element_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE revision_question CHANGE revision_element_id revision_element_id INT NOT NULL');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905DC14EC62A');
        $this->addSql('DROP INDEX IDX_C225905DC14EC62A ON revision_element');
        $this->addSql('ALTER TABLE revision_element DROP revision_sheet_id');
    }
}
