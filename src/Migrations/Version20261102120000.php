<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/** MySQL/MariaDB. Version20261102120001 is the PostgreSQL twin. */
final class Version20261102120000 extends AbstractMigration
{
    private const TABLES = [
        'odiseo_blog_article',
        'odiseo_blog_article_category',
        'odiseo_blog_article_category_translation',
        'odiseo_blog_article_comment',
        'odiseo_blog_article_image',
        'odiseo_blog_article_translation',
        'odiseo_blog_articles_categories',
        'odiseo_blog_articles_channels',
    ];

    public function getDescription(): string
    {
        return 'Renames the 1.x blog tables to odiseo_blog_legacy_*, so the new tables can take their names.';
    }

    public function up(Schema $schema): void
    {
        // A 1.x article has "enabled" where the new one has "state".
        $this->skipIf(
            !$schema->hasTable('odiseo_blog_article') || !$schema->getTable('odiseo_blog_article')->hasColumn('enabled'),
            'There are no 1.x blog tables.',
        );

        $tables = array_values(array_filter(self::TABLES, $schema->hasTable(...)));

        // MySQL foreign key names are unique per database, and the new tables reuse them. The import only reads.
        foreach ($tables as $table) {
            foreach ($schema->getTable($table)->getForeignKeys() as $foreignKey) {
                $this->addSql(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $foreignKey->getName()));
            }
        }

        foreach ($tables as $table) {
            $this->addSql(sprintf('RENAME TABLE %s TO %s', $table, self::legacyName($table)));
        }
    }

    public function down(Schema $schema): void
    {
        // Read from the database: the schema filter hides the legacy tables from $schema. Dropped foreign keys stay
        // dropped.
        $legacyTables = $this->connection->fetchFirstColumn(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()',
        );

        foreach (self::TABLES as $table) {
            if (\in_array(self::legacyName($table), $legacyTables, true)) {
                $this->addSql(sprintf('RENAME TABLE %s TO %s', self::legacyName($table), $table));
            }
        }
    }

    private static function legacyName(string $table): string
    {
        return 'odiseo_blog_legacy_' . substr($table, \strlen('odiseo_blog_'));
    }
}
