<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240721224221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE animal_animal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE avis_avis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE habitat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE image_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE race_race_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rapport_veterinaire_rapport_veterinaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_service_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE animal (animal_id INT NOT NULL, habitat_id INT DEFAULT NULL, race_id INT NOT NULL, prenom VARCHAR(50) NOT NULL, etat VARCHAR(50) NOT NULL, PRIMARY KEY(animal_id))');
        $this->addSql('CREATE INDEX IDX_6AAB231FAFFE2D26 ON animal (habitat_id)');
        $this->addSql('CREATE INDEX IDX_6AAB231F6E59D40D ON animal (race_id)');
        $this->addSql('CREATE TABLE avis (avis_id INT NOT NULL, pseudo VARCHAR(50) NOT NULL, commentaire VARCHAR(50) NOT NULL, is_visible BOOLEAN NOT NULL, PRIMARY KEY(avis_id))');
        $this->addSql('CREATE TABLE habitat (id INT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(50) NOT NULL, commentaire_habitat VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE image (image_id INT NOT NULL, image_data BYTEA NOT NULL, PRIMARY KEY(image_id))');
        $this->addSql('CREATE TABLE race (race_id INT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(race_id))');
        $this->addSql('CREATE TABLE rapport_veterinaire (rapport_veterinaire_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, detail VARCHAR(50) NOT NULL, PRIMARY KEY(rapport_veterinaire_id))');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE service (service_id INT NOT NULL, nom VARCHAR(50) NOT NULL, description VARCHAR(50) NOT NULL, PRIMARY KEY(service_id))');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON utilisateur (username)');
        $this->addSql('COMMENT ON COLUMN utilisateur.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F6E59D40D FOREIGN KEY (race_id) REFERENCES race (race_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE animal_animal_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE avis_avis_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE habitat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE image_image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE race_race_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rapport_veterinaire_rapport_veterinaire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('ALTER TABLE animal DROP CONSTRAINT FK_6AAB231FAFFE2D26');
        $this->addSql('ALTER TABLE animal DROP CONSTRAINT FK_6AAB231F6E59D40D');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE habitat');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE rapport_veterinaire');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE utilisateur');
    }
}
