<?php
class AddChainManagementTables extends Doctrine_Migration_Base
{
    public function up()
    {

        $options = array(
                'type'    => 'INNODB',
                'charset' => 'utf8'
        );


        # create chain table elements
        $columns = array(
                'id' => array(
                        'type'     => 'integer',
                        'length'   => 10,
                        'primary'  => 1,
                        'autoincrement' => 1,
                        'notnull'  => 1
                ),
                'name' =>  array(
                        'type'   => 'string',
                        'unique' => true,
                        'length' => 255

                ),
                'created_at' => array(
                        'type'   => 'timestamp',
                        'length' => 12
                ),
                'updated_at' => array(
                        'type'   => 'timestamp'
                )

        );

        $this->createTable( 'chain', $columns,$options );

        $columns = array(
                'id' => array(
                        'type'     => 'integer',
                        'length'   => 10,
                        'primary'  => 1,
                        'autoincrement' => 1,
                        'notnull'  => 1
                ),
                'shopname' =>  array(
                        'type'   => 'string',
                        'length' => 255

                ),
                'websiteid' =>  array(
                        'type'   => 'integer',
                        'length' => 5

                ),
                'chainid' =>  array(
                        'type'   => 'integer',
                        'length' => 10

                ),
                'created_at' => array(
                        'type'   => 'timestamp',
                        'length' => 12
                ),
                'updated_at' => array(
                        'type'   => 'timestamp'
                )

        );

        $this->createTable( 'chain_item', $columns,$options );
        $this->addIndex('chain_item', 'unique_shopname_website_chain', array(
                'fields' => array(
                        'shopName' => array(),
                        'websiteid' => array(),
                        'chainid' => array()
                ),
                'unique' => true
        ));

        $chain = array(
                'local'        => 'chainid',
                'foreign'      => 'id',
                'foreignTable' => 'chain',
                'onDelete'     => 'CASCADE',
        );

        $this->createForeignKey( 'chain_item', 'ref_chain_items', $chain );


        $website = array(
                'local'        => 'websiteid',
                'foreign'      => 'id',
                'foreignTable' => 'website',
                'onDelete'     => 'CASCADE',
        );

        $this->createForeignKey( 'chain_item', 'ref_chain_website', $website );

    }

    public function down()
    {
        $this->dropForeignKey( 'chain_item', 'ref_chain_items' );
        $this->dropForeignKey( 'chain_item', 'ref_chain_website' );


        $this->dropTable( 'chain');
        $this->dropTable( 'chain_item');
    }
}
