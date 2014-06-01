<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Articlecategory', 'doctrine_site');

/**
 * BaseArticlecategory
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $name
 * @property string $permalink
 * @property string $metadescription
 * @property blob $description
 * @property integer $status
 * @property integer $categoryiconid
 * @property integer $deleted
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property Image $Image
 * @property Doctrine_Collection $Articles
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseArticlecategory extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('articlecategory');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('permalink', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('metatitle', 'string', null, array(
                'type' => 'string',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => false,
                'autoincrement' => false,
        ));
        $this->hasColumn('metadescription', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('description', 'blob', null, array(
             'type' => 'blob',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('status', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('categoryiconid', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('deleted', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
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
        $this->hasOne('ArticleCategoryIcon as ArtCatIcon', array(
             'local' => 'categoryiconid',
             'foreign' => 'id'));

        $this->hasMany('Articles', array(
             'local' => 'id',
             'foreign' => 'articlecategoryid'));

        $this->hasMany('MoneySaving as moneysaving', array(
                'local' => 'id',
                'foreign' => 'articlecategoryid'));


        $this->hasMany('Category as relatedcategory', array(
                'refClass' => 'RefArticlecategoryRelatedcategory',
                'local' => 'articlecategoryid',
                'foreign' => 'relatedcategoryid'));

        $this->hasMany('Articles as articles', array(
                'refClass' => 'RefArticleCategory',
                'local' => 'relatedcategoryid',
                'foreign' => 'articleid'));


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
