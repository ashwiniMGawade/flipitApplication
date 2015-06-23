<?php
abstract class BaseShopReasons extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('shopreasons');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('fieldname', 'string', 100, array(
                'type' => 'string',
                'length' => '100',
        ));
        $this->hasColumn('fieldvalue', 'string', 512, array(
                'type' => 'string',
                'length' => '512',
        ));
        $this->hasColumn('shopid', 'integer', 20, array(
                'type' => 'integer',
                'length' => '20',
        ));
        $this->hasColumn('deleted', 'boolean', 11, array(
                'type' => 'boolean',
                'length' => '1',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne(
            'Shop  as shops',
            array(
              'local' => 'shopid',
              'foreign' => 'id'
            )
        );
        $timestampable = new Doctrine_Template_Timestampable(
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
        $this->actAs($timestampable);
    }
}
