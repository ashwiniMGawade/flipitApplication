<?php
Doctrine_Manager::getInstance()->bindComponent('Splash', 'doctrine');

abstract class BaseSplash extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('splash');
        $this->hasColumn('id', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '11',
             ));

        $this->hasColumn('locale', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));
        $this->hasColumn('offerId', 'integer', 11, array(
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
        $this->hasOne('offerId  as offerId', array(
                'local' => 'offerId',
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
