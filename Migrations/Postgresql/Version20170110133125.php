<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170110133125 extends AbstractMigration
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on "postgresql".');

        $this->addSql('ALTER INDEX uniq_1eeebc2f58842efc RENAME TO UNIQ_E4E61AB058842EFC');
        $this->addSql('ALTER INDEX uniq_c60479e15e237e06 RENAME TO UNIQ_A7B0E9CC5E237E06');
        $this->addSql('ALTER INDEX idx_c60479e1a7cecf13 RENAME TO IDX_A7B0E9CCA7CECF13');
        $this->addSql('ALTER INDEX idx_be7d49f772aaaa2f RENAME TO IDX_131A08DD72AAAA2F');
        $this->addSql('ALTER INDEX idx_be7d49f7b06bd60d RENAME TO IDX_131A08DDB06BD60D');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void 
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on "postgresql".');

        $this->addSql('ALTER INDEX uniq_a7b0e9cc5e237e06 RENAME TO uniq_c60479e15e237e06');
        $this->addSql('ALTER INDEX idx_a7b0e9cca7cecf13 RENAME TO idx_c60479e1a7cecf13');
        $this->addSql('ALTER INDEX idx_131a08dd72aaaa2f RENAME TO idx_be7d49f772aaaa2f');
        $this->addSql('ALTER INDEX idx_131a08ddb06bd60d RENAME TO idx_be7d49f7b06bd60d');
        $this->addSql('ALTER INDEX uniq_e4e61ab058842efc RENAME TO uniq_1eeebc2f58842efc');
    }
}