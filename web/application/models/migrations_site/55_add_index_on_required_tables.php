<?php
class AddIndexOnRequiredTables extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex(
            'votes',
            'offer_id',
            array(
                'fields' => array(
                    'offerId' => array()
               )
            )
        );

        $this->addIndex(
            'image',
            'type',
            array(
                'fields' => array(
                    'type' => array()
               )
            )
        );

        $this->addIndex(
            'couponcode',
            'couponcode',
            array(
                'fields' => array(
                    'offerid' => array(),
                    'status' => array()
                )
            )
        );
        
        $this->addIndex(
            'visitor',
            'createdby',
            array(
                'fields' => array(
                    'createdby' => array()
                )
            )
        );

        $this->addIndex(
            'view_count',
            'memberid',
            array(
                'fields' => array(
                    'memberid' => array()
                )
            )
        );

        $this->addIndex(
            'ref_shop_relatedshop',
            'shop_relatedshop',
            array(
                'fields' => array(
                    'shopId' => array(),
                    'relatedshopId' => array()
                )
            )
        );
    }

    public function down()
    {
        $this->removeIndex('votes', 'offer_id');
        $this->removeIndex('image', 'type');
        $this->removeIndex('couponcode', 'couponcode');
        $this->removeIndex('visitor', 'createdby');
        $this->removeIndex('view_count', 'memberid');
        $this->removeIndex('ref_shop_relatedshop', 'shop_relatedshop');
    }
}
