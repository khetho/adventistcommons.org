<?php

use Phinx\Migration\AbstractMigration;

class SectionProduct extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                 ALTER TABLE product_content DROP FOREIGN KEY FK_2F1C2B194584665A;
                 DROP INDEX product_id ON product_content;
                 ALTER TABLE product_content DROP product_id;
SQL
        );
    }

    public function down()
    {
        $this->query(
            <<<SQL
                 ALTER TABLE product_content DROP FOREIGN KEY FK_2F1C2B194584665A;
                 DROP INDEX product_id ON product_content;
                 ALTER TABLE product_content DROP product_id;
SQL
        );
    }
}
