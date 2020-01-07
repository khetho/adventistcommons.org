<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class Version20191118102000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'DoctrineStuff';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<SQL
            ALTER TABLE project_content_approval
                DROP FOREIGN KEY project_content_approval_ibfk_6;
            ALTER TABLE project_content_approval
                DROP FOREIGN KEY project_content_approval_ibfk_7;
            ALTER TABLE project_content_approval
                CHANGE content_id content_id INT UNSIGNED DEFAULT NULL,
                CHANGE project_id project_id INT UNSIGNED DEFAULT NULL,
                CHANGE approved_by approved_by INT UNSIGNED DEFAULT NULL;
            ALTER TABLE project_content_approval
                ADD CONSTRAINT FK_F9D2BCAD84A0A3ED FOREIGN KEY (content_id) REFERENCES product_content (id);
            ALTER TABLE project_content_approval
                ADD CONSTRAINT FK_F9D2BCAD166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_sections
                DROP FOREIGN KEY product_sections_ibfk_1;
            ALTER TABLE product_sections
                CHANGE product_id product_id INT UNSIGNED DEFAULT NULL,
                CHANGE `order` `order` INT NOT NULL,
                CHANGE position position INT NOT NULL;
            ALTER TABLE product_sections
                ADD CONSTRAINT FK_E026290F4584665A FOREIGN KEY (product_id) REFERENCES products (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE projects
                DROP FOREIGN KEY projects_ibfk_1;
            ALTER TABLE projects
                CHANGE product_id product_id INT UNSIGNED DEFAULT NULL,
                CHANGE language_id language_id INT UNSIGNED DEFAULT NULL,
                CHANGE status status TINYINT(1) NOT NULL;
            ALTER TABLE projects
                ADD CONSTRAINT FK_5C93B3A44584665A FOREIGN KEY (product_id) REFERENCES products (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_content
                DROP FOREIGN KEY product_content_ibfk_1;
            ALTER TABLE product_content
                DROP FOREIGN KEY product_content_ibfk_2;
            ALTER TABLE product_content
                CHANGE product_id product_id INT UNSIGNED DEFAULT NULL,
                CHANGE section_id section_id INT UNSIGNED DEFAULT NULL,
                CHANGE is_hidden is_hidden TINYINT(1) NOT NULL;
            ALTER TABLE product_content
                ADD CONSTRAINT FK_2F1C2B194584665A FOREIGN KEY (product_id) REFERENCES products (id);
            ALTER TABLE product_content
                ADD CONSTRAINT FK_2F1C2B19D823E37A FOREIGN KEY (section_id) REFERENCES product_sections (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE project_members
                DROP FOREIGN KEY project_members_ibfk_1;
            ALTER TABLE project_members
                DROP FOREIGN KEY project_members_ibfk_2;
            ALTER TABLE project_members
                CHANGE project_id project_id INT UNSIGNED DEFAULT NULL;
            ALTER TABLE project_members
                ADD CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
            ALTER TABLE project_members
                ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
            ALTER TABLE users
                CHANGE skills skills LONGTEXT DEFAULT NULL COMMENT '(DC2Type:phpserialize)',
                CHANGE active active TINYINT(1) DEFAULT NULL,
                CHANGE product_notify product_notify TINYINT(1) NOT NULL,
                CHANGE pro_translator pro_translator TINYINT(1) NOT NULL;
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE user_skills MODIFY id INT UNSIGNED NOT NULL;
            ALTER TABLE user_skills DROP PRIMARY KEY;
            ALTER TABLE user_skills DROP FOREIGN KEY user_skills_ibfk_1;
            ALTER TABLE user_skills DROP FOREIGN KEY user_skills_ibfk_2;
            ALTER TABLE user_skills DROP id;
            ALTER TABLE user_skills ADD PRIMARY KEY (user_id, skill_id);
            DROP INDEX user_skills_ibfk_2 ON user_skills;
            CREATE INDEX IDX_B0630D4DA76ED395 ON user_skills (user_id);
            CREATE INDEX IDX_B0630D4D5585C142 ON user_skills (skill_id);
            ALTER TABLE user_skills
                ADD CONSTRAINT FK_B0630D4DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
            ALTER TABLE user_skills
                ADD CONSTRAINT FK_B0630D4D5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE user_languages MODIFY id INT UNSIGNED NOT NULL;         
            DROP INDEX uc_users_languages ON user_languages;
            ALTER TABLE user_languages
                DROP PRIMARY KEY;
            ALTER TABLE user_languages
                DROP FOREIGN KEY user_languages_ibfk_1;
            ALTER TABLE user_languages
                DROP FOREIGN KEY user_languages_ibfk_2;
            ALTER TABLE user_languages
                DROP id;
            ALTER TABLE user_languages ADD PRIMARY KEY (user_id, language_id);
            DROP INDEX user_id ON user_languages;
            DROP INDEX language_id ON user_languages;
            CREATE INDEX IDX_A031DE9DA76ED395 ON user_languages (user_id);
            CREATE INDEX IDX_A031DE9D82F1BAF4 ON user_languages (language_id);
            ALTER TABLE user_languages
                ADD CONSTRAINT FK_A031DE9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
            ALTER TABLE user_languages
                ADD CONSTRAINT FK_A031DE9D82F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE users_groups MODIFY id INT UNSIGNED NOT NULL;
            ALTER TABLE users_groups
                DROP FOREIGN KEY fk_users_groups_groups1;
            ALTER TABLE users_groups
                DROP FOREIGN KEY fk_users_groups_users1;
            DROP INDEX uc_users_groups ON users_groups;
            DROP INDEX fk_users_groups_users1_idx ON users_groups;
            DROP INDEX fk_users_groups_groups1_idx ON users_groups;
            ALTER TABLE users_groups DROP FOREIGN KEY fk_users_groups_groups1;
            ALTER TABLE users_groups DROP FOREIGN KEY fk_users_groups_users1;
            CREATE INDEX IDX_FF8AB7E0FE54D947 ON users_groups (group_id);
            ALTER TABLE users_groups
                ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id);
            ALTER TABLE users_groups
                ADD CONSTRAINT fk_users_groups_groups1 FOREIGN KEY (group_id) REFERENCES groups (id)
                ON UPDATE NO ACTION ON DELETE CASCADE;
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_content_log
                DROP FOREIGN KEY product_content_log_ibfk_1;
            ALTER TABLE product_content_log
                DROP FOREIGN KEY product_content_log_ibfk_3;
            ALTER TABLE product_content_log
                ADD CONSTRAINT FK_96FBA02F84A0A3ED FOREIGN KEY (content_id) REFERENCES product_content (id);
            ALTER TABLE product_content_log
                ADD CONSTRAINT FK_96FBA02F166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE products
                DROP FOREIGN KEY products_ibfk_2;
            DROP INDEX binding ON products;
            ALTER TABLE products ADD binding_id INT DEFAULT NULL, DROP binding;
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_audiences
                DROP FOREIGN KEY product_audiences_ibfk_1;
            ALTER TABLE product_audiences
                DROP FOREIGN KEY product_audiences_ibfk_2;
            ALTER TABLE audiences
                CHANGE id id INT AUTO_INCREMENT NOT NULL;
            ALTER TABLE product_audiences MODIFY id INT UNSIGNED NOT NULL;
            ALTER TABLE product_audiences
                DROP id,
                DROP created_at,
                CHANGE audience_id audience_id INT NOT NULL;
            ALTER TABLE product_audiences
                ADD CONSTRAINT FK_663F2C444584665A FOREIGN KEY (product_id) REFERENCES products (id);
            ALTER TABLE product_audiences ADD PRIMARY KEY (product_id, audience_id);
            DROP INDEX product_id ON product_audiences;
            CREATE INDEX IDX_663F2C444584665A ON product_audiences (product_id);
            DROP INDEX audience_id ON product_audiences;
            CREATE INDEX IDX_663F2C44848CC616 ON product_audiences (audience_id);
            ALTER TABLE product_audiences
                ADD CONSTRAINT FK_663F2C44848CC616 FOREIGN KEY (audience_id) REFERENCES audiences (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_bindings
                CHANGE id id INT AUTO_INCREMENT NOT NULL;
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_content_revisions
                DROP FOREIGN KEY product_content_revisions_ibfk_1;
            ALTER TABLE product_content_revisions
                DROP FOREIGN KEY product_content_revisions_ibfk_3;
            ALTER TABLE product_content_revisions
                CHANGE content_id content_id INT UNSIGNED DEFAULT NULL,
                CHANGE user_id user_id INT UNSIGNED DEFAULT NULL,
                CHANGE project_id project_id INT UNSIGNED DEFAULT NULL;
            ALTER TABLE product_content_revisions
                ADD CONSTRAINT FK_DD358D584A0A3ED FOREIGN KEY (content_id) REFERENCES product_content (id);
            ALTER TABLE product_content_revisions
                ADD CONSTRAINT FK_DD358D5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE product_attachments
                DROP FOREIGN KEY product_attachments_ibfk_2;
            ALTER TABLE product_attachments
                ADD CONSTRAINT FK_EBEB64C94584665A FOREIGN KEY (product_id) REFERENCES products (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE project_content_status
                DROP FOREIGN KEY project_content_status_ibfk_1;
            ALTER TABLE project_content_status
                DROP FOREIGN KEY project_content_status_ibfk_2;
            ALTER TABLE project_content_status
                CHANGE content_id content_id INT UNSIGNED DEFAULT NULL,
                CHANGE project_id project_id INT UNSIGNED DEFAULT NULL,
                CHANGE is_approved is_approved TINYINT(1) NOT NULL;
            ALTER TABLE project_content_status
                ADD CONSTRAINT FK_FA81211284A0A3ED FOREIGN KEY (content_id) REFERENCES product_content (id);
            ALTER TABLE project_content_status
                ADD CONSTRAINT FK_FA812112166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE series
                CHANGE name name VARCHAR(255) NOT NULL;
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE languages
                CHANGE rtl rtl TINYINT(1) NOT NULL;
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE users_groups DROP PRIMARY KEY;
            ALTER TABLE users_groups DROP id;
            ALTER TABLE users_groups
                ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
            ALTER TABLE users_groups
                ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id);
            CREATE INDEX IDX_FF8AB7E0A76ED395 ON users_groups (user_id);
            CREATE INDEX IDX_FF8AB7E0FE54D947 ON users_groups (group_id);
            ALTER TABLE users_groups ADD PRIMARY KEY (user_id, group_id);
SQL
        );
        $this->addSql(
            <<<SQL
            ALTER TABLE products
                ADD CONSTRAINT FK_B3BA5A5A4AC8159D FOREIGN KEY (binding_id) REFERENCES product_bindings (id);
            CREATE INDEX binding_id ON products (binding_id);
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        throw new \Exception('Do NOT make a down on doctrine stuff migration !');
    }
}
