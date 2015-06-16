<?php
class AddColumnAddSearchUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'user', 'addtosearch', 'boolean', null ,
                        array('default' => 0 ,
                              'notnull' => true	));
    }

    public function down()
    {
        $this->removeColumn('user', 'addtosearch');
    }
}
