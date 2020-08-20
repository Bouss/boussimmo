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
        $this->addSql('CREATE TABLE "user" DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        if ($this->connection->getDatabasePlatform()->getName() === 'postgresql') {
            $this->addSql('ALTER TABLE "user" ADD id SERIAL');
        } else {
            $this->addSql('ALTER TABLE "user" ADD id INT AUTO_INCREMENT NOT NULL');
        }

        $this->addSql('ALTER TABLE "user" ADD email VARCHAR(180) NOT NULL, ADD google_id VARCHAR(255) NOT NULL, ADD access_token VARCHAR(255) NOT NULL, ADD profile_image VARCHAR(255) DEFAULT NULL, ADD roles JSON NOT NULL, ADD UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), ADD UNIQUE INDEX UNIQ_8D93D64976F5C865 (google_id), ADD PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "user"');
    }
}
