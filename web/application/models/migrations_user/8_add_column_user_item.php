<?php
class AddColumnToUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'user', 'passwordchangetime', 'timestamp', 20,
                array('default' => date("Y-m-d H:i:s")) );
    }

    public function down()
    {
        $this->removeColumn('user', 'passwordchangetime');
    }
}
