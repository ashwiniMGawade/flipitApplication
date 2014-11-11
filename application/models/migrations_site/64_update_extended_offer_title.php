<?php
class UpdateExtendedOfferTitle extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn( 'offer', 'extendedoffertitle', 'string', array('notnull' => false));
    }

    public function down()
    {
        $this->changeColumn( 'offer', 'extendedoffertitle', 'string', array('notnull' => true));
    }
}