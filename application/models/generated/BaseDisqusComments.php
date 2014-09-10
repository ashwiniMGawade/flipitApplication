<?php

abstract class BaseDisqusComments extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('disqus_comments');
        $this->hasColumn('id', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '11',
             ));

        $this->hasColumn('comment_id', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20'
        ));
        $this->hasColumn('message', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('page_title', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('page_url', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('author_name', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('author_profile_url', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('author_avtar', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
        $this->hasColumn('created_at', 'timestamp', 12, array(
                'type' => 'timestamp',
                'length' => '12'
        ));
    }

    public function setUp()
    {
        parent::setUp();
    }
}
