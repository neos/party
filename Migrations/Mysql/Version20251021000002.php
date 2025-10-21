<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251021000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables for Neos.Party';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQLPlatform'."
        );

        $this->addSql('CREATE TABLE neos_party_domain_model_abstractparty (persistence_object_identifier VARCHAR(40) NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE neos_party_domain_model_abstractparty_accounts_join (party_abstractparty VARCHAR(40) NOT NULL, flow_security_account VARCHAR(40) NOT NULL, INDEX IDX_E4E61AB038110E12 (party_abstractparty), UNIQUE INDEX UNIQ_E4E61AB058842EFC (flow_security_account), PRIMARY KEY(party_abstractparty, flow_security_account)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE neos_party_domain_model_electronicaddress (persistence_object_identifier VARCHAR(40) NOT NULL, identifier VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, usagetype VARCHAR(20) DEFAULT NULL, approved TINYINT(1) NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE neos_party_domain_model_person (persistence_object_identifier VARCHAR(40) NOT NULL, name VARCHAR(40) DEFAULT NULL, primaryelectronicaddress VARCHAR(40) DEFAULT NULL, UNIQUE INDEX UNIQ_A7B0E9CC5E237E06 (name), INDEX IDX_A7B0E9CCA7CECF13 (primaryelectronicaddress), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE neos_party_domain_model_person_electronicaddresses_join (party_person VARCHAR(40) NOT NULL, party_electronicaddress VARCHAR(40) NOT NULL, INDEX IDX_131A08DD72AAAA2F (party_person), INDEX IDX_131A08DDB06BD60D (party_electronicaddress), PRIMARY KEY(party_person, party_electronicaddress)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE neos_party_domain_model_personname (persistence_object_identifier VARCHAR(40) NOT NULL, title VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, middlename VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, othername VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_E4E61AB038110E12 FOREIGN KEY (party_abstractparty) REFERENCES neos_party_domain_model_abstractparty (persistence_object_identifier)');
        $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_E4E61AB058842EFC FOREIGN KEY (flow_security_account) REFERENCES neos_flow_security_account (persistence_object_identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT FK_A7B0E9CC5E237E06 FOREIGN KEY (name) REFERENCES neos_party_domain_model_personname (persistence_object_identifier)');
        $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT FK_A7B0E9CCA7CECF13 FOREIGN KEY (primaryelectronicaddress) REFERENCES neos_party_domain_model_electronicaddress (persistence_object_identifier)');
        $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT FK_A7B0E9CC47A46B0A FOREIGN KEY (persistence_object_identifier) REFERENCES neos_party_domain_model_abstractparty (persistence_object_identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT FK_131A08DD72AAAA2F FOREIGN KEY (party_person) REFERENCES neos_party_domain_model_person (persistence_object_identifier)');
        $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT FK_131A08DDB06BD60D FOREIGN KEY (party_electronicaddress) REFERENCES neos_party_domain_model_electronicaddress (persistence_object_identifier)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQLPlatform'."
        );

        $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_E4E61AB038110E12');
        $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_E4E61AB058842EFC');
        $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY FK_A7B0E9CC5E237E06');
        $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY FK_A7B0E9CCA7CECF13');
        $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY FK_A7B0E9CC47A46B0A');
        $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY FK_131A08DD72AAAA2F');
        $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY FK_131A08DDB06BD60D');
        $this->addSql('DROP TABLE neos_party_domain_model_abstractparty');
        $this->addSql('DROP TABLE neos_party_domain_model_abstractparty_accounts_join');
        $this->addSql('DROP TABLE neos_party_domain_model_electronicaddress');
        $this->addSql('DROP TABLE neos_party_domain_model_person');
        $this->addSql('DROP TABLE neos_party_domain_model_person_electronicaddresses_join');
        $this->addSql('DROP TABLE neos_party_domain_model_personname');
    }
}
