<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
final class Version20200105213634 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'SectionProduct';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                 ALTER TABLE product_content DROP FOREIGN KEY FK_2F1C2B194584665A;
                 DROP INDEX product_id ON product_content;
                 ALTER TABLE product_content DROP product_id;
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
                 ALTER TABLE product_content DROP FOREIGN KEY FK_2F1C2B194584665A;
                 DROP INDEX product_id ON product_content;
                 ALTER TABLE product_content DROP product_id;
SQL
        );
    }
}
