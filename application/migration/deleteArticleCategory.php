<?php

/**
 * Script for Deleting all article category from atriclecategory table
 *
 * @author Amit Sharma
 *
 */
class deleteArticleCategory
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_trans = null;

    public function __construct()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        require_once('constantForMigration.php');
        require_once('databseConnectionForMigrations.php');
    }

    protected function deleteArticleCategory($dsn, $key, $imbull)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
            $suffix = "" ;
        } else {
            $this->_localePath = $key . "/";
            $this->_hostName = "http://www.flipit.com";
            $suffix = "_" . strtoupper($key) ;
        }
      
        $doctrineManagerConnection = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $cutsomLocale = Signupmaxaccount::getAllMaxAccounts();
        $cutsomLocale = !empty($cutsomLocale[0]['locale']) ? $cutsomLocale[0]['locale'] : 'nl_NL';
        $this->_trans = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));
        $this->_trans->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php' . $suffix . '.mo',
                        'locale' => $cutsomLocale,
                )
        );
        $this->_trans->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $suffix . '.mo',
                        'locale' => $cutsomLocale ,
                )
        );

        $date = new Zend_Date();
        $month = $date->get(Zend_Date::MONTH_NAME);
        $year = $date->get(Zend_Date::YEAR);
        $day = $date->get(Zend_Date::DAY);

        defined('CURRENT_MONTH')
                || define('CURRENT_MONTH', $month );

        defined('CURRENT_YEAR')
        || define('CURRENT_YEAR', $year );

        defined('CURRENT_DAY')
        || define('CURRENT_DAY', $day );

        $ArticleCategoryDeleted =  Articlecategory::deleteAllArticleAndRefArticleCategory();

        if($ArticleCategoryDeleted){

            echo "\n";
            print "$key - Articles have been deleted successfully!!!";
        }else{

            echo "\n";
            print "$key - Articles have not been deleted!!!";

        }

        $manager->closeConnection($doctrineManagerConnection);

    }

}

new DeleteArticleCategory();
