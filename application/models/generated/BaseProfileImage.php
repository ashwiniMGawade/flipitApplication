<?php
Doctrine_Manager::getInstance()->bindComponent('ProfileImage', 'doctrine');
/**
 * BaseProfileImage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $ext
 * @property string $path
 * @property string $name
 * @property Doctrine_Collection $user
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseProfileImage extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('profile_image');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('ext', 'string', 5, array(
             'type' => 'string',
             'length' => '5',
             ));
        $this->hasColumn('path', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('User as user', array(
             'local' => 'id',
             'foreign' => 'profileImageId'));

        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             'type' => 'boolean',
             'options' => 
             array(
              'default' => 0,
             ),
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