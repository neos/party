<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Add the needed dtype columns to make ElectronicAddress and PersonName
 * extensible in userland code.
 */
class Version20150612093351 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress ADD dtype VARCHAR(255) NOT NULL");
		$this->addSql("UPDATE typo3_party_domain_model_electronicaddress SET dtype = 'typo3_party_electronicaddress' WHERE dtype = ''");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname ADD dtype VARCHAR(255) NOT NULL");
		$this->addSql("UPDATE typo3_party_domain_model_personname SET dtype = 'typo3_party_personname' WHERE dtype = ''");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress DROP dtype");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname DROP dtype");
	}
}