<?php
class removeColumnStatusLocale extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('locale_settings', 'status');
    }

    public function down()
    {
        $this->removeColumn('locale_settings', 'status');
    }
}
