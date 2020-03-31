<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200331090009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE historical (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historical_product (historical_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C522B111C75EAE06 (historical_id), INDEX IDX_C522B1114584665A (product_id), PRIMARY KEY(historical_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historical_product ADD CONSTRAINT FK_C522B111C75EAE06 FOREIGN KEY (historical_id) REFERENCES historical (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE historical_product ADD CONSTRAINT FK_C522B1114584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE historical_product DROP FOREIGN KEY FK_C522B111C75EAE06');
        $this->addSql('DROP TABLE historical');
        $this->addSql('DROP TABLE historical_product');
    }
}
