<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200326121726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'add link between translation and users';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY product_content_revisions_ibfk_2');
        $this->addSql('DROP INDEX user_id ON product_content_revisions');
        $this->addSql('ALTER TABLE product_content_revisions ADD approver_id INT UNSIGNED DEFAULT NULL, ADD reviewer_id INT UNSIGNED DEFAULT NULL, CHANGE user_id translator_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D55370E40B FOREIGN KEY (translator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D5BB23766C FOREIGN KEY (approver_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D570574616 FOREIGN KEY (reviewer_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DD358D5BB23766C ON product_content_revisions (approver_id)');
        $this->addSql('CREATE INDEX IDX_DD358D570574616 ON product_content_revisions (reviewer_id)');
        $this->addSql('CREATE INDEX translator_id ON product_content_revisions (translator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D55370E40B');
        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D5BB23766C');
        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D570574616');
        $this->addSql('DROP INDEX IDX_DD358D5BB23766C ON product_content_revisions');
        $this->addSql('DROP INDEX IDX_DD358D570574616 ON product_content_revisions');
        $this->addSql('DROP INDEX translator_id ON product_content_revisions');
        $this->addSql('ALTER TABLE product_content_revisions ADD user_id INT UNSIGNED DEFAULT NULL, DROP translator_id, DROP approver_id, DROP reviewer_id');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT product_content_revisions_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX user_id ON product_content_revisions (user_id)');
    }
}
