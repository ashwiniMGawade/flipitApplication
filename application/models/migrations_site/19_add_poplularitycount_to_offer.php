<?php
class AddPopularitycountToOffer extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'offer', 'popularitycount', 'decimal', 16, array(
               'type' => 'decimal',
               'length' => '16',
               'scale' => 4
       ));
    }

    public function down()
    {
        $this->removeColumn('offer', 'popularitycount');
    }
}
