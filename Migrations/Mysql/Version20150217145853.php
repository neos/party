<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Change AbstractParty:accounts foreign key constraint to delete on cascade
 */
class Version20150217145853 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        if ($this->sm->tablesExist(['typo3_flow_security_account'])) {
            $this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_1EEEBC2F58842EFC");
            $this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier) ON DELETE CASCADE");
        }
        if ($this->sm->tablesExist(['neos_flow_security_account'])) {
            $this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES neos_flow_security_account (persistence_object_identifier) ON DELETE CASCADE");
        }
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_1EEEBC2F58842EFC");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier)");
	}
}
