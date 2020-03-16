<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200316181405 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D5166D1F9C');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sentence DROP FOREIGN KEY FK_9D664ED58B50597F');
        $this->addSql('ALTER TABLE sentence ADD CONSTRAINT FK_9D664ED58B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_sections DROP FOREIGN KEY FK_E026290F4584665A');
        $this->addSql('ALTER TABLE product_sections ADD CONSTRAINT FK_E026290F4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paragraph DROP FOREIGN KEY FK_2F1C2B19D823E37A');
        $this->addSql('ALTER TABLE paragraph ADD CONSTRAINT FK_7DD39862D823E37A FOREIGN KEY (section_id) REFERENCES product_sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A44584665A');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A44584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE paragraph DROP FOREIGN KEY FK_7DD39862D823E37A');
        $this->addSql('ALTER TABLE paragraph ADD CONSTRAINT FK_2F1C2B19D823E37A FOREIGN KEY (section_id) REFERENCES product_sections (id)');
        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D5166D1F9C');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE product_sections DROP FOREIGN KEY FK_E026290F4584665A');
        $this->addSql('ALTER TABLE product_sections ADD CONSTRAINT FK_E026290F4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A44584665A');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A44584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE sentence DROP FOREIGN KEY FK_9D664ED58B50597F');
        $this->addSql('ALTER TABLE sentence ADD CONSTRAINT FK_9D664ED58B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');
    }
}
