<?php
class AddColumnOffersCountPage extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('page', 'offersCount', 'integer', 20);
    }

    public function down()
    {
        $this->removeColumn('page', 'offersCount');
    }
}
