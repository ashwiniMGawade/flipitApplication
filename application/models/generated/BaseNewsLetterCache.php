<?php
Doctrine_Manager::getInstance()->bindComponent('NewsLetterCache', 'doctrine_site');
abstract class BaseNewsLetterCache extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('news_letter_cache');
        $this->hasColumn('id', 'integer', null, array(
             'primary' => true,
             'unique' => true,
             'type' => 'integer',
             'autoincrement' => true,
        ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
        ));
        $this->hasColumn('value', 'blob', null, array(
            'type' => 'string'
        ));
        $this->hasColumn('status', 'boolean', null, array(
            'type' => 'boolean',
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
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             'hardDeleted' => true,
             ));
        $this->actAs($timestampable0);
        $this->actAs($softdelete0);
    }
}
