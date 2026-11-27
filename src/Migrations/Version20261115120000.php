<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/** MySQL/MariaDB. Version20261115120001 is the PostgreSQL twin. */
final class Version20261115120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the plain text and the word count of the content of an article.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_article_translation ADD plain_text LONGTEXT DEFAULT NULL, ADD word_count INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_article_translation DROP plain_text, DROP word_count');
    }
}
