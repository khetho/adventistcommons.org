<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20191208173941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'UserNull';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                ALTER TABLE `users`
                CHANGE `ip_address` `ip_address` varchar(45) COLLATE 'utf8_general_ci' NULL AFTER `id`,
                CHANGE `password` `password` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `username`,
                CHANGE `product_notify` `product_notify` tinyint(1) NOT NULL AFTER `skills`,
                CHANGE `pro_translator` `pro_translator` tinyint(1) NOT NULL AFTER `product_notify`,
                CHANGE forgotten_password_time forgotten_password_time INT DEFAULT NULL,
                DROP `forgotten_password_selector`;
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
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
