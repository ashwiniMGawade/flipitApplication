<?php
class AddColumnLightboxfirsttextLightboxsecondtextShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'shop',
            'lightboxfirsttext',
            'string',
            255,
            array('notnull' => false)
        );
        $this->addColumn(
            'shop',
            'lightboxsecondtext',
            'string',
            255,
            array('notnull' => false)
        );
    }

    public function down()
    {
        $this->removeColumn('shop', 'lightboxfirsttext');
        $this->removeColumn('shop', 'lightboxsecondtext');
    }
}
