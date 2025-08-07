<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250804091335 extends AbstractMigration
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
        $this->addSql('CREATE TABLE course_element (id INT AUTO_INCREMENT NOT NULL, statement_id INT NOT NULL, proof_id INT NOT NULL, chapter_id INT NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_49835BD5849CB65B (statement_id), UNIQUE INDEX UNIQ_49835BD5D7086615 (proof_id), INDEX IDX_49835BD5579F4768 (chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_element_tag (course_element_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_202F143FC0FD64A7 (course_element_id), INDEX IDX_202F143FBAD26311 (tag_id), PRIMARY KEY(course_element_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element (id INT AUTO_INCREMENT NOT NULL, exercise_id INT DEFAULT NULL, content LONGTEXT NOT NULL, INDEX IDX_41405E39E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, statement_id INT NOT NULL, solution_id INT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_AEDAD51C849CB65B (statement_id), UNIQUE INDEX UNIQ_AEDAD51C1C0BE183 (solution_id), INDEX IDX_AEDAD51C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_tag (exercise_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_95D612FFE934951A (exercise_id), INDEX IDX_95D612FFBAD26311 (tag_id), PRIMARY KEY(exercise_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_category (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, parent_id INT DEFAULT NULL, color VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_20B92923EDC87 (subject_id), INDEX IDX_20B929727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_category_tag (exercise_category_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_CCD3AFF05FB48D66 (exercise_category_id), INDEX IDX_CCD3AFF0BAD26311 (tag_id), PRIMARY KEY(exercise_category_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_pref (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exercise_id INT NOT NULL, favorite TINYINT(1) NOT NULL, todo TINYINT(1) NOT NULL, comment VARCHAR(2000) NOT NULL, done TINYINT(1) NOT NULL, difficulty INT NOT NULL, INDEX IDX_F870FA83A76ED395 (user_id), INDEX IDX_F870FA83E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, faicon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_token_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB923EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5849CB65B FOREIGN KEY (statement_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5D7086615 FOREIGN KEY (proof_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE course_element ADD CONSTRAINT FK_49835BD5579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE course_element_tag ADD CONSTRAINT FK_202F143FC0FD64A7 FOREIGN KEY (course_element_id) REFERENCES course_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_element_tag ADD CONSTRAINT FK_202F143FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C849CB65B FOREIGN KEY (statement_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C1C0BE183 FOREIGN KEY (solution_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C12469DE2 FOREIGN KEY (category_id) REFERENCES exercise_category (id)');
        $this->addSql('ALTER TABLE exercise_tag ADD CONSTRAINT FK_95D612FFE934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_tag ADD CONSTRAINT FK_95D612FFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_category ADD CONSTRAINT FK_20B92923EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE exercise_category ADD CONSTRAINT FK_20B929727ACA70 FOREIGN KEY (parent_id) REFERENCES exercise_category (id)');
        $this->addSql('ALTER TABLE exercise_category_tag ADD CONSTRAINT FK_CCD3AFF05FB48D66 FOREIGN KEY (exercise_category_id) REFERENCES exercise_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_category_tag ADD CONSTRAINT FK_CCD3AFF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_pref ADD CONSTRAINT FK_F870FA83A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE exercise_pref ADD CONSTRAINT FK_F870FA83E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E591CC992');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB923EDC87');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5849CB65B');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5D7086615');
        $this->addSql('ALTER TABLE course_element DROP FOREIGN KEY FK_49835BD5579F4768');
        $this->addSql('ALTER TABLE course_element_tag DROP FOREIGN KEY FK_202F143FC0FD64A7');
        $this->addSql('ALTER TABLE course_element_tag DROP FOREIGN KEY FK_202F143FBAD26311');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39E934951A');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C849CB65B');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C1C0BE183');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C12469DE2');
        $this->addSql('ALTER TABLE exercise_tag DROP FOREIGN KEY FK_95D612FFE934951A');
        $this->addSql('ALTER TABLE exercise_tag DROP FOREIGN KEY FK_95D612FFBAD26311');
        $this->addSql('ALTER TABLE exercise_category DROP FOREIGN KEY FK_20B92923EDC87');
        $this->addSql('ALTER TABLE exercise_category DROP FOREIGN KEY FK_20B929727ACA70');
        $this->addSql('ALTER TABLE exercise_category_tag DROP FOREIGN KEY FK_CCD3AFF05FB48D66');
        $this->addSql('ALTER TABLE exercise_category_tag DROP FOREIGN KEY FK_CCD3AFF0BAD26311');
        $this->addSql('ALTER TABLE exercise_pref DROP FOREIGN KEY FK_F870FA83A76ED395');
        $this->addSql('ALTER TABLE exercise_pref DROP FOREIGN KEY FK_F870FA83E934951A');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE course_element');
        $this->addSql('DROP TABLE course_element_tag');
        $this->addSql('DROP TABLE element');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exercise_tag');
        $this->addSql('DROP TABLE exercise_category');
        $this->addSql('DROP TABLE exercise_category_tag');
        $this->addSql('DROP TABLE exercise_pref');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
