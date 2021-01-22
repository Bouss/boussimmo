<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210120141848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        if ($this->connection->getDatabasePlatform()->getName() === 'postgresql') {
            $this->addSql('ALTER TABLE "user" ADD access_token_created_at TIMESTAMP DEFAULT \'1970-01-01 00:00:00\', ADD revoked_at TIMESTAMP DEFAULT NULL, ADD created_at TIMESTAMP DEFAULT \'1970-01-01 00:00:00\', DROP revoked');
        } else {
            $this->addSql('ALTER TABLE `user` ADD access_token_created_at DATETIME DEFAULT \'1970-01-01 00:00:00\', ADD revoked_at DATETIME DEFAULT NULL, ADD created_at DATETIME DEFAULT \'1970-01-01 00:00:00\', DROP revoked');
        }

        // this up() migration is auto-generated, please modify it to your needs
    }

    public function down(Schema $schema) : void
    {
        $user = $this->connection->getDatabasePlatform()->getName() === 'postgresql' ? '"user"' : '`user`';

        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE $user DROP access_token_created_at, DROP revoked_at, DROP created_at, ADD revoked TINYINT(1) DEFAULT '0' NOT NULL");
    }
}
