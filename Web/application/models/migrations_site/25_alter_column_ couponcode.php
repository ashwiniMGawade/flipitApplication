<?php
class AlterColumnCouponCode extends Doctrine_Migration_Base
{


    public function up()
    {

        $this->changeColumn( 'couponcode', 'code', 'string', 255  );
    }

    public function down()
    {
        $this->changeColumn( 'couponcode', 'code', 'string', 10 );
    }


}
