<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230117194144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livraire (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, mat_moto INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boisson ADD etat VARCHAR(255) NOT NULL, CHANGE prix prix INT NOT NULL');
        $this->addSql('ALTER TABLE burger ADD etat VARCHAR(255) NOT NULL, CHANGE prix prix INT NOT NULL');
        $this->addSql('ALTER TABLE commande CHANGE users_id users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D67B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D67B3B43D ON commande (users_id)');
        $this->addSql('ALTER TABLE frite ADD etat VARCHAR(255) NOT NULL, CHANGE prix prix INT NOT NULL');
        $this->addSql('ALTER TABLE gestionnaire ADD nom_complet VARCHAR(255) NOT NULL, ADD tel VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu ADD etat VARCHAR(255) NOT NULL, CHANGE prix prix INT NOT NULL');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EAB014612');
        $this->addSql('DROP INDEX IDX_B1DC7A1EAB014612 ON paiement');
        $this->addSql('ALTER TABLE paiement DROP clients_id');
        $this->addSql('ALTER TABLE produit ADD etat VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE tel tel VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE livraire');
        $this->addSql('ALTER TABLE boisson DROP etat, CHANGE prix prix VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE burger DROP etat, CHANGE prix prix VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D67B3B43D');
        $this->addSql('DROP INDEX IDX_6EEAA67D67B3B43D ON commande');
        $this->addSql('ALTER TABLE commande CHANGE users_id users_id INT NOT NULL');
        $this->addSql('ALTER TABLE frite DROP etat, CHANGE prix prix VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gestionnaire DROP nom_complet, DROP tel');
        $this->addSql('ALTER TABLE menu DROP etat, CHANGE prix prix VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD clients_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EAB014612 FOREIGN KEY (clients_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1EAB014612 ON paiement (clients_id)');
        $this->addSql('ALTER TABLE produit DROP etat');
        $this->addSql('ALTER TABLE user CHANGE tel tel VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
