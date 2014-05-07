<?php
class AddCountryColumnUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('user', 'countryName', 'string', 100);
    }

    public function down()
    {
        $this->removeColumn('user', 'countryName');
    }
}
