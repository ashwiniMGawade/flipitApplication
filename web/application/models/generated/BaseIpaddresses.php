<?php
Doctrine_Manager::getInstance()->bindComponent('Ipaddresses', 'doctrine');

abstract class BaseIpaddresses extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ip_addresses');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));

        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string'
        ));
        $this->hasColumn('ipaddress', 'string', 255, array(
            'type' => 'string'
        ));
    }

    public function setUp()
    {
        parent::setUp();

        $softdelete0 = new Doctrine_Template_SoftDelete(array(
                'name' => 'deleted',
                'type' => 'boolean',
        ));
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
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}
