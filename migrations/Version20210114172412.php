<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210114172412 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql('ALTER TABLE "user" RENAME property_ad_search_settings TO property_search_settings');
        } else {
            $this->addSql('ALTER TABLE `user` CHANGE property_ad_search_settings property_search_settings JSON NOT NULL');
        }
    }

    public function down(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql('ALTER TABLE "user" RENAME property_search_settings TO property_ad_search_settings');
        } else {
            $this->addSql('ALTER TABLE `user` CHANGE property_search_settings property_ad_search_settings JSON NOT NULL');
        }
    }
}
