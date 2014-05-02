<?php
class UpdateThumbnailSmallToArticles extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn( 'articles', 'thumbnailsmallid', 'integer', array('notnull' => false));
    }

    public function down()
    {
        $this->changeColumn( 'articles', 'thumbnailsmallid', 'integer', array('notnull' => true));
    }
}
