<?php
class AddEmailColumnsVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('visitor', 'mailClickCount', 'integer', '', array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'mailOpenCount', 'integer', '', array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'mailHardBounceCount', 'integer', '', array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'mailSoftBounceCount', 'integer', '', array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'inactiveStatusReason', 'varchar', 20);
        $this->addColumn('visitor', 'lastEmailOpenDate', 'timestamp');
    }

    public function down()
    {
        $this->removeColumn('visitor', 'mailClickCount');
        $this->removeColumn('visitor', 'mailOpenCount');
        $this->removeColumn('visitor', 'mailHardBounceCount');
        $this->removeColumn('visitor', 'mailSoftBounceCount');
        $this->removeColumn('visitor', 'inactiveStatusReason');
        $this->removeColumn('visitor', 'lastEmailOpenDate');
    }
}
