<?php

namespace UserMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151103113930 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('splashImages');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('image', 'string', array('notnull' => 1));
        $table->addColumn('position', 'integer', array('notnull' => 1));
        $table->addColumn('createdAt', 'datetime', array('length' => 12));
        $table->setPrimaryKey(array('id'));
    }

    public function postUp(Schema $schema)
    {
        $this->connection->executeQuery("INSERT INTO `splashImages` (`id`, `image`, `position`, `createdAt`) VALUES (NULL, 'slide_img1.jpg','1', NOW()),(NULL, 'slide_img2.jpg','2', NOW()), (NULL, 'slide_img3.jpg','3', NOW()), (NULL, 'slide_img4.jpg','4', NOW()), (NULL, 'slide_img5.jpg','5', NOW()), (NULL, 'slide_img6.jpg','6', NOW()), (NULL, 'slide_img7.jpg','7', NOW()), (NULL, 'slide_img8.jpg','8', NOW()), (NULL, 'slide_img9.jpg','9', NOW())");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('splashImages');
    }
}
