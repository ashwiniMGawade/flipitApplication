<?php
class AddTimezoneColumnAccountSettings  extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'signupmaxaccount', 'timezone', 'string', 255	);
        $this->addColumn( 'signupmaxaccount', 'newletter_is_scheduled', 'boolean', 1 ,
                array('notnull' => false ,
                        'default' => 0,
                        'length' => 1,
                        'comment' => '1-scheduled ,0-manual'));

        $this->addColumn( 'signupmaxaccount', 'newletter_status', 'boolean', 1 ,
                array('notnull' => false ,
                        'default' => 0,
                        'length' => 1,
                        'comment' => '1-sent ,0-unsent, this is only used in case of scheduled newsletters'));

        $this->addColumn( 'signupmaxaccount', 'newletter_scheduled_time', 'timestamp', 20 ,
                array('default' => date("Y-m-d H:i:s"),
                        'type'   => 'timestamp',
                        'comment' => 'newsletter scheduled timestamp'));
    }

    public function down()
    {
        $this->removeColumn( 'signupmaxaccount', 'timezone');
        $this->removeColumn( 'signupmaxaccount', 'newletter_is_scheduled');
        $this->removeColumn( 'signupmaxaccount', 'newletter_status');
        $this->removeColumn( 'signupmaxaccount', 'newletter_scheduled_time');
    }
}
