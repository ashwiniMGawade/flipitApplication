<?php
class AddColumnCategoryTitleColorShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'articlecategory',
            'categorytitlecolor',
            'string',
            null,
            array('notnull' => true)
        );
    }

    public function down()
    {
        $this->removeColumn('articlecategory', 'categorytitlecolor');
    }
}
