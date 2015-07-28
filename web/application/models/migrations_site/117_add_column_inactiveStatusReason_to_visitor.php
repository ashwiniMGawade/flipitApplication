<?php
class AddInactiveStatusReasonColumnVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('visitor', 'inactiveStatusReason', 'varchar', 20);
    }

    public function down()
    {
        $this->removeColumn('visitor', 'inactiveStatusReason');
    }
}
