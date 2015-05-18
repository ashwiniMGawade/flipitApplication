<?php
Doctrine_Manager::getInstance()->bindComponent('PageWidgets', 'doctrine_site');
abstract class BasePageWidgets extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('page_widgets');
        $this->hasColumn('id', 'integer', 11, array(
            'primary' => true,
            'type' => 'integer',
            'autoincrement' => true,
            'comment' => 'PK',
            'length' => '11'
        ));
        $this->hasColumn('widget_type', 'string', 255, array(
            'type' => 'string',
            'length' => '255'
        ));
        $this->hasColumn('position', 'integer', 11, array(
            'type' => 'integer',
            'length' => '11'
        ));
        $this->hasColumn('widgetId', 'integer', 11, array(
            'type' => 'integer',
            'length' => '11'
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Widget as widget', array('local' => 'widgetId', 'foreign' => 'id'));
        $timestampable0 = new Doctrine_Template_Timestampable(
            array(
                'created' =>
                    array(
                        'name' => 'created_at',
                    ),
                'updated' =>
                    array(
                        'name' => 'updated_at',
                    ),
            )
        );
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             ));
        $this->actAs($timestampable0);
        $this->actAs($softdelete0);
    }
}
