<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema,
	TYPO3\Flow\Persistence\Doctrine\Service;

/**
 * Adjust flow3 to flow
 */
class Version20120930211542 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		// collect foreign keys pointing to "our" tables
		$tableNames = array(
			'typo3_party_domain_model_abstractparty',
			'typo3_party_domain_model_electronicaddress',
			'typo3_party_domain_model_person',
			'typo3_party_domain_model_personname'
		);
		$foreignKeyHandlingSql = Service::getForeignKeyHandlingSql($schema, $this->platform, $tableNames, 'flow3_persistence_identifier', 'persistence_object_identifier');

		// drop FK constraints
		foreach ($foreignKeyHandlingSql['drop'] as $sql) {
			$this->addSql($sql);
		}

		// rename identifier fields
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty CHANGE flow3_persistence_identifier persistence_object_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty ADD PRIMARY KEY (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress CHANGE flow3_persistence_identifier persistence_object_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress ADD PRIMARY KEY (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person CHANGE flow3_persistence_identifier persistence_object_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD PRIMARY KEY (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname CHANGE flow3_persistence_identifier persistence_object_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname ADD PRIMARY KEY (persistence_object_identifier)");

		// add back FK constraints
		foreach ($foreignKeyHandlingSql['add'] as $sql) {
			$this->addSql($sql);
		}
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		// collect foreign keys pointing to "our" tables
		$tableNames = array(
			'typo3_party_domain_model_abstractparty',
			'typo3_party_domain_model_electronicaddress',
			'typo3_party_domain_model_person',
			'typo3_party_domain_model_personname'
		);
		$foreignKeyHandlingSql = Service::getForeignKeyHandlingSql($schema, $this->platform, $tableNames, 'persistence_object_identifier', 'flow3_persistence_identifier');

		// drop FK constraints
		foreach ($foreignKeyHandlingSql['drop'] as $sql) {
			$this->addSql($sql);
		}

		// rename identifier fields
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty CHANGE persistence_object_identifier flow3_persistence_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty ADD PRIMARY KEY (flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress CHANGE persistence_object_identifier flow3_persistence_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_electronicaddress ADD PRIMARY KEY (flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person CHANGE persistence_object_identifier flow3_persistence_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_person ADD PRIMARY KEY (flow3_persistence_identifier)");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname DROP PRIMARY KEY");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname CHANGE persistence_object_identifier flow3_persistence_identifier VARCHAR(40) NOT NULL");
		$this->addSql("ALTER TABLE typo3_party_domain_model_personname ADD PRIMARY KEY (flow3_persistence_identifier)");

		// add back FK constraints
		foreach ($foreignKeyHandlingSql['add'] as $sql) {
			$this->addSql($sql);
		}
	}

}
