<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190222101304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction ADD users_id INT NOT NULL, ADD cryptos_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D167B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13323DADD FOREIGN KEY (cryptos_id) REFERENCES cryptos (id)');
        $this->addSql('CREATE INDEX IDX_723705D167B3B43D ON transaction (users_id)');
        $this->addSql('CREATE INDEX IDX_723705D13323DADD ON transaction (cryptos_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D167B3B43D');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D13323DADD');
        $this->addSql('DROP INDEX IDX_723705D167B3B43D ON transaction');
        $this->addSql('DROP INDEX IDX_723705D13323DADD ON transaction');
        $this->addSql('ALTER TABLE transaction DROP users_id, DROP cryptos_id');
    }
}
