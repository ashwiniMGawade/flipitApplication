<?php
class AddColumnTotalOffersAndTotalCouponsPopularCategory extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'popular_category',
            'total_offers',
            'integer',
            null,
            array(
                'default' => 0 ,
                'notnull' => true
            )
        );
        $this->addColumn(
            'popular_category',
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
        $this->removeColumn('popular_category', 'total_offers');
        $this->removeColumn('popular_category', 'total_coupons');
    }
}