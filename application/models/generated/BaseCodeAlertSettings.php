<?php

abstract class BaseCodeAlertSettings extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('code_alert_settings');
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
            'email_subject',
            'string',
            50,
            array(
                'type' => 'string',
                'length' => 50,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            )
        );
        $this->hasColumn(
            'email_header',
            'blob',
            null,
            array(
                'type' => 'blob',
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
