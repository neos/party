<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Party Migration
 */
class Version20110613224537 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE party_abstractparty (flow3_persistence_identifier VARCHAR(40) NOT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(flow3_persistence_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE party_electronicaddress (flow3_persistence_identifier VARCHAR(40) NOT NULL, identifier VARCHAR(255) DEFAULT NULL, type VARCHAR(20) NOT NULL, usagetype VARCHAR(20) NOT NULL, approved TINYINT(1) DEFAULT NULL, PRIMARY KEY(flow3_persistence_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE party_person (flow3_persistence_identifier VARCHAR(40) NOT NULL, party_personname VARCHAR(40) DEFAULT NULL, party_electronicaddress VARCHAR(40) DEFAULT NULL, UNIQUE INDEX UNIQ_72AAAA2F987E5DAB (party_personname), INDEX IDX_72AAAA2FB06BD60D (party_electronicaddress), PRIMARY KEY(flow3_persistence_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE party_person_electronicaddresses_join (party_person VARCHAR(40) NOT NULL, party_electronicaddress VARCHAR(40) NOT NULL, INDEX IDX_759CC08F72AAAA2F (party_person), INDEX IDX_759CC08FB06BD60D (party_electronicaddress), PRIMARY KEY(party_person, party_electronicaddress)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE party_personname (flow3_persistence_identifier VARCHAR(40) NOT NULL, title VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, middlename VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, othername VARCHAR(255) DEFAULT NULL, alias VARCHAR(255) DEFAULT NULL, fullname VARCHAR(255) DEFAULT NULL, PRIMARY KEY(flow3_persistence_identifier)) ENGINE = InnoDB");
		$this->addSql("ALTER TABLE flow3_security_account ADD CONSTRAINT flow3_security_account_ibfk_1 FOREIGN KEY (party_abstractparty) REFERENCES party_abstractparty(flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE party_person ADD CONSTRAINT party_person_ibfk_1 FOREIGN KEY (party_personname) REFERENCES party_personname(flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE party_person ADD CONSTRAINT party_person_ibfk_2 FOREIGN KEY (party_electronicaddress) REFERENCES party_electronicaddress(flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE party_person ADD CONSTRAINT party_person_ibfk_3 FOREIGN KEY (flow3_persistence_identifier) REFERENCES party_abstractparty(flow3_persistence_identifier) ON DELETE CASCADE");
		$this->addSql("ALTER TABLE party_person_electronicaddresses_join ADD CONSTRAINT party_person_electronicaddresses_join_ibfk_1 FOREIGN KEY (party_person) REFERENCES party_person(flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE party_person_electronicaddresses_join ADD CONSTRAINT party_person_electronicaddresses_join_ibfk_2 FOREIGN KEY (party_electronicaddress) REFERENCES party_electronicaddress(flow3_persistence_identifier)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE flow3_security_account DROP FOREIGN KEY flow3_security_account_ibfk_1");
		$this->addSql("ALTER TABLE party_person DROP FOREIGN KEY party_person_ibfk_3");
		$this->addSql("ALTER TABLE party_person DROP FOREIGN KEY party_person_ibfk_2");
		$this->addSql("ALTER TABLE party_person_electronicaddresses_join DROP FOREIGN KEY party_person_electronicaddresses_join_ibfk_2");
		$this->addSql("ALTER TABLE party_person_electronicaddresses_join DROP FOREIGN KEY party_person_electronicaddresses_join_ibfk_1");
		$this->addSql("ALTER TABLE party_person DROP FOREIGN KEY party_person_ibfk_1");
		$this->addSql("DROP TABLE party_abstractparty");
		$this->addSql("DROP TABLE party_electronicaddress");
		$this->addSql("DROP TABLE party_person");
		$this->addSql("DROP TABLE party_person_electronicaddresses_join");
		$this->addSql("DROP TABLE party_personname");
	}
}

?>