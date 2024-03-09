<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240309220727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE odiseo_blog_article (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, archived_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EA96598A77153098 (code), INDEX IDX_EA96598AF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_articles_channels (article_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_A4C0CF5F7294869C (article_id), INDEX IDX_A4C0CF5F72F5A1AA (channel_id), PRIMARY KEY(article_id, channel_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_articles_categories (article_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_F090056C7294869C (article_id), INDEX IDX_F090056C12469DE2 (category_id), PRIMARY KEY(article_id, category_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_category (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_AA59457F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_2F9EF9492C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_article_category_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_comment (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, author_id INT DEFAULT NULL, comment LONGTEXT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3DD1AC477294869C (article_id), INDEX IDX_3DD1AC47727ACA70 (parent_id), INDEX IDX_3DD1AC47F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_image (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_F97399C97E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_article_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_7A606A2C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_article_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE odiseo_blog_article ADD CONSTRAINT FK_EA96598AF675F31B FOREIGN KEY (author_id) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE odiseo_blog_articles_channels ADD CONSTRAINT FK_A4C0CF5F7294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id)');
        $this->addSql('ALTER TABLE odiseo_blog_articles_channels ADD CONSTRAINT FK_A4C0CF5F72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE odiseo_blog_articles_categories ADD CONSTRAINT FK_F090056C7294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id)');
        $this->addSql('ALTER TABLE odiseo_blog_articles_categories ADD CONSTRAINT FK_F090056C12469DE2 FOREIGN KEY (category_id) REFERENCES odiseo_blog_article_category (id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_category_translation ADD CONSTRAINT FK_2F9EF9492C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_article_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_comment ADD CONSTRAINT FK_3DD1AC477294869C FOREIGN KEY (article_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_article_comment ADD CONSTRAINT FK_3DD1AC47727ACA70 FOREIGN KEY (parent_id) REFERENCES odiseo_blog_article_comment (id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_comment ADD CONSTRAINT FK_3DD1AC47F675F31B FOREIGN KEY (author_id) REFERENCES sylius_shop_user (id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_image ADD CONSTRAINT FK_F97399C97E3C61F9 FOREIGN KEY (owner_id) REFERENCES odiseo_blog_article (id)');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation ADD CONSTRAINT FK_7A606A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_article DROP FOREIGN KEY FK_EA96598AF675F31B');
        $this->addSql('ALTER TABLE odiseo_blog_articles_channels DROP FOREIGN KEY FK_A4C0CF5F7294869C');
        $this->addSql('ALTER TABLE odiseo_blog_articles_channels DROP FOREIGN KEY FK_A4C0CF5F72F5A1AA');
        $this->addSql('ALTER TABLE odiseo_blog_articles_categories DROP FOREIGN KEY FK_F090056C7294869C');
        $this->addSql('ALTER TABLE odiseo_blog_articles_categories DROP FOREIGN KEY FK_F090056C12469DE2');
        $this->addSql('ALTER TABLE odiseo_blog_article_category_translation DROP FOREIGN KEY FK_2F9EF9492C2AC5D3');
        $this->addSql('ALTER TABLE odiseo_blog_article_comment DROP FOREIGN KEY FK_3DD1AC477294869C');
        $this->addSql('ALTER TABLE odiseo_blog_article_comment DROP FOREIGN KEY FK_3DD1AC47727ACA70');
        $this->addSql('ALTER TABLE odiseo_blog_article_comment DROP FOREIGN KEY FK_3DD1AC47F675F31B');
        $this->addSql('ALTER TABLE odiseo_blog_article_image DROP FOREIGN KEY FK_F97399C97E3C61F9');
        $this->addSql('ALTER TABLE odiseo_blog_article_translation DROP FOREIGN KEY FK_7A606A2C2AC5D3');
        $this->addSql('DROP TABLE odiseo_blog_article');
        $this->addSql('DROP TABLE odiseo_blog_articles_channels');
        $this->addSql('DROP TABLE odiseo_blog_articles_categories');
        $this->addSql('DROP TABLE odiseo_blog_article_category');
        $this->addSql('DROP TABLE odiseo_blog_article_category_translation');
        $this->addSql('DROP TABLE odiseo_blog_article_comment');
        $this->addSql('DROP TABLE odiseo_blog_article_image');
        $this->addSql('DROP TABLE odiseo_blog_article_translation');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }
}
