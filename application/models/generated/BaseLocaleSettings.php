<?php
//Doctrine_Manager::getInstance()->bindComponent('LocaleSettings', 'doctrine_site');

abstract class BaseLocaleSettings extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('locale_settings');
        $this->hasColumn('id', 'integer', 10, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20'
             ));
        $this->hasColumn('locale', 'string', 10, array(
                'type' => 'string',
                'length' => '10'
        ));
        $this->hasColumn('timezone', 'string', 255, array(
                'type' => 'string',
                'length' => '255'
        ));
    }

    public function setUp()
    {
        parent::setUp();
    }
}
