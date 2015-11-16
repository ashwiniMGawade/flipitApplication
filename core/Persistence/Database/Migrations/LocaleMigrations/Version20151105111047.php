<?php

namespace LocaleMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151105111047 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //update the page details
        $this->connection->executeQuery(
            "UPDATE page SET
              pagetitle = replace(pagetitle, '20', '50'),
              metatitle = replace(metatitle, '20', '50'),
              metadescription = replace(metadescription, '20', '50'),
              permalink= replace(permalink, '20', '50')
            WHERE
              permalink='top-20' or permalink='top20'"
        );

        //update the menu
        $this->connection->executeQuery(
            "UPDATE menu SET
              name = replace(name, '20', '50'),
              url = replace(url, '20', '50')
            WHERE
              url='top-20'"
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        //revert  the page details
        $this->connection->executeQuery(
            "UPDATE page SET
              pagetitle = replace(pagetitle, '50', '20'),
              metatitle = replace(metatitle, '50', '20'),
              metadescription = replace(metadescription, '50', '20'),
              permalink = replace(permalink, '50', '20')
            WHERE
              permalink='top-50'"
        );

        //revert  the page details
        $this->connection->executeQuery(
            "UPDATE menu SET
              name = replace(name, '50', '20'),
              url = replace(url, '50', '20')
            WHERE
              url='top-50'"
        );

    }
}
