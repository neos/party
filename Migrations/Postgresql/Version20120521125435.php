<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Drop unique indexes for identity properties, reverting migration 20120429225206 (see http://forge.typo3.org/issues/37266)
 */
class Version20120521125435 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("DROP INDEX flow3_identity_typo3_party_domain_model_electronicaddress");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("CREATE UNIQUE INDEX flow3_identity_typo3_party_domain_model_electronicaddress ON typo3_party_domain_model_electronicaddress (identifier, type, usagetype)");
	}
}
