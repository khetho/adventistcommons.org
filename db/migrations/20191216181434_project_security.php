<?php

use Phinx\Migration\AbstractMigration;

class ProjectSecurity extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                 CREATE UNIQUE INDEX UNIQ_5C93B3A44584665A82F1BAF4 ON projects (product_id, language_id);
                 CREATE TABLE project_user (project_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_B4021E51166D1F9C (project_id), INDEX IDX_B4021E51A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
                 ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
                 ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
SQL
        );
    }

    public function down()
    {
        $this->query(
            <<<SQL
                DROP INDEX UNIQ_5C93B3A44584665A82F1BAF4 ON projects;
                DROP TABLE project_user;
SQL
        );
    }
}
