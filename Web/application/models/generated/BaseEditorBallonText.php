<?php
//Doctrine_Manager::getInstance()->bindComponent('EditorBallonText', 'doctrine');
abstract class BaseEditorBallonText extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('editor_ballon_text');
        $this->hasColumn(
            'id',
            'integer',
            11,
            array(
            'primary' => true,
            'type' => 'integer',
            'autoincrement' => true,
            'comment' => 'PK',
            'length' => '11',
            )
        );

        $this->hasColumn(
            'ballontext',
            'string',
            255,
            array(
            'type' => 'string',
            'length' => '255',
            )
        );
        $this->hasColumn('shopid', 'integer', 11, array(
                'type' => 'integer',
                'length' => '11',
        ));
        $this->hasColumn('deleted', 'integer', 11, array(
                'type' => 'integer',
                'length' => '1',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('shopid  as shopid', array(
                'local' => 'shopid',
                'foreign' => 'id'));

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
