<?php
class UpdateEmailColumnsVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('visitor', 'mailClickCount');
        $this->removeColumn('visitor', 'mailOpenCount');
        $this->removeColumn('visitor', 'mailHardBounceCount');
        $this->removeColumn('visitor', 'mailSoftBounceCount');

        $this->addColumn('visitor', 'mailClickCount', 'integer', 10, array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'mailOpenCount', 'integer', 10, array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'mailHardBounceCount', 'integer', 10, array('notnull' => 1, 'default' => 0));
        $this->addColumn('visitor', 'mailSoftBounceCount', 'integer', 10, array('notnull' => 1, 'default' => 0));
    }

    public function down()
    {
        $this->removeColumn('visitor', 'mailClickCount');
        $this->removeColumn('visitor', 'mailOpenCount');
        $this->removeColumn('visitor', 'mailHardBounceCount');
        $this->removeColumn('visitor', 'mailSoftBounceCount');

        $this->addColumn('visitor', 'mailClickCount', 'integer', 20);
        $this->addColumn('visitor', 'mailOpenCount', 'integer', 20);
        $this->addColumn('visitor', 'mailHardBounceCount', 'integer', 20);
        $this->addColumn('visitor', 'mailSoftBounceCount', 'integer', 20);
    }
}
