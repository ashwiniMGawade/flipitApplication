<?php
class AddColumnVote extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'votes', 'visitorid', 'integer', 20 );
    }

    public function down()
    {
        $this->removeColumn('votes', 'visitorid');
    }
}
