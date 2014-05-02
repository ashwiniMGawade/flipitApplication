<?php
Doctrine_Manager::getInstance()->bindComponent('Chain', 'doctrine');

/**
 * BaseChain
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $name
 * @property ChainItem chainItem
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
abstract class BaseChain extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('chain');
        $this->hasColumn('id', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'comment' => 'PK',
             'length' => '11',
             ));

        $this->hasColumn('name', 'string', 255, array(
                'type' => 'string',
                'unique' => true,
                'length' => '11',
        ));
    }

    public function setUp()
    {
        parent::setUp();


        $this->hasMany('ChainItem  as chainItem', array(
                'local' => 'id',
                'foreign' => 'chainId'));

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
