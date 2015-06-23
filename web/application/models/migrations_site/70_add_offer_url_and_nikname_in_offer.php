<?php
class AddOfferUrlAndNiknameInOffer extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'offer',
            'offerUrl',
            'string',
            500,
            array('notnull' => false)
        );
        $this->addColumn(
            'offer',
            'nickname',
            'string',
            255,
            array('notnull' => false)
        );
    }

    public function down()
    {
        $this->removeColumn('offer', 'offerUrl');
        $this->removeColumn('offer', 'nickname');
    }
}
