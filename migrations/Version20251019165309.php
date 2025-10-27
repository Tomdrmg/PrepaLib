<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251019165309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revision_element (id INT AUTO_INCREMENT NOT NULL, first_id INT NOT NULL, second_id INT NOT NULL, details_id INT DEFAULT NULL, sort_number INT NOT NULL, ask_first TINYINT(1) NOT NULL, ask_second TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C225905DE84D625F (first_id), UNIQUE INDEX UNIQ_C225905DFF961BCC (second_id), UNIQUE INDEX UNIQ_C225905DBB1A0722 (details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revision_pref (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, revision_element_id INT NOT NULL, difficulty INT NOT NULL, INDEX IDX_2D902ACEA76ED395 (user_id), INDEX IDX_2D902ACE3860264C (revision_element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revision_sheet (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, subject_id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_number INT NOT NULL, INDEX IDX_AAB2BBDD727ACA70 (parent_id), INDEX IDX_AAB2BBDD23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905DE84D625F FOREIGN KEY (first_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905DFF961BCC FOREIGN KEY (second_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905DBB1A0722 FOREIGN KEY (details_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_pref ADD CONSTRAINT FK_2D902ACEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE revision_pref ADD CONSTRAINT FK_2D902ACE3860264C FOREIGN KEY (revision_element_id) REFERENCES revision_element (id)');
        $this->addSql('ALTER TABLE revision_sheet ADD CONSTRAINT FK_AAB2BBDD727ACA70 FOREIGN KEY (parent_id) REFERENCES revision_sheet (id)');
        $this->addSql('ALTER TABLE revision_sheet ADD CONSTRAINT FK_AAB2BBDD23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE tag ADD revision_sheet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783C14EC62A FOREIGN KEY (revision_sheet_id) REFERENCES revision_sheet (id)');
        $this->addSql('CREATE INDEX IDX_389B783C14EC62A ON tag (revision_sheet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783C14EC62A');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905DE84D625F');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905DFF961BCC');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905DBB1A0722');
        $this->addSql('ALTER TABLE revision_pref DROP FOREIGN KEY FK_2D902ACEA76ED395');
        $this->addSql('ALTER TABLE revision_pref DROP FOREIGN KEY FK_2D902ACE3860264C');
        $this->addSql('ALTER TABLE revision_sheet DROP FOREIGN KEY FK_AAB2BBDD727ACA70');
        $this->addSql('ALTER TABLE revision_sheet DROP FOREIGN KEY FK_AAB2BBDD23EDC87');
        $this->addSql('DROP TABLE revision_element');
        $this->addSql('DROP TABLE revision_pref');
        $this->addSql('DROP TABLE revision_sheet');
        $this->addSql('DROP INDEX IDX_389B783C14EC62A ON tag');
        $this->addSql('ALTER TABLE tag DROP revision_sheet_id');
    }
}
