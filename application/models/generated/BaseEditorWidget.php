<?php

/**
 * BaseEditorWidget
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property boolean $status
 * @property string $code
 * @property integer $offerid
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseEditorWidget extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('editorwidget');
        $this->hasColumn(
            'id',
            'integer',
            20,
            array(
                'primary' => true,
                'type' => 'integer',
                'autoincrement' => true,
                'comment' => 'PK',
                'length' => '20'
            )
        );
        $this->hasColumn(
            'type',
            'string',
            10,
            array(
                'type' => 'string',
                'length' => '10'
            )
        );
        $this->hasColumn(
            'description',
            'string',
            10,
            array(
                'type' => 'string',
                'length' => '1024'
            )
        );
        $this->hasColumn(
            'subtitle',
            'string',
            10,
            array(
                'type' => 'string',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'editorId',
            'integer',
            10,
            array(
                'type' => 'integer',
                'length' => '10'
            )
        );
        $this->hasColumn(
            'status',
            'boolean',
            1,
            array(
                'type' => 'boolean',
                'length' => '1',
                'default' => 1,
                'comment' => '1-on, 0-off'
            )
        );
    }

    public function setUp()
    {
        parent::setUp();
        $softdelete = new Doctrine_Template_SoftDelete(array(
            'name' => 'status',
            'type' => 'boolean',
            'options' =>
            array(
                'default' => 0
            ),
        ));
        $timestampable = new Doctrine_Template_Timestampable(array(
            'created' =>
            array(
              'name' => 'created_at'
            ),
            'updated' =>
            array(
                'name' => 'updated_at'
            ),
        ));
        $this->actAs($softdelete);
        $this->actAs($timestampable);
    }
}
