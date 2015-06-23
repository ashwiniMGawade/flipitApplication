<?php

class AddColumnUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'user', 'showInAboutListing', 'boolean', 1 );

    }

    public function down()
    {
        $this->removeColumn('user', 'showInAboutListing');
    }
}
