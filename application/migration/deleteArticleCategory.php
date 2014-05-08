<?php

/**
 * Script for Deleting all article category from atriclecategory table
 */
class deleteArticleCategory
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_translate = null;

    public function __construct()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        require_once('constantsForMigration.php');
        require_once('databaseConnectionForMigrations.php');
    }

    protected function deleteArticleCategories($dsn, $localeKey, $imbull)
    {
        if ($localeKey == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
            $locale = "" ;
        } else {
            $this->_localePath = $localeKey . "/";
            $this->_hostName = "http://www.flipit.com";
            $locale = "_" . strtoupper($localeKey) ;
        }
      
        $doctrineManagerConnection = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $doctrineManager = Doctrine_Manager::getInstance();
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $doctrineManager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $customLocale = Signupmaxaccount::getAllMaxAccounts();
        $customLocale = !empty($customLocale[0]['locale']) ? $customLocale[0]['locale'] : 'nl_NL';
        $this->_translate = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));
        $this->_translate->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/frontend_php' . $locale . '.mo',
                        'locale' => $customLocale,
                )
        );
        $this->_translate->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $locale . '.mo',
                        'locale' => $customLocale ,
                )
        );

        $currentDate = new Zend_Date();
        $currentMonth = $currentDate->get(Zend_Date::MONTH_NAME);
        $currentYear = $currentDate->get(Zend_Date::YEAR);
        $currentDay = $currentDate->get(Zend_Date::DAY);

        defined('CURRENT_MONTH')
                || define('CURRENT_MONTH', $currentMonth);

        defined('CURRENT_YEAR')
        || define('CURRENT_YEAR', $currentYear);

        defined('CURRENT_DAY')
        || define('CURRENT_DAY', $currentDay);

        $deletedRelatedArticleCategories =  Articlecategory::deleteAllArticleCategoriesAndReferenceArticleCategories();

        if ($deletedRelatedArticleCategories) {

            echo "\n";
            print "$localeKey - Articles have been deleted successfully!!!";
        } else {

            echo "\n";
            print "$localeKey - Articles have not been deleted!!!";

        }

        $doctrineManager->closeConnection($doctrineManagerConnection);

    }

}

new DeleteArticleCategory();
