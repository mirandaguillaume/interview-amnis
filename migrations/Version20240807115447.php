<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807115447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, business_partner_id INTEGER NOT NULL, currency VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_CAC89EAC5330F055 FOREIGN KEY (business_partner_id) REFERENCES business_partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CAC89EAC5330F055 ON accounts (business_partner_id)');
        $this->addSql('CREATE UNIQUE INDEX business_currencies ON accounts (business_partner_id, currency)');
        $this->addSql('CREATE TABLE business_partners (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, legal_form VARCHAR(255) NOT NULL, address VARCHAR(70) NOT NULL, city VARCHAR(35) NOT NULL, zip VARCHAR(16) NOT NULL, country VARCHAR(2) NOT NULL)');
        $this->addSql('CREATE TABLE exchanges (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, business_partner_id INTEGER DEFAULT NULL, exchange_rate NUMERIC(10, 2) DEFAULT NULL, from_amount NUMERIC(10, 2) NOT NULL, to_amount NUMERIC(10, 2) DEFAULT NULL, from_currency VARCHAR(255) NOT NULL, to_currency VARCHAR(255) NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , executed BOOLEAN NOT NULL, CONSTRAINT FK_32043D235330F055 FOREIGN KEY (business_partner_id) REFERENCES business_partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_32043D235330F055 ON exchanges (business_partner_id)');
        $this->addSql('CREATE TABLE transactions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, account_id INTEGER NOT NULL, amount NUMERIC(10, 2) NOT NULL, name VARCHAR(255) NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , executed BOOLEAN NOT NULL, type VARCHAR(50) NOT NULL, country VARCHAR(2) NOT NULL, iban VARCHAR(34) NOT NULL, CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C9B6B5FBA ON transactions (account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE business_partners');
        $this->addSql('DROP TABLE exchanges');
        $this->addSql('DROP TABLE transactions');
    }
}
