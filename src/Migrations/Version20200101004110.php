<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200101004110 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'ProductPdfs';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                ALTER TABLE products ADD pdf_offset_file VARCHAR(255) DEFAULT NULL, ADD pdf_digital_file VARCHAR(255) DEFAULT NULL;
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                ALTER TABLE products DROP pdf_offset_file, DROP pdf_digital_file;
SQL
        );
    }
}
