<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename table names after changing package name to Neos.Party.
 */
class Version20161124230946 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('RENAME TABLE typo3_party_domain_model_abstractparty TO neos_party_domain_model_abstractparty');
        $this->addSql('RENAME TABLE typo3_party_domain_model_abstractparty_accounts_join TO neos_party_domain_model_abstractparty_accounts_join');
        $this->addSql('RENAME TABLE typo3_party_domain_model_person TO neos_party_domain_model_person');
        $this->addSql('RENAME TABLE typo3_party_domain_model_electronicaddress TO neos_party_domain_model_electronicaddress');
        $this->addSql('RENAME TABLE typo3_party_domain_model_person_electronicaddresses_join TO neos_party_domain_model_person_electronicaddresses_join');
        $this->addSql('RENAME TABLE typo3_party_domain_model_personname TO neos_party_domain_model_personname');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('RENAME TABLE neos_party_domain_model_abstractparty TO typo3_party_domain_model_abstractparty');
        $this->addSql('RENAME TABLE neos_party_domain_model_abstractparty_accounts_join TO typo3_party_domain_model_abstractparty_accounts_join');
        $this->addSql('RENAME TABLE neos_party_domain_model_person TO typo3_party_domain_model_person');
        $this->addSql('RENAME TABLE neos_party_domain_model_electronicaddress TO typo3_party_domain_model_electronicaddress');
        $this->addSql('RENAME TABLE neos_party_domain_model_person_electronicaddresses_join TO typo3_party_domain_model_person_electronicaddresses_join');
        $this->addSql('RENAME TABLE neos_party_domain_model_personname TO typo3_party_domain_model_personname');
    }
}
