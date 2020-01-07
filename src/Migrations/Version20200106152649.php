<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200106152649 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add download_log table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE download_log (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, product_id INT UNSIGNED DEFAULT NULL, project_id INT UNSIGNED DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, path VARCHAR(255) NOT NULL, INDEX IDX_A5291D98A76ED395 (user_id), INDEX IDX_A5291D984584665A (product_id), INDEX IDX_A5291D98166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D98A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D984584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D98166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE download_log ADD attachment_id INT UNSIGNED DEFAULT NULL;');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D98464E68B FOREIGN KEY (attachment_id) REFERENCES product_attachments (id);');
        $this->addSql('CREATE INDEX IDX_A5291D98464E68B ON download_log (attachment_id);');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE download_log');
    }
}
