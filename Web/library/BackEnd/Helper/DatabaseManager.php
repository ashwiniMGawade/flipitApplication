<?php
class BackEnd_Helper_DatabaseManager extends BootstrapDoctrineConnectionFunctions
{
    public static function addConnection($localeKey = 'be')
    {
        # read dsn from config file an create new connection.
        $bootstrap = \Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();
        $localeKey = strtolower($localeKey);
        $dsn = $options['doctrine'][$localeKey]['dsn'];
        # setup memcached
        $config = self::setMemcachedAndProxyClasses($options['resources']);
        # create a new connection based on select dsn
        self::setEntityManagerForlocale($dsn, $config);
    }

    public static function closeConnection($conn = '')
    {
        $manager = Zend_Registry::get('emLocale');
        $manager->getConnection()->close();
    }
}