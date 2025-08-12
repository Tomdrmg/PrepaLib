<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812160216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE essential_part (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, essential_id INT DEFAULT NULL, title VARCHAR(1000) NOT NULL, sort_number INT NOT NULL, UNIQUE INDEX UNIQ_8196838D1F1F2A24 (element_id), INDEX IDX_8196838D197101B2 (essential_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE essential_part ADD CONSTRAINT FK_8196838D1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE essential_part ADD CONSTRAINT FK_8196838D197101B2 FOREIGN KEY (essential_id) REFERENCES subject_essential (id)');
        $this->addSql('ALTER TABLE essential_category DROP FOREIGN KEY FK_C8E770A5197101B2');
        $this->addSql('ALTER TABLE essential_category DROP FOREIGN KEY FK_C8E770A51F1F2A24');
        $this->addSql('DROP TABLE essential_category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE essential_category (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, essential_id INT DEFAULT NULL, title VARCHAR(1000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sort_number INT NOT NULL, UNIQUE INDEX UNIQ_C8E770A51F1F2A24 (element_id), INDEX IDX_C8E770A5197101B2 (essential_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE essential_category ADD CONSTRAINT FK_C8E770A5197101B2 FOREIGN KEY (essential_id) REFERENCES subject_essential (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE essential_category ADD CONSTRAINT FK_C8E770A51F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE essential_part DROP FOREIGN KEY FK_8196838D1F1F2A24');
        $this->addSql('ALTER TABLE essential_part DROP FOREIGN KEY FK_8196838D197101B2');
        $this->addSql('DROP TABLE essential_part');
    }
}
