<?php

use Phinx\Migration\AbstractMigration;

class ProductType extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                ALTER TABLE `products`
                    CHANGE `type` `type` varchar(10) COLLATE 'utf8_general_ci' NULL,
                    ADD slug VARCHAR(255) NOT NULL,
                    CHANGE name name VARCHAR(255) NOT NULL;
                CREATE UNIQUE INDEX UNIQ_B3BA5A5A989D9B62 ON products (slug);
SQL
        );
        $this->query(
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

    public function down()
    {
        $this->query(
            <<<SQL
--                DROP INDEX UNIQ_B3BA5A5A989D9B62 ON products;
                ALTER TABLE `products`
                    DROP `slug`,
                    CHANGE name name VARCHAR(255) NULL;
SQL
        );
    }
}
