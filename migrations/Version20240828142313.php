<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828142313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494C3A3BB');
        $this->addSql('DROP INDEX UNIQ_8D93D6494C3A3BB ON user');
        $this->addSql('ALTER TABLE user DROP payment_id, DROP file_name, DROP file_size');
        $this->addSql('ALTER TABLE user_infos ADD file_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_infos DROP file_name');
        $this->addSql('ALTER TABLE user ADD payment_id INT DEFAULT NULL, ADD file_name VARCHAR(255) DEFAULT NULL, ADD file_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494C3A3BB ON user (payment_id)');
    }
}
