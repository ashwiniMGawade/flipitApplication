<?php
class AddColumnFunctionnameToWidget extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('widget', 'function_name', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('widget', 'function_name');
    }
}
