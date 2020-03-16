<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\ContentRevision;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200313102726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add status to content revision';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_revisions ADD status TINYTEXT NOT NULL');
        $this->addSql(sprintf(
            'UPDATE product_content_revisions SET status="%s"',
            ContentRevision::STATUS_NEW
        ));
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_revisions DROP status');
    }
}
