<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/** MySQL/MariaDB. Version20261127120001 is the PostgreSQL twin. */
final class Version20261127120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the files uploaded from the content editor.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE odiseo_blog_content_media (type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_ODISEO_BLOG_CONTENT_MEDIA_CREATED_AT (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE odiseo_blog_content_media');
    }
}
