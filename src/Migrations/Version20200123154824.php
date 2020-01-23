<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200123154824 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add sentence and paragraph tables instead of «content»';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE product_content TO paragraph');
        $this->addSql('ALTER TABLE paragraph DROP content, DROP is_hidden, DROP xliff_tag');

        $this->addSql('CREATE TABLE sentence (id INT UNSIGNED AUTO_INCREMENT NOT NULL, paragraph_id INT UNSIGNED DEFAULT NULL, content TEXT DEFAULT NULL, `order` INT DEFAULT NULL, INDEX paragraph_id (paragraph_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sentence ADD CONSTRAINT FK_9D664ED58B50597F FOREIGN KEY (paragraph_id) REFERENCES paragraph (id)');

        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D584A0A3ED');
        $this->addSql('DROP INDEX content_id ON product_content_revisions');
        $this->addSql('ALTER TABLE product_content_revisions CHANGE content_id sentence_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D527289490 FOREIGN KEY (sentence_id) REFERENCES sentence (id)');
        $this->addSql('CREATE INDEX sentence_id ON product_content_revisions (sentence_id)');

        $this->addSql('ALTER TABLE product_content_log DROP FOREIGN KEY FK_96FBA02F84A0A3ED');
        $this->addSql('DROP INDEX content_id ON product_content_log');
        $this->addSql('ALTER TABLE product_content_log CHANGE content_id sentence_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_log ADD CONSTRAINT FK_96FBA02F27289490 FOREIGN KEY (sentence_id) REFERENCES sentence (id)');
        $this->addSql('CREATE INDEX sentence_id ON product_content_log (sentence_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_content_log DROP FOREIGN KEY FK_96FBA02F27289490');
        $this->addSql('DROP INDEX sentence_id ON product_content_log');
        $this->addSql('ALTER TABLE product_content_log CHANGE sentence_id content_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_log ADD CONSTRAINT FK_96FBA02F84A0A3ED FOREIGN KEY (content_id) REFERENCES paragraph (id)');
        $this->addSql('CREATE INDEX content_id ON product_content_log (content_id)');

        $this->addSql('ALTER TABLE product_content_revisions DROP FOREIGN KEY FK_DD358D527289490');
        $this->addSql('DROP INDEX sentence_id ON product_content_revisions');
        $this->addSql('ALTER TABLE product_content_revisions CHANGE sentence_id content_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE product_content_revisions ADD CONSTRAINT FK_DD358D584A0A3ED FOREIGN KEY (content_id) REFERENCES paragraph (id)');
        $this->addSql('CREATE INDEX content_id ON product_content_revisions (content_id)');

        $this->addSql('DROP TABLE sentence');

        $this->addSql('ALTER TABLE paragraph ADD content TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`, ADD is_hidden TINYINT(1) NOT NULL, ADD xliff_tag VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_general_ci`');
        $this->addSql('RENAME TABLE paragraph TO product_content');
    }
}
