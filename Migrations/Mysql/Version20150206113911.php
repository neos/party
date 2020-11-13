<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\MigrationException;

/**
 * Adjust schema to Flow 3.0 "Party package decoupling"
 */
class Version20150206113911 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 * @throws MigrationException
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE typo3_party_domain_model_abstractparty_accounts_join (party_abstractparty VARCHAR(40) NOT NULL, flow_security_account VARCHAR(40) NOT NULL, INDEX IDX_1EEEBC2F38110E12 (party_abstractparty), UNIQUE INDEX UNIQ_1EEEBC2F58842EFC (flow_security_account), PRIMARY KEY(party_abstractparty, flow_security_account)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F38110E12 FOREIGN KEY (party_abstractparty) REFERENCES typo3_party_domain_model_abstractparty (persistence_object_identifier)");
        if ($this->sm->tablesExist(['typo3_flow_security_account'])) {
            $this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier)");
        }
		if ($this->partyColumnInFlowSecurityAccountExists()) {
			$this->addSql("INSERT INTO typo3_party_domain_model_abstractparty_accounts_join (flow_security_account, party_abstractparty) SELECT persistence_object_identifier, party FROM typo3_flow_security_account WHERE party IS NOT NULL");
		}
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		if ($this->partyColumnInFlowSecurityAccountExists()) {
			$this->addSql("UPDATE typo3_flow_security_account account LEFT JOIN typo3_party_domain_model_abstractparty_accounts_join accountsjoin ON account.persistence_object_identifier = accountsjoin.flow_security_account SET account.party = accountsjoin.party_abstractparty");
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
