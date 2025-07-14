<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250714140307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapter (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_F981B52E591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_169E6FB923EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_element (id INT AUTO_INCREMENT NOT NULL, statement_id INT NOT NULL, proof_id INT NOT NULL, tags_id INT NOT NULL, chapter_id INT NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_49835BD5849CB65B (statement_id), UNIQUE INDEX UNIQ_49835BD5D7086615 (proof_id), UNIQUE INDEX UNIQ_49835BD58D7B4FB4 (tags_id), INDEX IDX_49835BD5579F4768 (chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element_list (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, statement_id INT NOT NULL, solution_id INT NOT NULL, hints_id INT NOT NULL, tags_id INT NOT NULL, exercise_group_id INT NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_AEDAD51C849CB65B (statement_id), UNIQUE INDEX UNIQ_AEDAD51C1C0BE183 (solution_id), UNIQUE INDEX UNIQ_AEDAD51CA6245624 (hints_id), UNIQUE INDEX UNIQ_AEDAD51C8D7B4FB4 (tags_id), INDEX IDX_AEDAD51CF83879AF (exercise_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_group (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_48F6B30723EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_pref (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exercise_id INT NOT NULL, favorite TINYINT(1) NOT NULL, todo TINYINT(1) NOT NULL, comment VARCHAR(2000) NOT NULL, INDEX IDX_F870FA83A76ED395 (user_id), INDEX IDX_F870FA83E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, tag_list_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, color INT NOT NULL, INDEX IDX_389B783F9F3B21 (tag_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_list (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB923EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5849CB65B FOREIGN KEY (statement_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5D7086615 FOREIGN KEY (proof_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD58D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tag_list (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C849CB65B FOREIGN KEY (statement_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C1C0BE183 FOREIGN KEY (solution_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51CA6245624 FOREIGN KEY (hints_id) REFERENCES element_list (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tag_list (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51CF83879AF FOREIGN KEY (exercise_group_id) REFERENCES exercise_group (id)');
        $this->addSql('ALTER TABLE exercise_group ADD CONSTRAINT FK_48F6B30723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE exercise_pref ADD CONSTRAINT FK_F870FA83A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE exercise_pref ADD CONSTRAINT FK_F870FA83E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783F9F3B21 FOREIGN KEY (tag_list_id) REFERENCES tag_list (id)');
        $this->addSql('ALTER TABLE element ADD element_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39CA4556DF FOREIGN KEY (element_list_id) REFERENCES element_list (id)');
        $this->addSql('CREATE INDEX IDX_41405E39CA4556DF ON element (element_list_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39CA4556DF');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E591CC992');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB923EDC87');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5849CB65B');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5D7086615');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD58D7B4FB4');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5579F4768');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C849CB65B');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C1C0BE183');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51CA6245624');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C8D7B4FB4');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51CF83879AF');
        $this->addSql('ALTER TABLE exercise_group DROP FOREIGN KEY FK_48F6B30723EDC87');
        $this->addSql('ALTER TABLE exercise_pref DROP FOREIGN KEY FK_F870FA83A76ED395');
        $this->addSql('ALTER TABLE exercise_pref DROP FOREIGN KEY FK_F870FA83E934951A');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783F9F3B21');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE course_element');
        $this->addSql('DROP TABLE element_list');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exercise_group');
        $this->addSql('DROP TABLE exercise_pref');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_list');
        $this->addSql('DROP INDEX IDX_41405E39CA4556DF ON element');
        $this->addSql('ALTER TABLE element DROP element_list_id');
    }
}
