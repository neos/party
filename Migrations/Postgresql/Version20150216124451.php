<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Adjust schema to Flow 3.0 "Party package decoupling"
 */
class Version20150216124451 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("CREATE TABLE typo3_party_domain_model_abstractparty_accounts_join (party_abstractparty VARCHAR(40) NOT NULL, flow_security_account VARCHAR(40) NOT NULL, PRIMARY KEY(party_abstractparty, flow_security_account))");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F38110E12 FOREIGN KEY (party_abstractparty) REFERENCES typo3_party_domain_model_abstractparty (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");

		$this->addSql("INSERT INTO typo3_party_domain_model_abstractparty_accounts_join (flow_security_account, party_abstractparty) SELECT persistence_object_identifier, party FROM typo3_flow_security_account");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
		$this->abortIf($this->isCorrespondingFlowMigrationExecuted(), 'Revert the corresponding Flow Migration version 20150206114820 first.');

		$this->addSql("UPDATE typo3_flow_security_account account LEFT JOIN typo3_party_domain_model_abstractparty_accounts_join accountsjoin ON account.persistence_object_identifier = accountsjoin.flow_security_account SET account.party = accountsjoin.party_abstractparty");
		$this->addSql("DROP TABLE typo3_party_domain_model_abstractparty_accounts_join");
	}

	/**
	 * @return boolean
	 */
	protected function isCorrespondingFlowMigrationExecuted() {
		return !array_key_exists('party', $this->sm->listTableColumns('typo3_flow_security_account'));
	}
}