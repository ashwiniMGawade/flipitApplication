<?php
class AddColumnBrandingCssToShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'shop', 'brandingcss', 'text', null );
    }

    public function down()
    {
        $this->removeColumn('shop', 'brandingcss');
    }
}
