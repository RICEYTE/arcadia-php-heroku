<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701125842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE rapport_veterinaire_rapport_veterinaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE rapport_veterinaire (rapport_veterinaire_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, detail VARCHAR(50) NOT NULL, PRIMARY KEY(rapport_veterinaire_id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE rapport_veterinaire_rapport_veterinaire_id_seq CASCADE');
        $this->addSql('DROP TABLE rapport_veterinaire');
    }
}
