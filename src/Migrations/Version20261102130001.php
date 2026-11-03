<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/** PostgreSQL. Version20261102130000 is the MySQL/MariaDB twin. */
final class Version20261102130001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Creates the article, category, tag and author tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_category_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_tag_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_article_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_author_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE odiseo_blog_article (code VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, featured BOOLEAN NOT NULL, comments_enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id INT NOT NULL, main_category_id INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EA96598A77153098 ON odiseo_blog_article (code)');
        $this->addSql('CREATE INDEX IDX_EA96598AC6C55574 ON odiseo_blog_article (main_category_id)');
        $this->addSql('CREATE TABLE odiseo_blog_article_channels (article_id INT NOT NULL, channel_id INT NOT NULL, PRIMARY KEY (article_id, channel_id))');
        $this->addSql('CREATE INDEX IDX_5F01BE087294869C ON odiseo_blog_article_channels (article_id)');
        $this->addSql('CREATE INDEX IDX_5F01BE0872F5A1AA ON odiseo_blog_article_channels (channel_id)');
        $this->addSql('CREATE TABLE odiseo_blog_article_categories (article_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY (article_id, category_id))');
        $this->addSql('CREATE INDEX IDX_226874947294869C ON odiseo_blog_article_categories (article_id)');
        $this->addSql('CREATE INDEX IDX_2268749412469DE2 ON odiseo_blog_article_categories (category_id)');
        $this->addSql('CREATE TABLE odiseo_blog_article_tags (article_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY (article_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_22C5D3027294869C ON odiseo_blog_article_tags (article_id)');
        $this->addSql('CREATE INDEX IDX_22C5D302BAD26311 ON odiseo_blog_article_tags (tag_id)');
        $this->addSql('CREATE TABLE odiseo_blog_article_author (position INT NOT NULL, id INT NOT NULL, article_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A4FFA0247294869C ON odiseo_blog_article_author (article_id)');
        $this->addSql('CREATE INDEX IDX_A4FFA024F675F31B ON odiseo_blog_article_author (author_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_author_uidx ON odiseo_blog_article_author (article_id, author_id)');
        $this->addSql('CREATE TABLE odiseo_blog_article_category (code VARCHAR(255) NOT NULL, tree_left INT NOT NULL, tree_right INT NOT NULL, tree_level INT NOT NULL, position INT NOT NULL, id INT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA59457F77153098 ON odiseo_blog_article_category (code)');
        $this->addSql('CREATE INDEX IDX_AA59457FA977936C ON odiseo_blog_article_category (tree_root)');
        $this->addSql('CREATE INDEX IDX_AA59457F727ACA70 ON odiseo_blog_article_category (parent_id)');
        $this->addSql('CREATE TABLE odiseo_blog_article_category_translation (name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_2F9EF9492C2AC5D3 ON odiseo_blog_article_category_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_category_translation_slug_uidx ON odiseo_blog_article_category_translation (locale, slug)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_category_translation_uniq_trans ON odiseo_blog_article_category_translation (translatable_id, locale)');
        $this->addSql('CREATE TABLE odiseo_blog_article_tag (code VARCHAR(255) NOT NULL, id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8AFCBE377153098 ON odiseo_blog_article_tag (code)');
        $this->addSql('CREATE TABLE odiseo_blog_article_tag_translation (name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C1B7DB12C2AC5D3 ON odiseo_blog_article_tag_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_tag_translation_slug_uidx ON odiseo_blog_article_tag_translation (locale, slug)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_tag_translation_uniq_trans ON odiseo_blog_article_tag_translation (translatable_id, locale)');
        $this->addSql('CREATE TABLE odiseo_blog_article_translation (title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, excerpt TEXT DEFAULT NULL, content TEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_7A606A2C2AC5D3 ON odiseo_blog_article_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_translation_slug_uidx ON odiseo_blog_article_translation (locale, slug)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_article_translation_uniq_trans ON odiseo_blog_article_translation (translatable_id, locale)');
        $this->addSql('CREATE TABLE odiseo_blog_author (code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, social_links JSON NOT NULL, enabled BOOLEAN NOT NULL, id INT NOT NULL, admin_user_id INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A92C078F77153098 ON odiseo_blog_author (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A92C078F989D9B62 ON odiseo_blog_author (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A92C078F6352511C ON odiseo_blog_author (admin_user_id)');
        $this->addSql('CREATE TABLE odiseo_blog_author_translation (job_title VARCHAR(255) DEFAULT NULL, bio TEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, id INT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_CCDAC56A2C2AC5D3 ON odiseo_blog_author_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_author_translation_uniq_trans ON odiseo_blog_author_translation (translatable_id, locale)');
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
        $this->addSql('DROP SEQUENCE odiseo_blog_article_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_article_author_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_article_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_article_category_translation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_article_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_article_tag_translation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_article_translation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_author_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_author_translation_id_seq CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article DROP CONSTRAINT FK_EA96598AC6C55574');
        $this->addSql('ALTER TABLE odiseo_blog_article_channels DROP CONSTRAINT FK_5F01BE087294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_channels DROP CONSTRAINT FK_5F01BE0872F5A1AA');
        $this->addSql('ALTER TABLE odiseo_blog_article_categories DROP CONSTRAINT FK_226874947294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_categories DROP CONSTRAINT FK_2268749412469DE2');
        $this->addSql('ALTER TABLE odiseo_blog_article_tags DROP CONSTRAINT FK_22C5D3027294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_tags DROP CONSTRAINT FK_22C5D302BAD26311');
        $this->addSql('ALTER TABLE odiseo_blog_article_author DROP CONSTRAINT FK_A4FFA0247294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_author DROP CONSTRAINT FK_A4FFA024F675F31B');
        $this->addSql('ALTER TABLE odiseo_blog_article_category DROP CONSTRAINT FK_AA59457FA977936C');
        $this->addSql('ALTER TABLE odiseo_blog_article_category DROP CONSTRAINT FK_AA59457F727ACA70');
        $this->addSql('ALTER TABLE odiseo_blog_article_category_translation DROP CONSTRAINT FK_2F9EF9492C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_article_tag_translation DROP CONSTRAINT FK_C1B7DB12C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation DROP CONSTRAINT FK_7A606A2C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_author DROP CONSTRAINT FK_A92C078F6352511C');
        $this->addSql('ALTER TABLE odiseo_blog_author_translation DROP CONSTRAINT FK_CCDAC56A2C2AC5D3');
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
