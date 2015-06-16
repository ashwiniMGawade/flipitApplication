<?php
class AlterConstraintChainItem extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeIndex( 'chain_item', 'unique_shopname_website_chain');
        $definition = array(
                'fields' => array(
                        'shopName' => array(),
                        'websiteid' => array()
                ),
                'unique' => true
        );
        $this->createConstraint( 'chain_item', 'unique_shopname_website', $definition );

    }

    public function down()
    {
        $this->removeIndex('chain_item', 'unique_shopname_website');

        $definition = array(
                'fields' => array(
                        'shopName' => array(),
                        'websiteid' => array(),
                        'chainid' => array()
                ),
                'unique' => true
        );

        $this->createConstraint( 'chain_item', 'unique_shopname_website_chain', $definition );


    }
}
