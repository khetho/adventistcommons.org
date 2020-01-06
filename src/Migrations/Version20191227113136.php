<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20191227113136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'UserSocial';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                ALTER TABLE users
                    ADD facebook_id INT DEFAULT NULL,
                    ADD google_id INT DEFAULT NULL;
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                ALTER TABLE users
                    DROP facebook_id,
                    DROP google_id;
SQL
        );
    }
}
