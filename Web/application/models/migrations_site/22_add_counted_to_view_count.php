<?php
class AddCountedToViewCount extends Doctrine_Migration_Base
{
    public function up()
    {

        $this->addColumn( 'view_count', 'counted', 'boolean',null, array(
                'type' => 'boolean',
                'default' => 0 ,
        ));



    }

    public function down()
    {
        $this->removeColumn('view_count', 'counted');
    }
}
