<?php
class AddFeaturedImageToArticles extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'articles',
            'featuredImage',
            'integer',
            8,
            array(
                'type' => 'integer',
                'length' => 8,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
            )
        );

        $this->addColumn(
            'articles',
            'featuredImageStatus',
            'integer',
            1,
            array(
                'type' => 'integer',
                'length' => 1,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
            )
        );
    }

    public function down()
    {
        $this->removeColumn('articles', 'featuredImage');
        $this->removeColumn('articles', 'featuredImageStatus');
    }
}
