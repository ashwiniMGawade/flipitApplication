<?php
class UpdateColumnInArticlesTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn( 'articles', 'thumbnailid', 'integer', array('notnull' => false));

    }

    public function down()
    {
        $this->changeColumn( 'articles', 'thumbnailid', 'integer', array('notnull' => true));
    }
}
