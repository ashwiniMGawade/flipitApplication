<?php
class AddIndexOnConversionTable  extends Doctrine_Migration_Base
{
	public function up()
	{
		# create index on all table which don't have index on foreign key column 
 
		$this->addIndex( 'conversions', 'IP_Converted', 
					array( 
						'fields' => array(
							'converted' => array(),
							'IP' => array()
					))
		); 

 	}
	public function down()
	{
		$this->removeIndex( 'conversions', 'IP_Converted');
	}
}