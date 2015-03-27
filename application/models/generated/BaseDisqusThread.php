<?php
abstract class BaseDisqusThread extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('disqus_thread');
        $this->hasColumn(
            'id',
            'integer',
            11,
            array(
                'type' => 'integer',
                'length' => '11'
            )
        );
        $this->hasColumn('title', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('link', 'string', 255, array(
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
    }
}
