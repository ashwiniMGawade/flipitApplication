<?php
class AddColumnVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'visitor', 'locale', 'string', 5 ,
                        array('default' => '', 'notnull' => true ));
    }

    public function down()
    {
        $this->removeColumn('visitor', 'locale');
    }
}
