<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Update dtype values
 */
class Version20110714212900 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("UPDATE party_abstractparty SET dtype=CONCAT('typo3_', SUBSTR(dtype, 4)) WHERE dtype LIKE 'f3_%'");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("UPDATE party_abstractparty SET dtype=CONCAT('f3_', SUBSTR(dtype, 4)) WHERE dtype LIKE 'typo3_%'");
	}
}

?>