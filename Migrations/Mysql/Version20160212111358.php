<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adjust some (old) index names to current Doctrine DBAL behavior (see https://jira.neos.io/browse/FLOW-427)
 */
class Version20160212111358 extends AbstractMigration
{

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY typo3_party_domain_model_person_electronicaddresses_join_ibfk_1");
        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY typo3_party_domain_model_person_electronicaddresses_join_ibfk_2");
        $this->addSql("DROP INDEX idx_759cc08f72aaaa2f ON typo3_party_domain_model_person_electronicaddresses_join");
        $this->addSql("CREATE INDEX IDX_BE7D49F772AAAA2F ON typo3_party_domain_model_person_electronicaddresses_join (party_person)");
        $this->addSql("DROP INDEX idx_759cc08fb06bd60d ON typo3_party_domain_model_person_electronicaddresses_join");
        $this->addSql("CREATE INDEX IDX_BE7D49F7B06BD60D ON typo3_party_domain_model_person_electronicaddresses_join (party_electronicaddress)");
        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT typo3_party_domain_model_person_electronicaddresses_join_ibfk_1 FOREIGN KEY (party_person) REFERENCES typo3_party_domain_model_person (persistence_object_identifier)");
        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT typo3_party_domain_model_person_electronicaddresses_join_ibfk_2 FOREIGN KEY (party_electronicaddress) REFERENCES typo3_party_domain_model_electronicaddress (persistence_object_identifier)");
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY typo3_party_domain_model_person_electronicaddresses_join_ibfk_1");
        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join DROP FOREIGN KEY typo3_party_domain_model_person_electronicaddresses_join_ibfk_2");
        $this->addSql("DROP INDEX idx_be7d49f772aaaa2f ON typo3_party_domain_model_person_electronicaddresses_join");
        $this->addSql("CREATE INDEX IDX_759CC08F72AAAA2F ON typo3_party_domain_model_person_electronicaddresses_join (party_person)");
        $this->addSql("DROP INDEX idx_be7d49f7b06bd60d ON typo3_party_domain_model_person_electronicaddresses_join");
        $this->addSql("CREATE INDEX IDX_759CC08FB06BD60D ON typo3_party_domain_model_person_electronicaddresses_join (party_electronicaddress)");
        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT typo3_party_domain_model_person_electronicaddresses_join_ibfk_1 FOREIGN KEY (party_person) REFERENCES typo3_party_domain_model_person (persistence_object_identifier)");
        $this->addSql("ALTER TABLE typo3_party_domain_model_person_electronicaddresses_join ADD CONSTRAINT typo3_party_domain_model_person_electronicaddresses_join_ibfk_2 FOREIGN KEY (party_electronicaddress) REFERENCES typo3_party_domain_model_electronicaddress (persistence_object_identifier)");
    }
}