<?php
class AlterColumnMessageDisqusComment extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('disqus_comments', 'message', 'string', 512);
    }

    public function down()
    {
        $this->changeColumn('disqus_comments', 'message', 'string', 256);
    }
}