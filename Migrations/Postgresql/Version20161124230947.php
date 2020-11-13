<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename table names after changing package name to Neos.Party.
 */
class Version20161124230947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

        $this->addSql('ALTER TABLE typo3_party_domain_model_abstractparty RENAME TO neos_party_domain_model_abstractparty');
        $this->addSql('ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join RENAME TO neos_party_domain_model_abstractparty_accounts_join');
        $this->addSql('ALTER TABLE typo3_party_domain_model_person RENAME TO neos_party_domain_model_person');
        $this->addSql('ALTER TABLE typo3_party_domain_model_electronicaddress RENAME TO neos_party_domain_model_electronicaddress');
        $this->addSql('ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join RENAME TO neos_party_domain_model_person_electronicaddresses_join');
        $this->addSql('ALTER TABLE typo3_party_domain_model_personname RENAME TO neos_party_domain_model_personname');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

        $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty RENAME TO typo3_party_domain_model_abstractparty');
        $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join RENAME TO typo3_party_domain_model_abstractparty_accounts_join');
        $this->addSql('ALTER TABLE neos_party_domain_model_person RENAME TO typo3_party_domain_model_person');
        $this->addSql('ALTER TABLE neos_party_domain_model_electronicaddress RENAME TO typo3_party_domain_model_electronicaddress');
        $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join RENAME TO typo3_party_domain_model_person_electronicaddresses_join');
        $this->addSql('ALTER TABLE neos_party_domain_model_personname RENAME TO typo3_party_domain_model_personname');
    }
}
