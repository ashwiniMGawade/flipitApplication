<?php
class AddCouponCodeTypeToViewCount extends Doctrine_Migration_Base
{
    public function up()
    {

        $this->addColumn( 'offer', 'couponcodetype','enum', null, array(
                'type' => 'enum',
                'default' => 'GN',
                'values' =>
                array(
                        0 => 'GN',
                        1 => 'UN'
                ),
                'comment' => 'GN-general ,UN-unique',
        ));



    }

    public function down()
    {
        $this->removeColumn( 'offer', 'couponcodetype');
    }
}
