<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701124020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE race_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE race_race_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE race DROP CONSTRAINT race_pkey');
        $this->addSql('ALTER TABLE race ADD label VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE race RENAME COLUMN id TO race_id');
        $this->addSql('ALTER TABLE race ADD PRIMARY KEY (race_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE race_race_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE race_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX race_pkey');
        $this->addSql('ALTER TABLE race DROP label');
        $this->addSql('ALTER TABLE race RENAME COLUMN race_id TO id');
        $this->addSql('ALTER TABLE race ADD PRIMARY KEY (id)');
    }
}
