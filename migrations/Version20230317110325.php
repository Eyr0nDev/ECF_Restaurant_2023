<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230317110325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE opening_hours_restaurant (opening_hours_id INT NOT NULL, restaurant_id INT NOT NULL, INDEX IDX_DD09684ACE298D68 (opening_hours_id), INDEX IDX_DD09684AB1E7706E (restaurant_id), PRIMARY KEY(opening_hours_id, restaurant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE opening_hours_restaurant ADD CONSTRAINT FK_DD09684ACE298D68 FOREIGN KEY (opening_hours_id) REFERENCES opening_hours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE opening_hours_restaurant ADD CONSTRAINT FK_DD09684AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE opening_hours_restaurant DROP FOREIGN KEY FK_DD09684ACE298D68');
        $this->addSql('ALTER TABLE opening_hours_restaurant DROP FOREIGN KEY FK_DD09684AB1E7706E');
        $this->addSql('DROP TABLE opening_hours_restaurant');
    }
}
