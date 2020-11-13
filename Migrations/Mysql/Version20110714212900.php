<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Update dtype values
 */
class Version20110714212900 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("UPDATE party_abstractparty SET dtype=CONCAT('typo3_', SUBSTR(dtype, 4)) WHERE dtype LIKE 'f3_%'");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("UPDATE party_abstractparty SET dtype=CONCAT('f3_', SUBSTR(dtype, 4)) WHERE dtype LIKE 'typo3_%'");
	}
}
