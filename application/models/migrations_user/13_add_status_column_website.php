<?php
class AddStatusColumnWebsite extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('website', 'status', 'string', 10, array(
            'type' => 'string',
            'length' => '10',
            ));
    }

    public function down()
    {
        $this->removeColumn('website', 'status');
    }
}
