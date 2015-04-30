<?php
class AddColumnTotalOffersAndTotalCouponsSpecialList extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'special_list',
            'total_offers',
            'integer',
            null,
            array(
                'default' => 0 ,
                'notnull' => true
            )
        );
        $this->addColumn(
            'special_list',
            'total_coupons',
            'integer',
            null,
            array(
                'default' => 0 ,
                'notnull' => true
            )
        );
    }

    public function down()
    {
        $this->removeColumn('special_list', 'total_offers');
        $this->removeColumn('special_list', 'total_coupons');
    }
}