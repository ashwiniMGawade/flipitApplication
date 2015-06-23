<?php
class AddIndexOnConversionTable  extends Doctrine_Migration_Base
{
    public function up()
    {
        # create index on all table which don't have index on foreign key column

        $this->addIndex( 'conversions', 'offer_conversion',
                    array(
                        'fields' => array(
                            'offerId' => array(),
                            'converted' => array(),
                            'IP' => array()
                    ))
        );

        $this->addIndex( 'conversions', 'shop_conversion',
                    array(
                        'fields' => array(
                            'shopId' => array(),
                            'converted' => array(),
                            'IP' => array(),

                    ))
        );


    }
    public function down()
    {
        $this->removeIndex( 'conversions', 'shop_conversion');
        $this->removeIndex( 'conversions', 'offer_conversion');
    }
}
