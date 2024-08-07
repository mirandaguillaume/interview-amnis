<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807122956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exchanges AS SELECT id, business_partner_id, exchange_rate, from_amount, to_amount, from_currency, to_currency, date, executed FROM exchanges');
        $this->addSql('DROP TABLE exchanges');
        $this->addSql('CREATE TABLE exchanges (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, business_partner_id INTEGER DEFAULT NULL, payout_transaction_id INTEGER DEFAULT NULL, payin_transaction_id INTEGER DEFAULT NULL, exchange_rate NUMERIC(10, 2) DEFAULT NULL, from_amount NUMERIC(10, 2) NOT NULL, to_amount NUMERIC(10, 2) DEFAULT NULL, from_currency VARCHAR(255) NOT NULL, to_currency VARCHAR(255) NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , executed BOOLEAN NOT NULL, CONSTRAINT FK_32043D235330F055 FOREIGN KEY (business_partner_id) REFERENCES business_partners (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_32043D232BB46CC0 FOREIGN KEY (payout_transaction_id) REFERENCES transactions (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_32043D2362E86BEC FOREIGN KEY (payin_transaction_id) REFERENCES transactions (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exchanges (id, business_partner_id, exchange_rate, from_amount, to_amount, from_currency, to_currency, date, executed) SELECT id, business_partner_id, exchange_rate, from_amount, to_amount, from_currency, to_currency, date, executed FROM __temp__exchanges');
        $this->addSql('DROP TABLE __temp__exchanges');
        $this->addSql('CREATE INDEX IDX_32043D235330F055 ON exchanges (business_partner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32043D232BB46CC0 ON exchanges (payout_transaction_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32043D2362E86BEC ON exchanges (payin_transaction_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__exchanges AS SELECT id, business_partner_id, exchange_rate, from_amount, to_amount, from_currency, to_currency, date, executed FROM exchanges');
        $this->addSql('DROP TABLE exchanges');
        $this->addSql('CREATE TABLE exchanges (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, business_partner_id INTEGER DEFAULT NULL, exchange_rate NUMERIC(10, 2) DEFAULT NULL, from_amount NUMERIC(10, 2) NOT NULL, to_amount NUMERIC(10, 2) DEFAULT NULL, from_currency VARCHAR(255) NOT NULL, to_currency VARCHAR(255) NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , executed BOOLEAN NOT NULL, CONSTRAINT FK_32043D235330F055 FOREIGN KEY (business_partner_id) REFERENCES business_partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exchanges (id, business_partner_id, exchange_rate, from_amount, to_amount, from_currency, to_currency, date, executed) SELECT id, business_partner_id, exchange_rate, from_amount, to_amount, from_currency, to_currency, date, executed FROM __temp__exchanges');
        $this->addSql('DROP TABLE __temp__exchanges');
        $this->addSql('CREATE INDEX IDX_32043D235330F055 ON exchanges (business_partner_id)');
    }
}
