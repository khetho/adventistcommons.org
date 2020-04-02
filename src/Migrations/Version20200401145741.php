<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200401145741 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add reviewer to project and rename approver to proofreader';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_languages_proofread (user_id INT UNSIGNED NOT NULL, language_id INT UNSIGNED NOT NULL, INDEX IDX_782264E1A76ED395 (user_id), INDEX IDX_782264E182F1BAF4 (language_id), PRIMARY KEY(user_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_languages_proofread ADD CONSTRAINT FK_782264E1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_languages_proofread ADD CONSTRAINT FK_782264E182F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id)');
        $this->addSql('DROP TABLE user_languages_approved');
        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D5BB23766C');
        $this->addSql('DROP INDEX IDX_DD358D5BB23766C ON product_content_revisions');
        $this->addSql('ALTER TABLE product_content_revisions CHANGE approver_id proofreader_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D555712564 FOREIGN KEY (proofreader_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DD358D555712564 ON product_content_revisions (proofreader_id)');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4BB23766C');
        $this->addSql('DROP INDEX IDX_5C93B3A4BB23766C ON projects');
        $this->addSql('ALTER TABLE projects ADD reviewer_id INT UNSIGNED DEFAULT NULL, CHANGE approver_id proofreader_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A455712564 FOREIGN KEY (proofreader_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A470574616 FOREIGN KEY (reviewer_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5C93B3A455712564 ON projects (proofreader_id)');
        $this->addSql('CREATE INDEX IDX_5C93B3A470574616 ON projects (reviewer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_languages_approved (user_id INT UNSIGNED NOT NULL, language_id INT UNSIGNED NOT NULL, INDEX IDX_6DD2E332A76ED395 (user_id), INDEX IDX_6DD2E33282F1BAF4 (language_id), PRIMARY KEY(user_id, language_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_languages_approved ADD CONSTRAINT FK_6DD2E33282F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id)');
        $this->addSql('ALTER TABLE user_languages_approved ADD CONSTRAINT FK_6DD2E332A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE user_languages_proofread');
        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D555712564');
        $this->addSql('DROP INDEX IDX_DD358D555712564 ON product_content_revisions');
        $this->addSql('ALTER TABLE product_content_revisions CHANGE proofreader_id approver_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D5BB23766C FOREIGN KEY (approver_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DD358D5BB23766C ON product_content_revisions (approver_id)');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A455712564');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A470574616');
        $this->addSql('DROP INDEX IDX_5C93B3A455712564 ON projects');
        $this->addSql('DROP INDEX IDX_5C93B3A470574616 ON projects');
        $this->addSql('ALTER TABLE projects ADD approver_id INT UNSIGNED DEFAULT NULL, DROP proofreader_id, DROP reviewer_id');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4BB23766C FOREIGN KEY (approver_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5C93B3A4BB23766C ON projects (approver_id)');
    }
}
