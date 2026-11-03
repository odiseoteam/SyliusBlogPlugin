<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractPostgreSQLMigration;

/** PostgreSQL. Version20261102120000 is the MySQL/MariaDB twin. */
final class Version20261102120001 extends AbstractPostgreSQLMigration
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

        // Same as the MySQL twin, for consistency: the import only reads.
        foreach ($tables as $table) {
            foreach ($schema->getTable($table)->getForeignKeys() as $foreignKey) {
                $this->addSql(sprintf('ALTER TABLE %s DROP CONSTRAINT %s', $table, $foreignKey->getName()));
            }
        }

        // Index and sequence names are unique per schema in PostgreSQL, and the new tables reuse them.
        foreach ($tables as $table) {
            $indexes = $this->connection->fetchFirstColumn(
                'SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ?',
                [$table],
            );
            foreach ($indexes as $index) {
                \assert(\is_string($index));
                $this->addSql(sprintf('ALTER INDEX %s RENAME TO %s', $index, self::legacyName($index)));
            }

            if ($schema->hasSequence($table . '_id_seq')) {
                $this->addSql(sprintf('ALTER SEQUENCE %s_id_seq RENAME TO %s_id_seq', $table, self::legacyName($table)));
            }

            $this->addSql(sprintf('ALTER TABLE %s RENAME TO %s', $table, self::legacyName($table)));
        }
    }

    public function down(Schema $schema): void
    {
        // Read from the database: the schema filter hides the legacy tables from $schema. Indexes and sequences keep
        // their legacy names, and dropped foreign keys stay dropped.
        $legacyTables = $this->connection->fetchFirstColumn(
            'SELECT tablename FROM pg_tables WHERE schemaname = current_schema()',
        );

        foreach (self::TABLES as $table) {
            if (\in_array(self::legacyName($table), $legacyTables, true)) {
                $this->addSql(sprintf('ALTER TABLE %s RENAME TO %s', self::legacyName($table), $table));
            }
        }
    }

    /** odiseo_blog_x → odiseo_blog_legacy_x; any other name gets the prefix. */
    private static function legacyName(string $name): string
    {
        $name = str_starts_with($name, 'odiseo_blog_') ? substr($name, \strlen('odiseo_blog_')) : $name;

        return 'odiseo_blog_legacy_' . $name;
    }
}
