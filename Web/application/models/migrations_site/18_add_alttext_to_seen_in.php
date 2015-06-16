<?php
class AddAlttextToSeenIn extends Doctrine_Migration_Base
{
    public function up()
    {

        $this->addColumn( 'seen_in', 'alttext', 'string', 256 );

    }

    public function down()
    {
        $this->removeColumn('seen_in', 'alttext');

    }
}
