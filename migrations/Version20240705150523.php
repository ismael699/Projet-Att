<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705150523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, lieu_depart_id INT DEFAULT NULL, lieu_arrivee_id INT DEFAULT NULL, chauffeur_id INT DEFAULT NULL, service_id INT DEFAULT NULL, date DATETIME NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_F65593E5C16565FC (lieu_depart_id), INDEX IDX_F65593E5BF9A3FF6 (lieu_arrivee_id), INDEX IDX_F65593E585C0B3BE (chauffeur_id), INDEX IDX_F65593E5ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city_annonce (city_id INT NOT NULL, annonce_id INT NOT NULL, INDEX IDX_4B1D2DB78BAC62AF (city_id), INDEX IDX_4B1D2DB78805AB2F (annonce_id), PRIMARY KEY(city_id, annonce_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, payment_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, UNIQUE INDEX UNIQ_6D28840DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, payment_id INT DEFAULT NULL, user_infos_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, siren VARCHAR(9) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D6494C3A3BB (payment_id), UNIQUE INDEX UNIQ_8D93D649B4C7A8CA (user_infos_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_infos (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(20) NOT NULL, photo_name VARCHAR(255) DEFAULT NULL, driving_license_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5C16565FC FOREIGN KEY (lieu_depart_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5BF9A3FF6 FOREIGN KEY (lieu_arrivee_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E585C0B3BE FOREIGN KEY (chauffeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE city_annonce ADD CONSTRAINT FK_4B1D2DB78BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE city_annonce ADD CONSTRAINT FK_4B1D2DB78805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B4C7A8CA FOREIGN KEY (user_infos_id) REFERENCES user_infos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5C16565FC');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5BF9A3FF6');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E585C0B3BE');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5ED5CA9E6');
        $this->addSql('ALTER TABLE city_annonce DROP FOREIGN KEY FK_4B1D2DB78BAC62AF');
        $this->addSql('ALTER TABLE city_annonce DROP FOREIGN KEY FK_4B1D2DB78805AB2F');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494C3A3BB');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B4C7A8CA');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE city_annonce');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_infos');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
