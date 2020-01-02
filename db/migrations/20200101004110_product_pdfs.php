<?php

use Phinx\Migration\AbstractMigration;

class ProductPdfs extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                ALTER TABLE products ADD pdf_offset_file VARCHAR(255) DEFAULT NULL, ADD pdf_digital_file VARCHAR(255) DEFAULT NULL;
SQL
        );
    }

    public function down()
    {
        $this->query(
            <<<SQL
                ALTER TABLE products DROP pdf_offset_file, DROP pdf_digital_file;
SQL
        );
    }
}
