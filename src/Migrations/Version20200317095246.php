<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200317095246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add cascade delete from product deletion, part 2';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D527289490');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D527289490 FOREIGN KEY (sentence_id) REFERENCES sentence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_attachments DROP FOREIGN KEY FK_EBEB64C9166D1F9C');
        $this->addSql('ALTER TABLE product_attachments ADD CONSTRAINT FK_EBEB64C9166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE download_log DROP FOREIGN KEY FK_A5291D98166D1F9C');
        $this->addSql('ALTER TABLE download_log DROP FOREIGN KEY FK_A5291D984584665A');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D98166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D984584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE download_log DROP FOREIGN KEY FK_A5291D984584665A');
        $this->addSql('ALTER TABLE download_log DROP FOREIGN KEY FK_A5291D98166D1F9C');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D984584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE download_log ADD CONSTRAINT FK_A5291D98166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE product_attachments DROP FOREIGN KEY FK_EBEB64C9166D1F9C');
        $this->addSql('ALTER TABLE product_attachments ADD CONSTRAINT FK_EBEB64C9166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D527289490');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D527289490 FOREIGN KEY (sentence_id) REFERENCES sentence (id)');
    }
}
