<?php
Doctrine_Manager::getInstance()->bindComponent('Translations', 'doctrine_site');

abstract class BaseTranslations extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('translations');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20'
             ));
        $this->hasColumn('translationKey', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false
        ));
        $this->hasColumn('translation', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean'
             ));
        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' =>
             array(
              'name' => 'created_at'
             ),
             'updated' =>
             array(
              'name' => 'updated_at'
             ),
             ));
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}
