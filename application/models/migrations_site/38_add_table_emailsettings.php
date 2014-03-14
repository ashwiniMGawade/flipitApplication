<?php
class AddEmailSettingsTable extends Doctrine_Migration_Base
{
    public function up()
    {
    	$columns = array(
    			'id' => array(
    					'type'     => 'integer',
    					'length'   => 20,
    					'primary'  => 1,
    					'autoincrement' => 1,
    					'notnull'  => 1
    			),
    			'senderemail' => array(
    					'type'   => 'string'
    			),
    			'sendername' => array(
    					'type'   => 'integer',
    					'length' => 11
    			
    			),
    			'emailsubject' => array(
    					'type'   => 'integer',
    					'length' => 11
    			
    			),
    			'emailheader' => array(
    					'type'   => 'integer',
    					'length' => 11
    					 
    			),
    			'deleted' => array(
    					'type'   => 'boolean',
    					'length' => 1
    			),
    			'created_at' => array(
    					'type'   => 'timestamp',
    					'length' => 12
    			),
    			'updated_at' => array(
    					'type'   => 'timestamp',
    					'length' => 12
    			)
    			
    			
    	);
    	
    	$options = array(
    			'type'    => 'INNODB',
    			'charset' => 'utf8'
    	);
    	
    	$this->createTable( 'emailsettings', $columns, $options );
    	
    }

    public function down()
    {
        $this->dropTable( 'emailsettings' );
    }
}
 
 