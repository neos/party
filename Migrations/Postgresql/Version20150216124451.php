<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Adjust schema to Flow 3.0 "Party package decoupling"
 */
class Version20150216124451 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("CREATE TABLE typo3_party_domain_model_abstractparty_accounts_join (party_abstractparty VARCHAR(40) NOT NULL, flow_security_account VARCHAR(40) NOT NULL, PRIMARY KEY(party_abstractparty, flow_security_account))");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F38110E12 FOREIGN KEY (party_abstractparty) REFERENCES typo3_party_domain_model_abstractparty (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");

        if ($this->partyColumnInFlowSecurityAccountExists()) {
            $this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
            $this->addSql("INSERT INTO typo3_party_domain_model_abstractparty_accounts_join (flow_security_account, party_abstractparty) SELECT persistence_object_identifier, party FROM typo3_flow_security_account WHERE party IS NOT NULL");
		}
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		if ($this->partyColumnInFlowSecurityAccountExists()) {
			$this->addSql("UPDATE typo3_flow_security_account AS a SET party = j.party_abstractparty FROM typo3_party_domain_model_abstractparty_accounts_join AS j WHERE a.party = j.party_abstractparty");
		}
		$this->addSql("DROP TABLE typo3_party_domain_model_abstractparty_accounts_join");
	}

	/**
	 * @return boolean
	 */
	protected function partyColumnInFlowSecurityAccountExists() {
		return array_key_exists('party', $this->sm->listTableColumns('typo3_flow_security_account'));
	}
}
