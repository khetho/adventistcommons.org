<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20191212085012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'ProductType';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                ALTER TABLE `products`
                    CHANGE `type` `type` varchar(10) COLLATE 'utf8_general_ci' NULL,
                    ADD slug VARCHAR(255) NOT NULL,
                    CHANGE name name VARCHAR(255) NOT NULL;
                CREATE UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 ON products (slug);
SQL
        );
        $this->addSql(
            <<<SQL
                UPDATE `products`
                SET `slug` = LOWER(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                    REPLACE(
                        TRIM(`name`)
                    , ':', '')
                    , '(', '')
                    , ')', '')
                    , ',', '')
                    , '/', '')
                    , '\\\', '')
                    , '\\?', '')
                    , '''', '')
                    , '\\"', '')
                    , '\\&', '')
                    , '\\!', '')
                    , '\\.', '')
                    , '#', '')
                    , ' ', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
	            	, '--', '-')
            	);
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                DROP INDEX UNIQ_B3BA5A5A989D9B62 ON products;
                ALTER TABLE `products`
                    DROP `slug`,
                    CHANGE name name VARCHAR(255) NULL;
SQL
        );
    }
}
