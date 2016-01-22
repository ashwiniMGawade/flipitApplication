<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160122115720 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->executeQuery("UPDATE `shop` SET `classification`='-1' WHERE classification='1'");
    }

    public function down(Schema $schema)
    {
        $this->connection->executeQuery("UPDATE `shop` SET `classification`='1' WHERE classification='-1'");
    }
}
