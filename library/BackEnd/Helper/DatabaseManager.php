<?php
class BackEnd_Helper_DatabaseManager
{
	/**
	* create dynamic for chain management
	*  
	*/
	public static function addConnection($key = 'be')
	{
			# read dsn from confiog file an dcreta new contien connection
			
			$manager = Doctrine_Manager::getInstance();
			$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
			$options = $bootstrap->getOptions();
			
			$key = strtolower($key);
		    $connName = "dynamic_conn_" . $key;
			
			$dsn = $options['doctrine'][$key]['dsn'];
			
			# create a nrew connectoion based on select dsn  
			$conn = $manager->connection($dsn,$connName);
			
			if ($conn === $manager->getCurrentConnection()) {
				
				return array('connName' =>$connName, 'adapter' => $conn) ;
			}
	
	}
	
	
	
	/**
	 * Close databse connection
	 * 
	 */
	
	
	public static function closeConnection($conn)
	{
		if ($conn)
		{
			$manager = Doctrine_Manager::getInstance();
			$manager->closeConnection($conn);
		}
	}	
	
}