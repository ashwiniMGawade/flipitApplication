<?php
class AddOfferDescriptionInOffer extends Doctrine_Migration_Base
{
    public function up()
    {
         $this->removeColumn('offer', 'offerDescription');
    }
}
