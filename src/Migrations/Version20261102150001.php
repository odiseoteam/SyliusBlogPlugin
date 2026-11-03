<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/** PostgreSQL. Version20261102150000 is the MySQL/MariaDB twin. */
final class Version20261102150001 extends AbstractPostgreSQLMigration
{
    public function getDescription(): string
    {
        return 'Adds the blog configuration of each channel.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE odiseo_blog_channel_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE odiseo_blog_channel_configuration_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE odiseo_blog_channel_configuration (enabled BOOLEAN NOT NULL, posts_per_page INT NOT NULL, id INT NOT NULL, channel_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B5D73DD772F5A1AA ON odiseo_blog_channel_configuration (channel_id)');
        $this->addSql('CREATE TABLE odiseo_blog_channel_configuration_translation (title VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, id INT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_9E303C0B2C2AC5D3 ON odiseo_blog_channel_configuration_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX odiseo_blog_channel_configuration_translation_uniq_trans ON odiseo_blog_channel_configuration_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration ADD CONSTRAINT FK_B5D73DD772F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration_translation ADD CONSTRAINT FK_9E303C0B2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_channel_configuration (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration DROP CONSTRAINT FK_B5D73DD772F5A1AA');
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration_translation DROP CONSTRAINT FK_9E303C0B2C2AC5D3');
        $this->addSql('DROP TABLE odiseo_blog_channel_configuration_translation');
        $this->addSql('DROP TABLE odiseo_blog_channel_configuration');
        $this->addSql('DROP SEQUENCE odiseo_blog_channel_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE odiseo_blog_channel_configuration_translation_id_seq CASCADE');
    }
}
