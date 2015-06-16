<?php
class AddColumnExtendedOfferTitle extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'offer',
            'extendedoffertitle',
            'string',
            255,
            array('default' => '', 'notnull' => true)
        );
    }

    public function down()
    {
        $this->removeColumn('offer', 'extendedoffertitle');
    }
}
