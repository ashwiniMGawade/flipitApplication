<?php
class AddColumnPopularKortingscodeUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'user', 'popularkortingscode', 'integer', 10 );
    }

    public function down()
    {
        $this->removeColumn('user', 'popularkortingscode');
    }
}
