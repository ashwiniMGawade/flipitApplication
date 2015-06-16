<?php
class AddColumnEmailSignupmaxaccount extends Doctrine_Migration_Base
{

    public function up()
    {
        $this->addColumn( 'signupmaxaccount', 'emailperlocale', 'text', null ,
                        array('notnull' => true));
    }

    public function down()
    {
        $this->removeColumn( 'signupmaxaccount', 'emailperlocale');
    }

}
