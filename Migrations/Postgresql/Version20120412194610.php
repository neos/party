<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Create tables for PostgreSQL
 */
class Version20120412194610 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("CREATE TABLE typo3_party_domain_model_abstractparty (flow3_persistence_identifier VARCHAR(40) NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(flow3_persistence_identifier))");
		$this->addSql("CREATE TABLE typo3_party_domain_model_person (flow3_persistence_identifier VARCHAR(40) NOT NULL, name VARCHAR(40) DEFAULT NULL, primaryelectronicaddress VARCHAR(40) DEFAULT NULL, PRIMARY KEY(flow3_persistence_identifier))");
		$this->addSql("CREATE UNIQUE INDEX UNIQ_C60479E15E237E06 ON typo3_party_domain_model_person (name)");
		$this->addSql("CREATE INDEX IDX_C60479E1A7CECF13 ON typo3_party_domain_model_person (primaryelectronicaddress)");
		$this->addSql("CREATE TABLE typo3_party_domain_model_person_electronicaddresses_join (party_person VARCHAR(40) NOT NULL, party_electronicaddress VARCHAR(40) NOT NULL, PRIMARY KEY(party_person, party_electronicaddress))");
		$this->addSql("CREATE INDEX IDX_BE7D49F772AAAA2F ON typo3_party_domain_model_person_electronicaddresses_join (party_person)");
		$this->addSql("CREATE INDEX IDX_BE7D49F7B06BD60D ON typo3_party_domain_model_person_electronicaddresses_join (party_electronicaddress)");
		$this->addSql("CREATE TABLE typo3_party_domain_model_electronicaddress (flow3_persistence_identifier VARCHAR(40) NOT NULL, identifier VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, usagetype VARCHAR(20) NOT NULL, approved BOOLEAN NOT NULL, PRIMARY KEY(flow3_persistence_identifier))");
		$this->addSql("CREATE TABLE typo3_party_domain_model_personname (flow3_persistence_identifier VARCHAR(40) NOT NULL, title VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, middlename VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, othername VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, PRIMARY KEY(flow3_persistence_identifier))");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT FK_C60479E15E237E06 FOREIGN KEY (name) REFERENCES typo3_party_domain_model_personname (flow3_persistence_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT FK_C60479E1A7CECF13 FOREIGN KEY (primaryelectronicaddress) REFERENCES typo3_party_domain_model_electronicaddress (flow3_persistence_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT FK_C60479E121E3D446 FOREIGN KEY (flow3_persistence_identifier) REFERENCES typo3_party_domain_model_abstractparty (flow3_persistence_identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT FK_BE7D49F772AAAA2F FOREIGN KEY (party_person) REFERENCES typo3_party_domain_model_person (flow3_persistence_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT FK_BE7D49F7B06BD60D FOREIGN KEY (party_electronicaddress) REFERENCES typo3_party_domain_model_electronicaddress (flow3_persistence_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP CONSTRAINT FK_C60479E121E3D446");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join DROP CONSTRAINT FK_BE7D49F772AAAA2F");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP CONSTRAINT FK_C60479E1A7CECF13");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join DROP CONSTRAINT FK_BE7D49F7B06BD60D");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP CONSTRAINT FK_C60479E15E237E06");
		$this->addSql("DROP TABLE typo3_party_domain_model_abstractparty");
		$this->addSql("DROP TABLE typo3_party_domain_model_person");
		$this->addSql("DROP TABLE typo3_party_domain_model_person_electronicaddresses_join");
		$this->addSql("DROP TABLE typo3_party_domain_model_electronicaddress");
		$this->addSql("DROP TABLE typo3_party_domain_model_personname");
	}
}

?>