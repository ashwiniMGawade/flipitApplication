<?php
class RemoveColumnNewletterStatus extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('signupmaxaccount', 'newletter_status');
    }

    public function down()
    {
        $this->removeColumn('signupmaxaccount', 'newletter_status');
    }
}
