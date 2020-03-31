<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200330175525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add the «enabled» property of product';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE projects ADD enabled TINYINT(1) NOT NULL');
        $this->addSql('UPDATE projects SET enabled = 1 WHERE 1');
        $this->addSql('ALTER TABLE products ADD enabled TINYINT(1) NOT NULL');
        $this->addSql('UPDATE products SET enabled = 0 WHERE 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE projects DROP enabled');
        $this->addSql('ALTER TABLE product DROP enabled');
    }
}
