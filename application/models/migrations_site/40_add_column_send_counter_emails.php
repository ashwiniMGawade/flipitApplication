<?php
class AddColumnSendCounterEmails extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn( 'emails', 'send_counter', 'integer', 20 );
    }

    public function down()
    {
        $this->removeColumn('emails', 'send_counter');
    }
}
