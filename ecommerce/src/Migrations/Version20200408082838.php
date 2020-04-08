<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200408082838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite_product (favorite_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8E1EAAC3AA17481D (favorite_id), INDEX IDX_8E1EAAC34584665A (product_id), PRIMARY KEY(favorite_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC3AA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC34584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD product_id INT DEFAULT NULL, ADD favorite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494584665A FOREIGN KEY (product_id) REFERENCES favorite (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494584665A ON user (product_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AA17481D ON user (favorite_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494584665A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AA17481D');
        $this->addSql('ALTER TABLE favorite_product DROP FOREIGN KEY FK_8E1EAAC3AA17481D');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE favorite_product');
        $this->addSql('DROP INDEX IDX_8D93D6494584665A ON user');
        $this->addSql('DROP INDEX IDX_8D93D649AA17481D ON user');
        $this->addSql('ALTER TABLE user DROP product_id, DROP favorite_id');
    }
}
