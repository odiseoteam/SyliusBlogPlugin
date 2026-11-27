<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/** PostgreSQL. Version20261127120000 is the MySQL/MariaDB twin. */
final class Version20261127120001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Adds the files uploaded from the content editor.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE odiseo_blog_content_media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE odiseo_blog_content_media (type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_ODISEO_BLOG_CONTENT_MEDIA_CREATED_AT ON odiseo_blog_content_media (created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE odiseo_blog_content_media_id_seq CASCADE');
        $this->addSql('DROP TABLE odiseo_blog_content_media');
    }
}
