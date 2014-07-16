<?php
class AddColumnAccountSettings extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'signupmaxaccount', 'sendername', 'string', 255 ,
                        array('default' => '', 'notnull' => true ));
        $this->addColumn( 'signupmaxaccount', 'emailsubject', 'string', 255 ,
                array('default' => '', 'notnull' => true ));
        $this->addColumn( 'signupmaxaccount', 'testemail', 'string', 255 ,
                array('default' => '', 'notnull' => true ));
    }

    public function down()
    {
        $this->removeColumn('signupmaxaccount', 'sendername');
        $this->removeColumn('signupmaxaccount', 'emailsubject');
        $this->removeColumn('signupmaxaccount', 'testemail');
    }
}
