<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Create unique indexes for identity properties
 */
class Version20120429213446 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE UNIQUE INDEX flow3_identity_typo3_party_domain_model_electronicaddress ON typo3_party_domain_model_electronicaddress (identifier, type, usagetype)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("DROP INDEX flow3_identity_typo3_party_domain_model_electronicaddress ON typo3_party_domain_model_electronicaddress");
	}
}
