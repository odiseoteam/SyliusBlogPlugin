<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/** MySQL/MariaDB. Version20261102130001 is the PostgreSQL twin. */
final class Version20261102130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the article, category, tag and author tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE odiseo_blog_article (code VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, published_at DATETIME DEFAULT NULL, featured TINYINT(1) NOT NULL, comments_enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, main_category_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_EA96598A77153098 (code), INDEX IDX_EA96598AC6C55574 (main_category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_channels (article_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_5F01BE087294869C (article_id), INDEX IDX_5F01BE0872F5A1AA (channel_id), PRIMARY KEY (article_id, channel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_categories (article_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_226874947294869C (article_id), INDEX IDX_2268749412469DE2 (category_id), PRIMARY KEY (article_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_tags (article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_22C5D3027294869C (article_id), INDEX IDX_22C5D302BAD26311 (tag_id), PRIMARY KEY (article_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_author (position INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_A4FFA0247294869C (article_id), INDEX IDX_A4FFA024F675F31B (author_id), UNIQUE INDEX odiseo_blog_article_author_uidx (article_id, author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_category (code VARCHAR(255) NOT NULL, tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, position INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_AA59457F77153098 (code), INDEX IDX_AA59457FA977936C (tree_root), INDEX IDX_AA59457F727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_category_translation (name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, INDEX IDX_2F9EF9492C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_article_category_translation_slug_uidx (locale, slug), UNIQUE INDEX odiseo_blog_article_category_translation_uniq_trans (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_tag (code VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_C8AFCBE377153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_tag_translation (name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, INDEX IDX_C1B7DB12C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_article_tag_translation_slug_uidx (locale, slug), UNIQUE INDEX odiseo_blog_article_tag_translation_uniq_trans (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_translation (title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, excerpt LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, INDEX IDX_7A606A2C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_article_translation_slug_uidx (locale, slug), UNIQUE INDEX odiseo_blog_article_translation_uniq_trans (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_author (code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, social_links JSON NOT NULL, enabled TINYINT(1) NOT NULL, id INT AUTO_INCREMENT NOT NULL, admin_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A92C078F77153098 (code), UNIQUE INDEX UNIQ_A92C078F989D9B62 (slug), UNIQUE INDEX UNIQ_A92C078F6352511C (admin_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_author_translation (job_title VARCHAR(255) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, INDEX IDX_CCDAC56A2C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_author_translation_uniq_trans (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE odiseo_blog_article ADD CONSTRAINT FK_EA96598AC6C55574 FOREIGN KEY (main_category_id) REFERENCES odiseo_blog_article_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE odiseo_blog_article_channels ADD CONSTRAINT FK_5F01BE087294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_channels ADD CONSTRAINT FK_5F01BE0872F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_categories ADD CONSTRAINT FK_226874947294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_categories ADD CONSTRAINT FK_2268749412469DE2 FOREIGN KEY (category_id) REFERENCES odiseo_blog_article_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_tags ADD CONSTRAINT FK_22C5D3027294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_tags ADD CONSTRAINT FK_22C5D302BAD26311 FOREIGN KEY (tag_id) REFERENCES odiseo_blog_article_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_author ADD CONSTRAINT FK_A4FFA0247294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_author ADD CONSTRAINT FK_A4FFA024F675F31B FOREIGN KEY (author_id) REFERENCES odiseo_blog_author (id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_category ADD CONSTRAINT FK_AA59457FA977936C FOREIGN KEY (tree_root) REFERENCES odiseo_blog_article_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_category ADD CONSTRAINT FK_AA59457F727ACA70 FOREIGN KEY (parent_id) REFERENCES odiseo_blog_article_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_category_translation ADD CONSTRAINT FK_2F9EF9492C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_article_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_tag_translation ADD CONSTRAINT FK_C1B7DB12C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_article_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation ADD CONSTRAINT FK_7A606A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_author ADD CONSTRAINT FK_A92C078F6352511C FOREIGN KEY (admin_user_id) REFERENCES sylius_admin_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE odiseo_blog_author_translation ADD CONSTRAINT FK_CCDAC56A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_author (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_article DROP FOREIGN KEY FK_EA96598AC6C55574');
        $this->addSql('ALTER TABLE odiseo_blog_article_channels DROP FOREIGN KEY FK_5F01BE087294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_channels DROP FOREIGN KEY FK_5F01BE0872F5A1AA');
        $this->addSql('ALTER TABLE odiseo_blog_article_categories DROP FOREIGN KEY FK_226874947294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_categories DROP FOREIGN KEY FK_2268749412469DE2');
        $this->addSql('ALTER TABLE odiseo_blog_article_tags DROP FOREIGN KEY FK_22C5D3027294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_tags DROP FOREIGN KEY FK_22C5D302BAD26311');
        $this->addSql('ALTER TABLE odiseo_blog_article_author DROP FOREIGN KEY FK_A4FFA0247294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_author DROP FOREIGN KEY FK_A4FFA024F675F31B');
        $this->addSql('ALTER TABLE odiseo_blog_article_category DROP FOREIGN KEY FK_AA59457FA977936C');
        $this->addSql('ALTER TABLE odiseo_blog_article_category DROP FOREIGN KEY FK_AA59457F727ACA70');
        $this->addSql('ALTER TABLE odiseo_blog_article_category_translation DROP FOREIGN KEY FK_2F9EF9492C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_article_tag_translation DROP FOREIGN KEY FK_C1B7DB12C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation DROP FOREIGN KEY FK_7A606A2C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_author DROP FOREIGN KEY FK_A92C078F6352511C');
        $this->addSql('ALTER TABLE odiseo_blog_author_translation DROP FOREIGN KEY FK_CCDAC56A2C2AC5D3');
        $this->addSql('DROP TABLE odiseo_blog_article');
        $this->addSql('DROP TABLE odiseo_blog_article_channels');
        $this->addSql('DROP TABLE odiseo_blog_article_categories');
        $this->addSql('DROP TABLE odiseo_blog_article_tags');
        $this->addSql('DROP TABLE odiseo_blog_article_author');
        $this->addSql('DROP TABLE odiseo_blog_article_category');
        $this->addSql('DROP TABLE odiseo_blog_article_category_translation');
        $this->addSql('DROP TABLE odiseo_blog_article_tag');
        $this->addSql('DROP TABLE odiseo_blog_article_tag_translation');
        $this->addSql('DROP TABLE odiseo_blog_article_translation');
        $this->addSql('DROP TABLE odiseo_blog_author');
        $this->addSql('DROP TABLE odiseo_blog_author_translation');
    }
}
