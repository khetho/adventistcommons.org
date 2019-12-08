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
                CHANGE `product_notify` `product_notify` tinyint(1) NULL AFTER `skills`,
                CHANGE `pro_translator` `pro_translator` tinyint(1) NULL AFTER `product_notify`;
SQL
        );
    }

    public function down()
    {
        throw new \Exception('Do NOT make a down on doctrine stuff migration !');
    }
}
