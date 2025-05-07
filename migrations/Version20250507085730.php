<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507085730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE event_date DROP FOREIGN KEY FK_B5557BD1436D055B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B5557BD1436D055B ON event_date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_date ADD date_time DATETIME NOT NULL, CHANGE datetime_id event_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_date ADD CONSTRAINT FK_B5557BD171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B5557BD171F7E88B ON event_date (event_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE event_date DROP FOREIGN KEY FK_B5557BD171F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B5557BD171F7E88B ON event_date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_date DROP date_time, CHANGE event_id datetime_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_date ADD CONSTRAINT FK_B5557BD1436D055B FOREIGN KEY (datetime_id) REFERENCES event (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B5557BD1436D055B ON event_date (datetime_id)
        SQL);
    }
}
