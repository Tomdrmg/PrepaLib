<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251019150711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E591CC992');
        $this->addSql('ALTER TABLE subject_essential DROP FOREIGN KEY FK_A971FF9E23EDC87');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB923EDC87');
        $this->addSql('ALTER TABLE course_element_tag DROP FOREIGN KEY FK_202F143FBAD26311');
        $this->addSql('ALTER TABLE course_element_tag DROP FOREIGN KEY FK_202F143FC0FD64A7');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5579F4768');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5849CB65B');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5D7086615');
        $this->addSql('ALTER TABLE essential_part DROP FOREIGN KEY FK_8196838D197101B2');
        $this->addSql('ALTER TABLE essential_part DROP FOREIGN KEY FK_8196838D1F1F2A24');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE subject_essential');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE course_element_tag');
        $this->addSql('DROP TABLE course_element');
        $this->addSql('DROP TABLE essential_part');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapter (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_F981B52E591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE subject_essential (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, UNIQUE INDEX UNIQ_A971FF9E23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_169E6FB923EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE course_element_tag (course_element_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_202F143FC0FD64A7 (course_element_id), INDEX IDX_202F143FBAD26311 (tag_id), PRIMARY KEY(course_element_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE course_element (id INT AUTO_INCREMENT NOT NULL, statement_id INT NOT NULL, proof_id INT NOT NULL, chapter_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_49835BD5849CB65B (statement_id), UNIQUE INDEX UNIQ_49835BD5D7086615 (proof_id), INDEX IDX_49835BD5579F4768 (chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE essential_part (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, essential_id INT DEFAULT NULL, title VARCHAR(1000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, sort_number INT NOT NULL, UNIQUE INDEX UNIQ_8196838D1F1F2A24 (element_id), INDEX IDX_8196838D197101B2 (essential_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subject_essential ADD CONSTRAINT FK_A971FF9E23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB923EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE course_element_tag ADD CONSTRAINT FK_202F143FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_element_tag ADD CONSTRAINT FK_202F143FC0FD64A7 FOREIGN KEY (course_element_id) REFERENCES course_element (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5849CB65B FOREIGN KEY (statement_id) REFERENCES element (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5D7086615 FOREIGN KEY (proof_id) REFERENCES element (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE essential_part ADD CONSTRAINT FK_8196838D197101B2 FOREIGN KEY (essential_id) REFERENCES subject_essential (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE essential_part ADD CONSTRAINT FK_8196838D1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
