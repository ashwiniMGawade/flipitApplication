<?php

class AddRefreshTimeToVarnish extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('varnish', 'refresh_time', 'timestamp');
    }

    public function down()
    {
        $this->removeColumn('varnish', 'refresh_time');
    }
}
