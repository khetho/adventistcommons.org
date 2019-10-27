<?php

use Phinx\Migration\AbstractMigration;

class IdmlKeys extends AbstractMigration
{
	public function up()
	{
		$this->query(<<<SQL
			ALTER TABLE `product_sections` ADD `story_key` varchar(255) DEFAULT NULL;
SQL
		);
		$this->query(<<<SQL
			ALTER TABLE `product_content` ADD `content_key` varchar(255) DEFAULT NULL, ADD `order` integer DEFAULT NULL;
SQL
		);
	}
	
	public function down()
	{
		$this->query(<<<SQL
			ALTER TABLE `product_content` DROP `content_key`, DROP `order`;
SQL
		);
		$this->query(<<<SQL
			ALTER TABLE `product_sections` DROP `story_key`;
SQL
		);
	}
}
