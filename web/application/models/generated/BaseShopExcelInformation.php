<?php
abstract class BaseShopExcelInformation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('shopExcelInformation');
        $this->hasColumn(
            'id',
            'integer',
            11,
            array(
                'primary' => true,
                'type' => 'integer',
                'autoincrement' => true,
                'comment' => 'PK',
                'length' => '11'
            )
        );
        $this->hasColumn(
            'totalShopsCount',
            'integer',
            11,
            array(
                'type' => 'integer',
                'length' => '11'
            )
        );
        $this->hasColumn(
            'passCount',
            'integer',
            11,
            array(
                'type' => 'integer',
                'length' => '11'
            )
        );
        $this->hasColumn(
            'failCount',
            'integer',
            11,
            array(
                'type' => 'integer',
                'length' => '11'
            )
        );
        $this->hasColumn(
            'userName',
            'string',
            255,
            array(
                'type' => 'string',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'filename',
            'string',
            255,
            array(
                'type' => 'string',
                'length' => '255'
            )
        );
        $this->hasColumn(
            'deleted',
            'boolean',
            1,
            array(
                'type' => 'boolean',
                'length' => '1',
                'default'=> 0
            )
        );
    }

    public function setUp()
    {
        parent::setUp();
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
