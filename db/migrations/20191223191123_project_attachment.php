<?php

use Phinx\Migration\AbstractMigration;

class ProjectAttachment extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                ALTER TABLE product_attachments DROP FOREIGN KEY FK_EBEB64C94584665A;
                ALTER TABLE product_attachments DROP FOREIGN KEY product_attachments_ibfk_1;
                DROP INDEX language_id ON product_attachments;
                DROP INDEX product_id ON product_attachments;
                ALTER TABLE product_attachments
                    ADD project_id INT UNSIGNED DEFAULT NULL;
                UPDATE product_attachments AS a 
                    INNER JOIN products AS d ON d.id = a.product_id 
                    INNER JOIN projects AS j ON j.product_id = d.id AND j.language_id = a.language_id 
                    SET a.project_id = j.id;
                ALTER TABLE product_attachments
                    DROP language_id,
                    DROP product_id;
                ALTER TABLE product_attachments
                    ADD CONSTRAINT FK_EBEB64C9166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
                CREATE INDEX project_id ON product_attachments (project_id);
SQL
        );
    }

    public function down()
    {
        $this->query(
            <<<SQL
SQL
        );
    }
}
