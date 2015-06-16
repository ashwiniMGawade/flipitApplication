<?php
class AddCustomHeaderPage extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'page', 'customheader', 'string', 1024 ,
                array('notnull' => false ));
    }

    public function down()
    {
        $this->removeColumn('page', 'customheader');
    }
}
