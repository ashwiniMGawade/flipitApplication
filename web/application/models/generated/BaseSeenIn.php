<?php
Doctrine_Manager::getInstance()->bindComponent('SeenIn', 'doctrine_site');

/**
 * BaseSeenIn
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $toolltip
 * @property boolean $status
 * @property integer $logoId
 * @property string $altText
 * @property Logo $logo
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##Er.kundal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseSeenIn extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('seen_in');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));
        $this->hasColumn('name', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('url', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('altText', 'string', 255, array(
                'type' => 'string',
                'length' => '255',
        ));
        $this->hasColumn('toolltip', 'string', 1024, array(
             'type' => 'string',
             'length' => '1024',
             ));
        $this->hasColumn('status', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'comment' => '1 – enable , 0 – disable',
             ));
        $this->hasColumn('logoId', 'integer', 20, array(
             'unique' => true,
             'type' => 'integer',
             'comment' => 'FK to image.id',
             'length' => '20',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Logo as logo', array(
             'local' => 'logoId',
             'foreign' => 'id'));

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
