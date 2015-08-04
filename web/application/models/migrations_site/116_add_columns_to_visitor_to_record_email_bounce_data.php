<?php
class AddEmailDataColumnsVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('visitor', 'mailClickCount', 'integer', 20);
        $this->addColumn('visitor', 'mailOpenCount', 'integer', 20);
        $this->addColumn('visitor', 'mailHardBounceCount', 'integer', 20);
        $this->addColumn('visitor', 'mailSoftBounceCount', 'integer', 20);
    }

    public function down()
    {
        $this->removeColumn('visitor', 'mailClickCount');
        $this->removeColumn('visitor', 'mailOpenCount');
        $this->removeColumn('visitor', 'mailHardBounceCount');
        $this->removeColumn('visitor', 'mailSoftBounceCount');
    }
}