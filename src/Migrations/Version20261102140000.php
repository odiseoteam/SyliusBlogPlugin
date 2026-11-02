<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/** MySQL/MariaDB. Version20261102140001 is the PostgreSQL twin. */
final class Version20261102140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the article, category and author images, and the image alt text.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE odiseo_blog_article_image (type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_category_image (type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_author_image (type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE odiseo_blog_article ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE odiseo_blog_article ADD CONSTRAINT FK_EA96598A3DA5256D FOREIGN KEY (image_id) REFERENCES odiseo_blog_article_image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA96598A3DA5256D ON odiseo_blog_article (image_id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_category ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE odiseo_blog_article_category ADD CONSTRAINT FK_AA59457F3DA5256D FOREIGN KEY (image_id) REFERENCES odiseo_blog_article_category_image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA59457F3DA5256D ON odiseo_blog_article_category (image_id)');
        $this->addSql('ALTER TABLE odiseo_blog_author ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE odiseo_blog_author ADD CONSTRAINT FK_A92C078F3DA5256D FOREIGN KEY (image_id) REFERENCES odiseo_blog_author_image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A92C078F3DA5256D ON odiseo_blog_author (image_id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation ADD image_alt VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_article DROP FOREIGN KEY FK_EA96598A3DA5256D');
        $this->addSql('DROP INDEX UNIQ_EA96598A3DA5256D ON odiseo_blog_article');
        $this->addSql('ALTER TABLE odiseo_blog_article DROP image_id');
        $this->addSql('ALTER TABLE odiseo_blog_article_category DROP FOREIGN KEY FK_AA59457F3DA5256D');
        $this->addSql('DROP INDEX UNIQ_AA59457F3DA5256D ON odiseo_blog_article_category');
        $this->addSql('ALTER TABLE odiseo_blog_article_category DROP image_id');
        $this->addSql('ALTER TABLE odiseo_blog_author DROP FOREIGN KEY FK_A92C078F3DA5256D');
        $this->addSql('DROP INDEX UNIQ_A92C078F3DA5256D ON odiseo_blog_author');
        $this->addSql('ALTER TABLE odiseo_blog_author DROP image_id');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation DROP image_alt');
        $this->addSql('DROP TABLE odiseo_blog_article_image');
        $this->addSql('DROP TABLE odiseo_blog_article_category_image');
        $this->addSql('DROP TABLE odiseo_blog_author_image');
    }
}
