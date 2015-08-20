<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Core\Persistence\Database\Service\DoctrineManager;
use Core\Domain\Entity\About;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150819134145 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        
                $this->addSql('CREATE TABLE temp2 (id INT NOT NULL, testField VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        //$this->addSql('DROP TABLE temp2');

    }
}
