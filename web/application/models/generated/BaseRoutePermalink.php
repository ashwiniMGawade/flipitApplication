<?php
//Doctrine_Manager::getInstance()->bindComponent('RoutePermalink', 'doctrine_site');

/**
 * BaseRoutePermalink
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $title
 * @property blob $content
 * @property boolean $status
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##cbhopal## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseRoutePermalink extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('route_permalink');
        $this->hasColumn('id', 'integer', 20, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '20',
             ));

        $this->hasColumn('permalink', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));

        $this->hasColumn('type', 'string', 255, array(
                'type' => 'string',
                'length' => '255',

        ));

        $this->hasColumn('exactlink', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
        ));


    }

    public function setUp()
    {
        parent::setUp();

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