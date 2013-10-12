<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Fix column names for direct associations
 */
class Version20110923125536 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP FOREIGN KEY typo3_party_domain_model_person_ibfk_1");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP FOREIGN KEY typo3_party_domain_model_person_ibfk_2");
		$this->addSql("DROP INDEX UNIQ_72AAAA2F987E5DAB ON typo3_party_domain_model_person");
		$this->addSql("DROP INDEX IDX_72AAAA2FB06BD60D ON typo3_party_domain_model_person");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person CHANGE party_personname name VARCHAR(40) DEFAULT NULL, CHANGE party_electronicaddress primaryelectronicaddress VARCHAR(40) DEFAULT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT typo3_party_domain_model_person_ibfk_1 FOREIGN KEY (name) REFERENCES typo3_party_domain_model_personname(flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT typo3_party_domain_model_person_ibfk_2 FOREIGN KEY (primaryelectronicaddress) REFERENCES typo3_party_domain_model_electronicaddress(flow3_persistence_identifier)");
		$this->addSql("CREATE UNIQUE INDEX UNIQ_C60479E15E237E06 ON typo3_party_domain_model_person (name)");
		$this->addSql("CREATE INDEX IDX_C60479E1A7CECF13 ON typo3_party_domain_model_person (primaryelectronicaddress)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP FOREIGN KEY typo3_party_domain_model_person_ibfk_1");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP FOREIGN KEY typo3_party_domain_model_person_ibfk_2");
		$this->addSql("DROP INDEX UNIQ_C60479E15E237E06 ON typo3_party_domain_model_person");
		$this->addSql("DROP INDEX IDX_C60479E1A7CECF13 ON typo3_party_domain_model_person");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person CHANGE name party_personname VARCHAR(40) DEFAULT NULL, CHANGE primaryelectronicaddress party_electronicaddress VARCHAR(40) DEFAULT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT typo3_party_domain_model_person_ibfk_1 FOREIGN KEY (party_personname) REFERENCES typo3_party_domain_model_personname(flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD CONSTRAINT typo3_party_domain_model_person_ibfk_2 FOREIGN KEY (party_electronicaddress) REFERENCES typo3_party_domain_model_electronicaddress(flow3_persistence_identifier)");
		$this->addSql("CREATE UNIQUE INDEX UNIQ_72AAAA2F987E5DAB ON typo3_party_domain_model_person (party_personname)");
		$this->addSql("CREATE INDEX IDX_72AAAA2FB06BD60D ON typo3_party_domain_model_person (party_electronicaddress)");
	}
}
