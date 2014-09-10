<?php
class RemoveIndexOnDisqusCommentsTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeIndex(
            'disqus_comments',
            'message_comments',
            array(
                'fields' => array(
                    'message' => array()
                )
            )
        );
    }
    public function down()
    {

    }
}