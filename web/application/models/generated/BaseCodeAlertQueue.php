<?php

abstract class BaseCodeAlertQueue extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('code_alert_queue');
        $this->hasColumn(
            'id',
            'integer',
            10,
            array(
                'primary' => true,
                'type' => 'integer',
                'autoincrement' => true,
                'comment' => 'PK',
                'length' => '20'
            )
        );
        $this->hasColumn(
            'offerId',
            'integer',
            11,
            array(
                'type' => 'integer',
                'length' => 4,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            )
        );
        $this->hasColumn(
            'shopId',
            'integer',
            11,
            array(
                'type' => 'integer',
                'length' => 4,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            )
        );
        $this->hasColumn(
            'created_at',
            'timestamp',
            null,
            array(
                'type' => 'timestamp',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            )
        );
        $this->hasColumn(
            'updated_at',
            'timestamp',
            null,
            array(
                'type' => 'timestamp',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            )
        );
        $this->hasColumn(
            'started',
            'boolean',
            1,
            array(
                'type' => 'boolean',
                'length' => 1,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
                'default' => 0
            )
        );
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
             ));

        $this->actAs($timestampable0);
        $this->actAs($softdelete0);
    }
}
