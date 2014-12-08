<?php
class AddOfferDescriptionInOffer extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'offer',
            'offerDescription',
            'string',
            1000,
            array('notnull' => false)
        );
    }

    public function down()
    {
        $this->removeColumn('offer', 'offerDescription');
    }
}
