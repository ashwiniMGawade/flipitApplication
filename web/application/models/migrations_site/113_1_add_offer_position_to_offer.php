<?php

class AddOfferPositionToOffer extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('offer', 'offer_position', 'integer', 11);
    }

    public function down()
    {
        $this->removeColumn('offer', 'offer_position', 11);
    }
}