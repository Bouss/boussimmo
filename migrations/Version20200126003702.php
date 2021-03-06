<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200126003702 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ($this->connection->getDatabasePlatform()->getName() === 'postgresql') {
            $this->addSql('CREATE TABLE "user" (id SERIAL, email varchar(180) NOT NULL, google_id varchar(255) NOT NULL, access_token varchar(255) NOT NULL, profile_image varchar(255) DEFAULT NULL, roles json NOT NULL, CONSTRAINT UNIQ_8D93D649E7927C74 UNIQUE(email), CONSTRAINT UNIQ_8D93D64976F5C865 UNIQUE(google_id), PRIMARY KEY(id))');
        } else {
            $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, google_id VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, profile_image VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64976F5C865 (google_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema) : void
    {
        $user = $this->connection->getDatabasePlatform()->getName() === 'postgresql' ? '"user"' : '`user`';

        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP TABLE $user");
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
