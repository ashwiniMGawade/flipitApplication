<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Articles', 'doctrine_site');

/**
 * BaseArticles
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $articlecategoryid
 * @property string $title
 * @property string $permalink
 * @property integer $thumbnailid
 * * @property integer $thumbnailsmallid
 * @property string $metatitle
 * @property string $metadescription
 * @property string $content
 * @property integer $publish
 * @property timestamp $publishdate
 * @property integer $deleted
 * @property timestamp $created_at
 * @property timestamp $updated_at
 * @property Image $Image
 * @property Articlecategory $Articlecategory
 * @property Doctrine_Collection $RefArticleStore
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseArticles extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('articles');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
      $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('permalink', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('thumbnailid', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('thumbnailsmallid', 'integer', 8, array(
                'type' => 'integer',
                'length' => 8,
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
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
        $this->hasColumn('content', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('publish', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('publishdate', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('authorid', 'integer', 8, array(
                'type' => 'integer',
                'fixed' => false,
                'unsigned' => false,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
        ));
        $this->hasColumn('authorname', 'string', null, array(
                'type' => 'string',
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
        $this->hasColumn('featuredImage', 'integer', 8, array(
            'type' => 'integer',
            'length' => 8,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('featuredImageStatus', 'integer', 1, array(
            'type' => 'integer',
            'length' => 1,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('plusTitle', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
    }

    public function setUp()
    {
        parent::setUp();

        $this->hasOne('ArticlesIcon as articleImage', array(
             'local' => 'thumbnailid',
             'foreign' => 'id'));


        $this->hasOne(
            'ArticlesThumb as thumbnail',
            array(
                'local' => 'thumbnailsmallid',
                'foreign' => 'id'
            )
        );

        $this->hasOne('ArticlesFeaturedImage as articlefeaturedimage', array(
            'local' => 'featuredImage',
            'foreign' => 'id'));

        $this->hasMany('Shop as shop', array(
                'refClass' => 'RefArticleStore',
                'local' => 'articleid',
                'foreign' => 'storeid'));


       $this->hasMany('RefArticleStore as relatedstores', array(
             'local' => 'id',
             'foreign' => 'articleid'));

        $this->hasMany('RefArticleCategory as relatedcategory', array(
                'local' => 'id',
                'foreign' => 'articleid'));

        $this->hasMany('Articlecategory as articlecategory', array(
                'refClass' => 'RefArticleCategory',
                'local' => 'articleid',
                'foreign' => 'relatedcategoryid'));


        $this->hasMany('RefArticleCategory as refarticlecategory', array(
                'local' => 'id',
                'foreign' => 'articleid'));

        $this->hasMany('ArticleViewCount as articleviewcount', array(
                'local' => 'id',
                'foreign' => 'articleid'));

        $this->hasOne('User as user', array(
                'local' => 'authorid',
                'foreign' => 'id'));

        $this->hasMany('User as users', array(
                'local' => 'authorid',
                'foreign' => 'id'));

        $this->hasMany('ArticleChapter as chapters', array(
                'local' => 'id',
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
