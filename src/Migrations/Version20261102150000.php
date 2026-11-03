<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/** MySQL/MariaDB. Version20261102150001 is the PostgreSQL twin. */
final class Version20261102150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the blog configuration of each channel.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE odiseo_blog_channel_configuration (enabled TINYINT(1) NOT NULL, posts_per_page INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, UNIQUE INDEX UNIQ_B5D73DD772F5A1AA (channel_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE odiseo_blog_channel_configuration_translation (title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(255) NOT NULL, translatable_id INT NOT NULL, INDEX IDX_9E303C0B2C2AC5D3 (translatable_id), UNIQUE INDEX odiseo_blog_channel_configuration_translation_uniq_trans (translatable_id, locale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration ADD CONSTRAINT FK_B5D73DD772F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration_translation ADD CONSTRAINT FK_9E303C0B2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES odiseo_blog_channel_configuration (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration DROP FOREIGN KEY FK_B5D73DD772F5A1AA');
        $this->addSql('ALTER TABLE odiseo_blog_channel_configuration_translation DROP FOREIGN KEY FK_9E303C0B2C2AC5D3');
        $this->addSql('DROP TABLE odiseo_blog_channel_configuration_translation');
        $this->addSql('DROP TABLE odiseo_blog_channel_configuration');
    }
}
