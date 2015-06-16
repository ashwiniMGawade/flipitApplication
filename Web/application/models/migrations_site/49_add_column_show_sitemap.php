<?php
class AddColumnShowSitemap extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'page',
            'showsitemap',
            'int',
            1,
            array(
                'default' => '0',
                'notnull' => true
            )
        );
    }

    public function down()
    {
        $this->removeColumn('page', 'showsitemap');
    }
}
