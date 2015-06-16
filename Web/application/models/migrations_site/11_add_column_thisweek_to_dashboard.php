<?php
class AddColumnThisWeekFieldDashboard extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'dashboard', 'total_no_of_shops_online_code_thisweek', 'integer', null ,
                        array('default' => 0 ,
                              'notnull' => true	));
    }

    public function down()
    {
        $this->removeColumn('dashboard', 'total_no_of_shops_online_code_thisweek');
    }
}
