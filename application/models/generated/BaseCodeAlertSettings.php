<?php
//Doctrine_Manager::getInstance()->bindComponent('LocaleSettings', 'doctrine_site');

abstract class BaseCodeAlertSettings extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('code_alert_settings');
        $this->hasColumn('id', 'integer', 10, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20'
             ));
         $this->hasColumn('email_subject', 'string', 50, array(
                'type' => 'string',
                'length' => 50,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
          $this->hasColumn('email_header', 'blob', null, array(
                'type' => 'blob',
        ));
          $this->hasColumn('code_alert_schedule', 'boolean', 1, array(
                'type' => 'boolean',
                'notnull' => false ,
                'default' => 0,
                'length' => 1,
                'comment' => '1-scheduled ,0-manual'
        ));
        $this->hasColumn('code_alert_status', 'boolean', 20, array(
                'type' => 'boolean',
                'notnull' => false ,
                'default' => 0,
                'length' => 1,
                'comment' => '1-sent ,0-unsent'
        ));
        $this->hasColumn('code_alert_schedule_time', 'timestamp', 20, array(
                'default' => date("Y-m-d H:i:s"),
                'type'   => 'timestamp',
                'comment' => 'code alert scheduled timestamp'
        ));

          $this->hasColumn('created_at', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('updated_at', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
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

        $this->actAs($timestampable0);
    }
}
