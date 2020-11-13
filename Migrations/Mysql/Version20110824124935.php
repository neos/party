<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Rename Party tables to follow FQCN
 */
class Version20110824124935 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("RENAME TABLE party_abstractparty TO typo3_party_domain_model_abstractparty");
		$this->addSql("RENAME TABLE party_electronicaddress TO typo3_party_domain_model_electronicaddress");
		$this->addSql("RENAME TABLE party_person TO typo3_party_domain_model_person");
		$this->addSql("RENAME TABLE party_person_electronicaddresses_join TO typo3_party_domain_model_person_electronicaddresses_join");
		$this->addSql("RENAME TABLE party_personname TO typo3_party_domain_model_personname");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("RENAME TABLE typo3_party_domain_model_abstractparty TO party_abstractparty");
		$this->addSql("RENAME TABLE typo3_party_domain_model_electronicaddress TO party_electronicaddress");
		$this->addSql("RENAME TABLE typo3_party_domain_model_person TO party_person");
		$this->addSql("RENAME TABLE typo3_party_domain_model_person_electronicaddresses_join TO party_person_electronicaddresses_join");
		$this->addSql("RENAME TABLE typo3_party_domain_model_personname TO party_personname");
	}
}
