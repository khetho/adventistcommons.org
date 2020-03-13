<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200313161511 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_content_log');
        $this->addSql('DROP TABLE project_content_approval');
        $this->addSql('DROP TABLE project_content_status');
        $this->addSql('DROP TABLE project_members');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_content_log (id INT UNSIGNED AUTO_INCREMENT NOT NULL, sentence_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, project_id INT UNSIGNED DEFAULT NULL, resolved_by INT UNSIGNED DEFAULT NULL, comment TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, type VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, is_resolved TINYINT(1) NOT NULL, resolved_on DATETIME DEFAULT NULL, INDEX user_id (user_id), INDEX sentence_id (sentence_id), INDEX project_id (project_id), INDEX resolved_by (resolved_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE project_content_approval (id INT UNSIGNED AUTO_INCREMENT NOT NULL, content_id INT UNSIGNED DEFAULT NULL, project_id INT UNSIGNED DEFAULT NULL, approved_by INT UNSIGNED DEFAULT NULL, approved_on DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX approved_by (approved_by), INDEX content_id (content_id), INDEX project_id (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE project_content_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, content_id INT UNSIGNED DEFAULT NULL, project_id INT UNSIGNED DEFAULT NULL, is_approved TINYINT(1) NOT NULL, INDEX content_id (content_id), INDEX project_id (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE project_members (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, project_id INT UNSIGNED DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, invite_email VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, INDEX user_id (user_id), INDEX project_id (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_content_log ADD CONSTRAINT FK_96FBA02F166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE product_content_log ADD CONSTRAINT FK_96FBA02F27289490 FOREIGN KEY (sentence_id) REFERENCES sentence (id)');
        $this->addSql('ALTER TABLE product_content_log ADD CONSTRAINT product_content_log_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE product_content_log ADD CONSTRAINT product_content_log_ibfk_4 FOREIGN KEY (resolved_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE project_content_approval ADD CONSTRAINT FK_F9D2BCAD166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_content_approval ADD CONSTRAINT FK_F9D2BCAD84A0A3ED FOREIGN KEY (content_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE project_content_approval ADD CONSTRAINT project_content_approval_ibfk_5 FOREIGN KEY (approved_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE project_content_status ADD CONSTRAINT FK_FA812112166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_content_status ADD CONSTRAINT FK_FA81211284A0A3ED FOREIGN KEY (content_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }
}
