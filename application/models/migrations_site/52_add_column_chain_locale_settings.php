<?php
class AddColumnChainLocaleSettings extends Doctrine_Migration_Base
{

    public function up()
    {
        $this->addColumn('locale_settings', 'chain', 'string', 255);
    }

    public function down()
    {
        $this->removeColumn('locale_settings', 'chain');
    }

}
