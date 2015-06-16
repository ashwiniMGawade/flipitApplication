<?php
//Doctrine_Manager::getInstance()->bindComponent('Varnish', 'doctrine_site');

abstract class BaseVarnish extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('varnish');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('url', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('status', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('refresh_time', 'timestamp', null, array(
            'type' => 'timestamp',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' =>
             array(
              'name' => 'created_at',
             ),
             'updated' =>
             array(
              'name' => 'updated_at',
             ),
             ));
        $this->actAs($timestampable0);
    }
}
