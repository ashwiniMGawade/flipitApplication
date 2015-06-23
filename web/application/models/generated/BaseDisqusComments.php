<?php
abstract class BaseDisqusComments extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('disqus_comments');
        $this->hasColumn('id', 'integer', 11, array(
            'type' => 'integer',
            'length' => '11'
        ));
        $this->hasColumn('thread_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => '11'
        ));
        $this->hasColumn('author_name', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('comment', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('created', 'integer', 11, array(
            'type' => 'integer',
            'length' => '11'
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne(
            'DisqusThread as thread',
            array(
                'local' => 'thread_id',
                'foreign' => 'id'
            )
        );
    }
}
