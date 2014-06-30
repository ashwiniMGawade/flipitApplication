<?php

class deletePageAttributes
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_trans = null;

    public function __construct()
    {
        require_once('constantsForMigration.php');
        require_once('databaseConnectionForMigrations.php');
        foreach ($databaseConnections as $databaseConnectionKey => $databaseConnection) {
            if ($databaseConnectionKey != 'imbull') {
                try {
                    $this->deletePageAttributes($databaseConnection ['dsn'], $databaseConnectionKey, $imbull);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
        $doctrineManager->closeConnection($doctrineManagerConnection);
    }

    protected function deletePageAttributes($dsn, $key, $imbull)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
            $locale = "" ;
        } else {
            $this->_localePath = $key . "/";
            $this->_hostName = "http://www.flipit.com";
            $locale = "_" . strtoupper($key) ;
        }

        defined('PUBLIC_PATH')|| define('PUBLIC_PATH', dirname(dirname(dirname(__FILE__)))."/public/");

        $doctrineManagerConnection = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $doctrineManager = Doctrine_Manager::getInstance();
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $doctrineManager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $databaseLocale = Signupmaxaccount::getAllMaxAccounts();
        $databaseLocale = !empty($databaseLocale[0]['locale']) ? $databaseLocale[0]['locale'] : 'nl_NL';
        $this->_trans = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));

        $this->_trans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php'
                . $locale . '.mo',
                'locale' => $databaseLocale
            )
        );

        $this->_trans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links'
                . $locale . '.mo',
                'locale' => $databaseLocale
            )
        );

        $date = new Zend_Date();
        $month = $date->get(Zend_Date::MONTH_NAME);
        $year = $date->get(Zend_Date::YEAR);
        $day = $date->get(Zend_Date::DAY);

        defined('CURRENT_MONTH')|| define('CURRENT_MONTH', $month);
        defined('CURRENT_YEAR') || define('CURRENT_YEAR', $year);
        defined('CURRENT_DAY')  || define('CURRENT_DAY', $day);
        defined('PUBLIC_PATH')  || define('PUBLIC_PATH', dirname(dirname(dirname(__FILE__))) . "/public/");
        set_time_limit(0);
        $dataDeleted =  PageAttribute::deletePageAttributes();
        if ($dataDeleted) {
            echo "\n";
            print "$key - Attributes have been deleted successfully!!!";
        } else {
            echo "\n";
            print "$key - Attributes have already been deleted successfully!!!";
        }
        $doctrineManager->closeConnection($doctrineManagerConnection);
    }
}
new DeletePageAttributes();
