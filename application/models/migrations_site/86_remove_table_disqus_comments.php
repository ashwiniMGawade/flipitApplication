<?php
class RemoveTableDisqusComments extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->dropTable('disqus_comments');
    }

    public function down()
    {
        $this->dropTable('disqus_comments');
    }
}