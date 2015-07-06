<?php
class AddExtendedSubidToAffliatenetwork extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('affliate_network', 'extendedSubid', 'string', 512);
    }

    public function down()
    {
        $this->removeColumn('affliate_network', 'extendedSubid');
    }
}