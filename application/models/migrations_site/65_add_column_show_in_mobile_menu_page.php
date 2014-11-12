<?php
class AddColumnShowInMobileMenu extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'page',
            'showinmobilemenu',
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
        $this->removeColumn('page', 'showinmobilemenu');
    }
}
