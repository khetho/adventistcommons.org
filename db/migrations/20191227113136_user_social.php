<?php

use Phinx\Migration\AbstractMigration;

class UserSocial extends AbstractMigration
{
    public function up()
    {
        $this->query(
            <<<SQL
                ALTER TABLE users
                    ADD facebook_id INT DEFAULT NULL,
                    ADD google_id INT DEFAULT NULL;
SQL
        );
    }

    public function down()
    {
        $this->query(
            <<<SQL
                ALTER TABLE users
                    DROP facebook_id,
                    DROP google_id;
SQL
        );
    }
}
