<?php
class AddColumnCustomTextShop extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'shop',
            'customtext',
            'blob',
            null
        );
    }

    public function down()
    {
        $this->removeColumn('shop', 'customtext');
    }
}
