<?php
class AddColumnChainWebsite extends Doctrine_Migration_Base
{

    public function up()
    {
        $this->addColumn('website', 'chain', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('website', 'chain');
    }

}
