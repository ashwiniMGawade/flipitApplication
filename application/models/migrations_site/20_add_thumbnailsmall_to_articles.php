<?php
class AddThumbnailSmallToArticles extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'articles', 'thumbnailsmallid', 'integer',8 , array(
                'type' => 'integer',
                'length' => 8,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
    }

    public function down()
    {
        $this->removeColumn('articles', 'thumbnailsmallid');
    }
}
