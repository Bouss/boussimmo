<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200326123759 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $user = $this->connection->getDatabasePlatform()->getName() === 'postgresql' ? '"user"' : '`user`';

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE $user ADD property_ad_search_settings JSON NOT NULL");
    }

    public function down(Schema $schema) : void
    {
        $user = $this->connection->getDatabasePlatform()->getName() === 'postgresql' ? '"user"' : '`user`';

        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE $user DROP property_ad_search_settings");
    }

    /**
     * Workaround (@see https://github.com/doctrine/migrations/issues/1104)
     *
     * @return bool
     */
    public function isTransactional(): bool
    {
        return false;
    }
}
