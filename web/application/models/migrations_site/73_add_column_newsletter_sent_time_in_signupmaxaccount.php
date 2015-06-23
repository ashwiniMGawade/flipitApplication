<?php
class AddColumnNewsletterSentTimeInSignupmaxaccount extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn(
            'signupmaxaccount',
            'newsletter_sent_time',
            'timestamp',
            array(
                'type' => 'timestamp',
                'length' => 12
            )
        );
    }
    public function down()
    {
        $this->removeColumn('signupmaxaccount', 'newsletter_sent_time');
    }
}
