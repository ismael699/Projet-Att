<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240801182019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494C3A3BB');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B4C7A8CA');
        $this->addSql('DROP INDEX UNIQ_8D93D6494C3A3BB ON user');
        $this->addSql('ALTER TABLE user DROP payment_id');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B4C7A8CA FOREIGN KEY (user_infos_id) REFERENCES user_infos (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B4C7A8CA');
        $this->addSql('ALTER TABLE user ADD payment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B4C7A8CA FOREIGN KEY (user_infos_id) REFERENCES user_infos (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494C3A3BB ON user (payment_id)');
    }
}
