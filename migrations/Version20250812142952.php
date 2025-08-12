<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250812142952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lored_element (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, hint_for_id INT DEFAULT NULL, answer_for_id INT DEFAULT NULL, lore VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1DE42DD1F1F2A24 (element_id), INDEX IDX_1DE42DD58BDCDC7 (hint_for_id), INDEX IDX_1DE42DDDEC2E3EA (answer_for_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lored_element ADD CONSTRAINT FK_1DE42DD1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE lored_element ADD CONSTRAINT FK_1DE42DD58BDCDC7 FOREIGN KEY (hint_for_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE lored_element ADD CONSTRAINT FK_1DE42DDDEC2E3EA FOREIGN KEY (answer_for_id) REFERENCES exercise (id)');
        $this->addSql('ALTER TABLE hint DROP FOREIGN KEY FK_34C603531F1F2A24');
        $this->addSql('ALTER TABLE hint DROP FOREIGN KEY FK_34C60353E934951A');
        $this->addSql('DROP TABLE hint');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hint (id INT AUTO_INCREMENT NOT NULL, element_id INT NOT NULL, exercise_id INT NOT NULL, lore VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_34C603531F1F2A24 (element_id), INDEX IDX_34C60353E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE hint ADD CONSTRAINT FK_34C603531F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE hint ADD CONSTRAINT FK_34C60353E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE lored_element DROP FOREIGN KEY FK_1DE42DD1F1F2A24');
        $this->addSql('ALTER TABLE lored_element DROP FOREIGN KEY FK_1DE42DD58BDCDC7');
        $this->addSql('ALTER TABLE lored_element DROP FOREIGN KEY FK_1DE42DDDEC2E3EA');
        $this->addSql('DROP TABLE lored_element');
    }
}
