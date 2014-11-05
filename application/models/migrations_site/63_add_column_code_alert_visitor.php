<?php

class AddColumnCodeAlertVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('visitor', 'codeAlert', 'integer', 20);
    }

    public function down()
    {
        $this->removeColumn('visitor', 'codeAlert');
    }
}
