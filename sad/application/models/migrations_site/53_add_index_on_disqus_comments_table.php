<?php
class AddIndexOnDisqusCommentsTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex(
            'disqus_comments',
            'page_url_comments',
            array(
                'fields' => array(
                    'page_url' => array()
                )
            )
        );

        $this->addIndex(
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
        $this->removeIndex('disqus_comments', 'page_url_comments');
        $this->removeIndex('disqus_comments', 'message_comments');
    }
}
