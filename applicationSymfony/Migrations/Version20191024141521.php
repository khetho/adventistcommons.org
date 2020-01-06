<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20191024141521 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'IdmlKeys';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
			ALTER TABLE `product_sections` ADD `story_key` varchar(255) DEFAULT NULL;
SQL
        );
        $this->addSql(
            <<<SQL
			ALTER TABLE `product_content` ADD `content_key` varchar(255) DEFAULT NULL, ADD `order` integer DEFAULT NULL;
SQL
        );
    }
    
    public function down(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
			ALTER TABLE `product_content` DROP `content_key`, DROP `order`;
SQL
        );
        $this->addSql(
            <<<SQL
			ALTER TABLE `product_sections` DROP `story_key`;
SQL
        );
    }
}
