<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Drop unique indexes for identity properties, reverting migration 20120429213446 (see http://forge.typo3.org/issues/37266)
 */
class Version20120521125401 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("DROP INDEX flow3_identity_typo3_party_domain_model_electronicaddress ON typo3_party_domain_model_electronicaddress");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE UNIQUE INDEX flow3_identity_typo3_party_domain_model_electronicaddress ON typo3_party_domain_model_electronicaddress (identifier, type, usagetype)");
	}
}

?>