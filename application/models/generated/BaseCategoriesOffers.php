<?php
abstract class BaseCategoriesOffers extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('categories_offers');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('position', 'integer', null, array(
             'type' => 'integer',
             'comment' => 'Holds the article position among special list offer',
             ));
        $this->hasColumn('offerId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to offer.id',
             'length' => '20',
        ));

        $this->hasColumn('categoryId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to categroy.id',
             'length' => '20',
        ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne(
            'Offer as offers',
            array(
             'local' => 'offerId',
             'foreign' => 'id'
            )
        );

        $this->hasOne(
            'Category as categories',
            array(
             'local' => 'categoryId',
             'foreign' => 'id'
            )
        );

        $softdelete0 = new Doctrine_Template_SoftDelete(
            array(
             'name' => 'deleted',
             'type' => 'boolean',
             )
        );
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
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}
