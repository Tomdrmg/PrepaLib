<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812153941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE essential_category (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, essential_id INT DEFAULT NULL, title VARCHAR(1000) NOT NULL, UNIQUE INDEX UNIQ_C8E770A51F1F2A24 (element_id), INDEX IDX_C8E770A5197101B2 (essential_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject_essential (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, UNIQUE INDEX UNIQ_A971FF9E23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE essential_category ADD CONSTRAINT FK_C8E770A51F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE essential_category ADD CONSTRAINT FK_C8E770A5197101B2 FOREIGN KEY (essential_id) REFERENCES subject_essential (id)');
        $this->addSql('ALTER TABLE subject_essential ADD CONSTRAINT FK_A971FF9E23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE essential_category DROP FOREIGN KEY FK_C8E770A51F1F2A24');
        $this->addSql('ALTER TABLE essential_category DROP FOREIGN KEY FK_C8E770A5197101B2');
        $this->addSql('ALTER TABLE subject_essential DROP FOREIGN KEY FK_A971FF9E23EDC87');
        $this->addSql('DROP TABLE essential_category');
        $this->addSql('DROP TABLE subject_essential');
    }
}
