<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Schema\Schema;

class Version20170110130325 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription(): string 
    {
        return 'Adjust foreign key and index names to the renaming of TYPO3.Party to Neos.Party';
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        // Renaming of indexes is only possible with MySQL version 5.7+
        if ($this->connection->getDatabasePlatform() instanceof MySQL57Platform) {
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join RENAME INDEX idx_1eeebc2f38110e12 TO IDX_E4E61AB038110E12');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join RENAME INDEX uniq_1eeebc2f58842efc TO UNIQ_E4E61AB058842EFC');
            $this->addSql('ALTER TABLE neos_party_domain_model_person RENAME INDEX uniq_c60479e15e237e06 TO UNIQ_A7B0E9CC5E237E06');
            $this->addSql('ALTER TABLE neos_party_domain_model_person RENAME INDEX idx_c60479e1a7cecf13 TO IDX_A7B0E9CCA7CECF13');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join RENAME INDEX idx_be7d49f772aaaa2f TO IDX_131A08DD72AAAA2F');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join RENAME INDEX idx_be7d49f7b06bd60d TO IDX_131A08DDB06BD60D');
        } else {
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_1EEEBC2F38110E12');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_1EEEBC2F58842EFC');
            $this->addSql('DROP INDEX idx_1eeebc2f38110e12 ON neos_party_domain_model_abstractparty_accounts_join');
            $this->addSql('CREATE INDEX IDX_E4E61AB038110E12 ON neos_party_domain_model_abstractparty_accounts_join (party_abstractparty)');
            $this->addSql('DROP INDEX uniq_1eeebc2f58842efc ON neos_party_domain_model_abstractparty_accounts_join');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_E4E61AB058842EFC ON neos_party_domain_model_abstractparty_accounts_join (flow_security_account)');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F38110E12 FOREIGN KEY (party_abstractparty) REFERENCES neos_party_domain_model_abstractparty (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_1EEEBC2F58842EFC FOREIGN KEY (flow_security_account) REFERENCES neos_flow_security_account (persistence_object_identifier) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY neos_party_domain_model_person_ibfk_1');
            $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY neos_party_domain_model_person_ibfk_2');
            $this->addSql('DROP INDEX uniq_c60479e15e237e06 ON neos_party_domain_model_person');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_A7B0E9CC5E237E06 ON neos_party_domain_model_person (name)');
            $this->addSql('DROP INDEX idx_c60479e1a7cecf13 ON neos_party_domain_model_person');
            $this->addSql('CREATE INDEX IDX_A7B0E9CCA7CECF13 ON neos_party_domain_model_person (primaryelectronicaddress)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT neos_party_domain_model_person_ibfk_1 FOREIGN KEY (name) REFERENCES neos_party_domain_model_personname (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT neos_party_domain_model_person_ibfk_2 FOREIGN KEY (primaryelectronicaddress) REFERENCES neos_party_domain_model_electronicaddress (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY neos_party_domain_model_person_electronicaddresses_join_ibfk_1');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY neos_party_domain_model_person_electronicaddresses_join_ibfk_2');
            $this->addSql('DROP INDEX idx_be7d49f772aaaa2f ON neos_party_domain_model_person_electronicaddresses_join');
            $this->addSql('CREATE INDEX IDX_131A08DD72AAAA2F ON neos_party_domain_model_person_electronicaddresses_join (party_person)');
            $this->addSql('DROP INDEX idx_be7d49f7b06bd60d ON neos_party_domain_model_person_electronicaddresses_join');
            $this->addSql('CREATE INDEX IDX_131A08DDB06BD60D ON neos_party_domain_model_person_electronicaddresses_join (party_electronicaddress)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT neos_party_domain_model_person_electronicaddresses_join_ibfk_1 FOREIGN KEY (party_person) REFERENCES neos_party_domain_model_person (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT neos_party_domain_model_person_electronicaddresses_join_ibfk_2 FOREIGN KEY (party_electronicaddress) REFERENCES neos_party_domain_model_electronicaddress (persistence_object_identifier)');
        }
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        // Renaming of indexes is only possible with MySQL version 5.7+
        if ($this->connection->getDatabasePlatform() instanceof MySQL57Platform) {
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join RENAME INDEX uniq_e4e61ab058842efc TO UNIQ_1EEEBC2F58842EFC');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join RENAME INDEX idx_e4e61ab038110e12 TO IDX_1EEEBC2F38110E12');
            $this->addSql('ALTER TABLE neos_party_domain_model_person RENAME INDEX uniq_a7b0e9cc5e237e06 TO UNIQ_C60479E15E237E06');
            $this->addSql('ALTER TABLE neos_party_domain_model_person RENAME INDEX idx_a7b0e9cca7cecf13 TO IDX_C60479E1A7CECF13');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join RENAME INDEX idx_131a08dd72aaaa2f TO IDX_BE7D49F772AAAA2F');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join RENAME INDEX idx_131a08ddb06bd60d TO IDX_BE7D49F7B06BD60D');
        } else {
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_E4E61AB038110E12');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join DROP FOREIGN KEY FK_E4E61AB058842EFC');
            $this->addSql('DROP INDEX uniq_e4e61ab058842efc ON neos_party_domain_model_abstractparty_accounts_join');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_1EEEBC2F58842EFC ON neos_party_domain_model_abstractparty_accounts_join (flow_security_account)');
            $this->addSql('DROP INDEX idx_e4e61ab038110e12 ON neos_party_domain_model_abstractparty_accounts_join');
            $this->addSql('CREATE INDEX IDX_1EEEBC2F38110E12 ON neos_party_domain_model_abstractparty_accounts_join (party_abstractparty)');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_E4E61AB038110E12 FOREIGN KEY (party_abstractparty) REFERENCES neos_party_domain_model_abstractparty (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_abstractparty_accounts_join ADD CONSTRAINT FK_E4E61AB058842EFC FOREIGN KEY (flow_security_account) REFERENCES neos_flow_security_account (persistence_object_identifier) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY FK_A7B0E9CC5E237E06');
            $this->addSql('ALTER TABLE neos_party_domain_model_person DROP FOREIGN KEY FK_A7B0E9CCA7CECF13');
            $this->addSql('DROP INDEX uniq_a7b0e9cc5e237e06 ON neos_party_domain_model_person');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_C60479E15E237E06 ON neos_party_domain_model_person (name)');
            $this->addSql('DROP INDEX idx_a7b0e9cca7cecf13 ON neos_party_domain_model_person');
            $this->addSql('CREATE INDEX IDX_C60479E1A7CECF13 ON neos_party_domain_model_person (primaryelectronicaddress)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT FK_A7B0E9CC5E237E06 FOREIGN KEY (name) REFERENCES neos_party_domain_model_personname (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person ADD CONSTRAINT FK_A7B0E9CCA7CECF13 FOREIGN KEY (primaryelectronicaddress) REFERENCES neos_party_domain_model_electronicaddress (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY FK_131A08DD72AAAA2F');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY FK_131A08DDB06BD60D');
            $this->addSql('DROP INDEX idx_131a08dd72aaaa2f ON neos_party_domain_model_person_electronicaddresses_join');
            $this->addSql('CREATE INDEX IDX_BE7D49F772AAAA2F ON neos_party_domain_model_person_electronicaddresses_join (party_person)');
            $this->addSql('DROP INDEX idx_131a08ddb06bd60d ON neos_party_domain_model_person_electronicaddresses_join');
            $this->addSql('CREATE INDEX IDX_BE7D49F7B06BD60D ON neos_party_domain_model_person_electronicaddresses_join (party_electronicaddress)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT FK_131A08DD72AAAA2F FOREIGN KEY (party_person) REFERENCES neos_party_domain_model_person (persistence_object_identifier)');
            $this->addSql('ALTER TABLE neos_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT FK_131A08DDB06BD60D FOREIGN KEY (party_electronicaddress) REFERENCES neos_party_domain_model_electronicaddress (persistence_object_identifier)');
        }
    }
}