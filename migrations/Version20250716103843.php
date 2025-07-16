<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716103843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_list_tag (tag_list_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_243B5075F9F3B21 (tag_list_id), INDEX IDX_243B5075BAD26311 (tag_id), PRIMARY KEY(tag_list_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_list_tag ADD CONSTRAINT FK_243B5075F9F3B21 FOREIGN KEY (tag_list_id) REFERENCES tag_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_list_tag ADD CONSTRAINT FK_243B5075BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783F9F3B21');
        $this->addSql('DROP INDEX IDX_389B783F9F3B21 ON tag');
        $this->addSql('ALTER TABLE tag DROP tag_list_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_list_tag DROP FOREIGN KEY FK_243B5075F9F3B21');
        $this->addSql('ALTER TABLE tag_list_tag DROP FOREIGN KEY FK_243B5075BAD26311');
        $this->addSql('DROP TABLE tag_list_tag');
        $this->addSql('ALTER TABLE tag ADD tag_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783F9F3B21 FOREIGN KEY (tag_list_id) REFERENCES tag_list (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_389B783F9F3B21 ON tag (tag_list_id)');
    }
}
