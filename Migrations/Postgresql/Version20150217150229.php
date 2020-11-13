<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Change AbstractParty:accounts foreign key constraint to delete on cascade
 */
class Version20150217150229 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join DROP CONSTRAINT FK_1EEEBC2F58842EFC");
        if ($this->sm->tablesExist(['typo3_flow_security_account'])) {
            $this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        }
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void  {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join DROP CONSTRAINT FK_1EEEBC2F58842EFC");
		$this->addSql("ALTER TABLE typo3_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
	}
}
