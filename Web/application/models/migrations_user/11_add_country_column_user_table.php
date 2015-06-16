<?php
class AddCountryColumnUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('user', 'countryLocale', 'string', 10);
    }

    public function down()
    {
        $this->removeColumn('user', 'countryLocale');
    }
}
