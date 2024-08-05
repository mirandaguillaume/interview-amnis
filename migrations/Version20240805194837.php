<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240805194837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE accounts (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, business_partner_id INTEGER NOT NULL, currency VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_CAC89EAC5330F055 FOREIGN KEY (business_partner_id) REFERENCES business_partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE);
            CREATE INDEX IDX_CAC89EAC5330F055 ON accounts (business_partner_id);
            CREATE TEMPORARY TABLE __temp__business_partners AS SELECT id, name, status, legal_form, address, city, zip, country FROM business_partners;
            DROP TABLE business_partners;
            CREATE TABLE business_partners (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, legal_form VARCHAR(255) NOT NULL, address VARCHAR(70) NOT NULL, city VARCHAR(35) NOT NULL, zip VARCHAR(16) NOT NULL, country VARCHAR(2) NOT NULL);
            INSERT INTO business_partners (id, name, status, legal_form, address, city, zip, country) SELECT id, name, status, legal_form, address, city, zip, country FROM __temp__business_partners;
            DROP TABLE __temp__business_partners;
            CREATE TEMPORARY TABLE __temp__transactions AS SELECT id, business_partner_id, amount, name, date, executed, type, country, iban FROM transactions;
            DROP TABLE transactions;
            CREATE TABLE transactions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, account_id INTEGER NOT NULL, amount NUMERIC(10, 2) NOT NULL, name VARCHAR(255) NOT NULL, date DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , executed BOOLEAN NOT NULL, type VARCHAR(50) NOT NULL, country VARCHAR(2) NOT NULL, iban VARCHAR(34) NOT NULL, CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE);  
            INSERT INTO transactions (id, account_id, amount, name, date, executed, type, country, iban) SELECT id, business_partner_id, amount, name, date, executed, type, country, iban FROM __temp__transactions;
            DROP TABLE __temp__transactions;
            CREATE INDEX IDX_EAA81A4C9B6B5FBA ON transactions (account_id);
            CREATE UNIQUE INDEX business_currencies ON accounts (business_partner_id, currency);
        SQL);

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
