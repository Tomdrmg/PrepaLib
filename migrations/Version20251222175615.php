<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251222175615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE element (id INT AUTO_INCREMENT NOT NULL, exercise_id INT DEFAULT NULL, exercise_answer_id INT DEFAULT NULL, content LONGTEXT NOT NULL, INDEX IDX_41405E39E934951A (exercise_id), INDEX IDX_41405E394E44EED (exercise_answer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, statement_id INT NOT NULL, solution_id INT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_number INT NOT NULL, UNIQUE INDEX UNIQ_AEDAD51C849CB65B (statement_id), UNIQUE INDEX UNIQ_AEDAD51C1C0BE183 (solution_id), INDEX IDX_AEDAD51C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_tag (exercise_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_95D612FFE934951A (exercise_id), INDEX IDX_95D612FFBAD26311 (tag_id), PRIMARY KEY(exercise_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_category (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, parent_id INT DEFAULT NULL, color VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, sort_number INT NOT NULL, INDEX IDX_20B92923EDC87 (subject_id), INDEX IDX_20B929727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_category_tag (exercise_category_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_CCD3AFF05FB48D66 (exercise_category_id), INDEX IDX_CCD3AFF0BAD26311 (tag_id), PRIMARY KEY(exercise_category_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exercise_pref (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exercise_id INT NOT NULL, favorite TINYINT(1) NOT NULL, comment VARCHAR(2000) NOT NULL, done TINYINT(1) NOT NULL, difficulty INT NOT NULL, INDEX IDX_F870FA83A76ED395 (user_id), INDEX IDX_F870FA83E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lored_element (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, hint_for_id INT DEFAULT NULL, answer_for_id INT DEFAULT NULL, lore VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1DE42DD1F1F2A24 (element_id), INDEX IDX_1DE42DD58BDCDC7 (hint_for_id), INDEX IDX_1DE42DDDEC2E3EA (answer_for_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_data (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, subject_id INT NOT NULL, question_count INT NOT NULL, unknown_weight INT NOT NULL, familiar_weight INT NOT NULL, known_weight INT NOT NULL, mastered_weight INT NOT NULL, never_seen_weight INT NOT NULL, UNIQUE INDEX UNIQ_67020F38A76ED395 (user_id), INDEX IDX_67020F3823EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_data_revision_sheet (quiz_data_id INT NOT NULL, revision_sheet_id INT NOT NULL, INDEX IDX_1FE70F3E25AF0C9C (quiz_data_id), INDEX IDX_1FE70F3EC14EC62A (revision_sheet_id), PRIMARY KEY(quiz_data_id, revision_sheet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revision_element (id INT AUTO_INCREMENT NOT NULL, content_id INT NOT NULL, details_id INT DEFAULT NULL, revision_sheet_id INT NOT NULL, sort_number INT NOT NULL, UNIQUE INDEX UNIQ_C225905D84A0A3ED (content_id), UNIQUE INDEX UNIQ_C225905DBB1A0722 (details_id), INDEX IDX_C225905DC14EC62A (revision_sheet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revision_pref (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, revision_element_id INT NOT NULL, difficulty INT NOT NULL, INDEX IDX_2D902ACEA76ED395 (user_id), INDEX IDX_2D902ACE3860264C (revision_element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revision_question (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, answer_id INT NOT NULL, revision_element_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_FCAB89C11E27F6BF (question_id), UNIQUE INDEX UNIQ_FCAB89C1AA334807 (answer_id), INDEX IDX_FCAB89C13860264C (revision_element_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revision_sheet (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, subject_id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_number INT NOT NULL, INDEX IDX_AAB2BBDD727ACA70 (parent_id), INDEX IDX_AAB2BBDD23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, faicon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, revision_sheet_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(7) NOT NULL, INDEX IDX_389B783C14EC62A (revision_sheet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_token_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', wants_news TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E394E44EED FOREIGN KEY (exercise_answer_id) REFERENCES exercise (id)');
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
        $this->addSql('ALTER TABLE lored_element ADD CONSTRAINT FK_1DE42DD1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE lored_element ADD CONSTRAINT FK_1DE42DD58BDCDC7 FOREIGN KEY (hint_for_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE lored_element ADD CONSTRAINT FK_1DE42DDDEC2E3EA FOREIGN KEY (answer_for_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE quiz_data ADD CONSTRAINT FK_67020F38A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE quiz_data ADD CONSTRAINT FK_67020F3823EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet ADD CONSTRAINT FK_1FE70F3E25AF0C9C FOREIGN KEY (quiz_data_id) REFERENCES quiz_data (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet ADD CONSTRAINT FK_1FE70F3EC14EC62A FOREIGN KEY (revision_sheet_id) REFERENCES revision_sheet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905D84A0A3ED FOREIGN KEY (content_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905DBB1A0722 FOREIGN KEY (details_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_element ADD CONSTRAINT FK_C225905DC14EC62A FOREIGN KEY (revision_sheet_id) REFERENCES revision_sheet (id)');
        $this->addSql('ALTER TABLE revision_pref ADD CONSTRAINT FK_2D902ACEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE revision_pref ADD CONSTRAINT FK_2D902ACE3860264C FOREIGN KEY (revision_element_id) REFERENCES revision_element (id)');
        $this->addSql('ALTER TABLE revision_question ADD CONSTRAINT FK_FCAB89C11E27F6BF FOREIGN KEY (question_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_question ADD CONSTRAINT FK_FCAB89C1AA334807 FOREIGN KEY (answer_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE revision_question ADD CONSTRAINT FK_FCAB89C13860264C FOREIGN KEY (revision_element_id) REFERENCES revision_element (id)');
        $this->addSql('ALTER TABLE revision_sheet ADD CONSTRAINT FK_AAB2BBDD727ACA70 FOREIGN KEY (parent_id) REFERENCES revision_sheet (id)');
        $this->addSql('ALTER TABLE revision_sheet ADD CONSTRAINT FK_AAB2BBDD23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783C14EC62A FOREIGN KEY (revision_sheet_id) REFERENCES revision_sheet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39E934951A');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E394E44EED');
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
        $this->addSql('ALTER TABLE lored_element DROP FOREIGN KEY FK_1DE42DD1F1F2A24');
        $this->addSql('ALTER TABLE lored_element DROP FOREIGN KEY FK_1DE42DD58BDCDC7');
        $this->addSql('ALTER TABLE lored_element DROP FOREIGN KEY FK_1DE42DDDEC2E3EA');
        $this->addSql('ALTER TABLE quiz_data DROP FOREIGN KEY FK_67020F38A76ED395');
        $this->addSql('ALTER TABLE quiz_data DROP FOREIGN KEY FK_67020F3823EDC87');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet DROP FOREIGN KEY FK_1FE70F3E25AF0C9C');
        $this->addSql('ALTER TABLE quiz_data_revision_sheet DROP FOREIGN KEY FK_1FE70F3EC14EC62A');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905D84A0A3ED');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905DBB1A0722');
        $this->addSql('ALTER TABLE revision_element DROP FOREIGN KEY FK_C225905DC14EC62A');
        $this->addSql('ALTER TABLE revision_pref DROP FOREIGN KEY FK_2D902ACEA76ED395');
        $this->addSql('ALTER TABLE revision_pref DROP FOREIGN KEY FK_2D902ACE3860264C');
        $this->addSql('ALTER TABLE revision_question DROP FOREIGN KEY FK_FCAB89C11E27F6BF');
        $this->addSql('ALTER TABLE revision_question DROP FOREIGN KEY FK_FCAB89C1AA334807');
        $this->addSql('ALTER TABLE revision_question DROP FOREIGN KEY FK_FCAB89C13860264C');
        $this->addSql('ALTER TABLE revision_sheet DROP FOREIGN KEY FK_AAB2BBDD727ACA70');
        $this->addSql('ALTER TABLE revision_sheet DROP FOREIGN KEY FK_AAB2BBDD23EDC87');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783C14EC62A');
        $this->addSql('DROP TABLE element');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exercise_tag');
        $this->addSql('DROP TABLE exercise_category');
        $this->addSql('DROP TABLE exercise_category_tag');
        $this->addSql('DROP TABLE exercise_pref');
        $this->addSql('DROP TABLE lored_element');
        $this->addSql('DROP TABLE quiz_data');
        $this->addSql('DROP TABLE quiz_data_revision_sheet');
        $this->addSql('DROP TABLE revision_element');
        $this->addSql('DROP TABLE revision_pref');
        $this->addSql('DROP TABLE revision_question');
        $this->addSql('DROP TABLE revision_sheet');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
