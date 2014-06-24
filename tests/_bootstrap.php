<?php
// This is global bootstrap for autoloading 
//require_once 'TestCommons.php';
require_once(dirname(__FILE__) . '/../library/Doctrine/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(
    Doctrine_Core::ATTR_MODEL_LOADING,
    Doctrine_Core::MODEL_LOADING_CONSERVATIVE
);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
$manager->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, false);

Doctrine_Manager::connection("mysql://root:password@localhost/flipit_test");
