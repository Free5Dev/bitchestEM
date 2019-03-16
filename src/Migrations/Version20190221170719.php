<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190221170719 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cotation ADD cryptos_id INT NOT NULL');
        $this->addSql('ALTER TABLE cotation ADD CONSTRAINT FK_996DA9443323DADD FOREIGN KEY (cryptos_id) REFERENCES cryptos (id)');
        $this->addSql('CREATE INDEX IDX_996DA9443323DADD ON cotation (cryptos_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cotation DROP FOREIGN KEY FK_996DA9443323DADD');
        $this->addSql('DROP INDEX IDX_996DA9443323DADD ON cotation');
        $this->addSql('ALTER TABLE cotation DROP cryptos_id');
    }
}
