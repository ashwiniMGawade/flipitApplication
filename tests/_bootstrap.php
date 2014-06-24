<?php
// This is global bootstrap for autoloading 
//require_once 'TestCommons.php';
require_once(dirname(__FILE__) . '/../library/Doctrine/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
$manager = Doctrine_Manager::getInstance();
