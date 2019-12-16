<?php

use Phinx\Migration\AbstractMigration;

class UserNull extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                ALTER TABLE `users`
                CHANGE `ip_address` `ip_address` varchar(45) COLLATE 'utf8_general_ci' NULL AFTER `id`,
                CHANGE `password` `password` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `username`,
                CHANGE `product_notify` `product_notify` tinyint(1) NOT NULL AFTER `skills`,
                CHANGE `pro_translator` `pro_translator` tinyint(1) NOT NULL AFTER `product_notify`,
                DROP `forgotten_password_selector`;;
SQL
        );
    }

    public function down()
    {
        $this->query(
            <<<SQL
                ALTER TABLE `users`
                CHANGE `ip_address` `ip_address` varchar(45) NOT NULL,
				CHANGE `password` `password` varchar(255) NOT NULL,
				CHANGE `product_notify` `product_notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
				CHANGE `pro_translator` `pro_translator` tinyint(1) unsigned NOT NULL DEFAULT '0',
				ADD `forgotten_password_selector` varchar(255) DEFAULT NULL;
SQL
        );
    }
}
