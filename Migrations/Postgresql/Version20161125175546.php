<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename dtypes in
 * - neos_party_domain_model_personname
 * - neos_party_domain_model_electronicaddress
 * - neos_party_domain_model_abstractparty
 */
class Version20161125175546 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on "postgresql".');

        $this->addSql("UPDATE neos_party_domain_model_personname SET dtype = REPLACE(dtype, 'typo3_', 'neos_')");
        $this->addSql("UPDATE neos_party_domain_model_electronicaddress SET dtype = REPLACE(dtype, 'typo3_', 'neos_')");
        $this->addSql("UPDATE neos_party_domain_model_abstractparty SET dtype = REPLACE(dtype, 'typo3_', 'neos_')");
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on "postgresql".');

        $this->addSql("UPDATE neos_party_domain_model_personname SET dtype = REPLACE(dtype, 'neos_', 'typo3_')");
        $this->addSql("UPDATE neos_party_domain_model_electronicaddress SET dtype = REPLACE(dtype, 'neos_', 'typo3_')");
        $this->addSql("UPDATE neos_party_domain_model_abstractparty SET dtype = REPLACE(dtype, 'neos_', 'typo3_')");
    }
}
