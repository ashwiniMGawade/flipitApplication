<?php
class AddHomepageWidgetBannerColumnAccountSettings  extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'signupmaxaccount', 'homepage_widget_banner_name', 'string', 255 ,
                array('notnull' => false ));

        $this->addColumn( 'signupmaxaccount', 'homepage_widget_banner_path', 'string', 255 ,
                array('notnull' => false ));


    }

    public function down()
    {
        $this->removeColumn( 'signupmaxaccount', 'homepage_widget_banner_name');
        $this->removeColumn( 'signupmaxaccount', 'homepage_widget_banner_path');

    }
}
