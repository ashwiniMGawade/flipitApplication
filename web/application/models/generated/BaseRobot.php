<?php
Doctrine_Manager::getInstance()->bindComponent('Robot', 'doctrine');

abstract class BaseRobot extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('robot');
        $this->hasColumn('id', 'integer', 6, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '6'
        ));
        $this->hasColumn('website', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));
        $this->hasColumn('content', 'blob', 11, array(
                'type' => 'blob'
        ));
        $this->hasColumn('deleted', 'integer', 11, array(
                'type' => 'integer',
                'length' => '1',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable = new Doctrine_Template_Timestampable(array(
             'created' =>
             array(
              'name' => 'created_at',
             ),
             'updated' =>
             array(
              'name' => 'updated_at',
             ),
        ));
        $this->actAs($timestampable);
    }
}
