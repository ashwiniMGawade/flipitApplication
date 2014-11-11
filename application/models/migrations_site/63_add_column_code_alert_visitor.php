<?php

class AddColumnCodeAlertVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('visitor', 'codealert', 'integer', 20);
    }

    public function down()
    {
        $this->removeColumn('visitor', 'codealert');
    }
}
