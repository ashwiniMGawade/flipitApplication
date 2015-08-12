<?php
class AddLastEmailOpenDateColumnVisitor extends Doctrine_Migration_Base
{
    public function up()
    {
        $currentTime = time();
        $this->addColumn(
            'visitor',
            'lastEmailOpenDate',
            'string',
            255,
            array(
                'notnull'  => 1,
                'default'  => $currentTime
            )
        );
    }

    public function down()
    {
        $this->removeColumn('visitor', 'lastEmailOpenDate');
    }
}
