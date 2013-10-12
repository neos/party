<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Adjust default values to NOT NULL unless allowed in model.
 */
class Version20120329220341 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress CHANGE identifier identifier VARCHAR(255) NOT NULL, CHANGE approved approved TINYINT(1) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname CHANGE title title VARCHAR(255) NOT NULL, CHANGE firstname firstname VARCHAR(255) NOT NULL, CHANGE middlename middlename VARCHAR(255) NOT NULL, CHANGE lastname lastname VARCHAR(255) NOT NULL, CHANGE othername othername VARCHAR(255) NOT NULL, CHANGE alias alias VARCHAR(255) NOT NULL, CHANGE fullname fullname VARCHAR(255) NOT NULL");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress CHANGE identifier identifier VARCHAR(255) DEFAULT NULL, CHANGE approved approved TINYINT(1) DEFAULT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE middlename middlename VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE othername othername VARCHAR(255) DEFAULT NULL, CHANGE alias alias VARCHAR(255) DEFAULT NULL, CHANGE fullname fullname VARCHAR(255) DEFAULT NULL");
	}
}
