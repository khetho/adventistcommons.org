<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200326163834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'add link between project and approver';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE projects ADD approver_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4BB23766C FOREIGN KEY (approver_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5C93B3A4BB23766C ON projects (approver_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4BB23766C');
        $this->addSql('DROP INDEX IDX_5C93B3A4BB23766C ON projects');
        $this->addSql('ALTER TABLE projects DROP approver_id');
    }
}
