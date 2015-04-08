<?php
class DisqusThreadTable extends Doctrine_Table
{
    public static function getInstance()
    {
        return Doctrine_Core::getTable('disqus_thread');
    }
}