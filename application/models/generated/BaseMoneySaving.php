<?php
Doctrine_Manager::getInstance()->bindComponent('MoneySaving', 'doctrine_site');

 /**
 * @author Raman
 * @version 1.0
 *
 * @property integer $id
 * @property integer $pageid
 * @property integer $categoryid
 * @property integer $deleted
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
abstract class BaseMoneySaving extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('moneysaving');
        $this->hasColumn('id', 'integer', 20, array(
             'type' => 'integer',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
      $this->hasColumn('pageid', 'integer', 20, array(
             'type' => 'interger',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('categoryid', 'integer', 20, array(
             'type' => 'integer',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));

        $this->hasColumn('deleted', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
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

        $this->hasOne('Page as page', array(
                'local' => 'pageid',
                'foreign' => 'id'));

        $this->hasMany('Articlecategory as articlecategory', array(
                'local' => 'categoryid',
                'foreign' => 'id'));

        $this->hasMany('Page as page', array(
                'local' => 'pageid',
                'foreign' => 'id'));


        $this->hasMany('RefArticleCategory as refarticlecategory', array(
                'local' => 'categoryid',
                'foreign' => 'relatedcategoryid'));


        $softdelete0 = new Doctrine_Template_SoftDelete(array(
                'name' => 'deleted',
                'type' => 'boolean',
        ));

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
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}
