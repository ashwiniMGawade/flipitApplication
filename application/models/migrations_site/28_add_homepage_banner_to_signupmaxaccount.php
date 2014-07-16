<?php
class AddhomepageBannerColumnAccountSettings  extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'signupmaxaccount', 'homepagebanner_name', 'string', 255 ,
                array('notnull' => false ));

        $this->addColumn( 'signupmaxaccount', 'homepagebanner_path', 'string', 255 ,
                array('notnull' => false ));


    }

    public function down()
    {
        $this->removeColumn( 'signupmaxaccount', 'homepagebanner_name');
        $this->removeColumn( 'signupmaxaccount', 'homepagebanner_path');

    }
}
