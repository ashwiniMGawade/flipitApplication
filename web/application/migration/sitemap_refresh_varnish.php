<?php
//error_reporting(E_ALL|E_STRICT);
/**
 * SitemapRefreshVarnish
 *
 * used to update the varnish based on the sitemap
 *
 * @author Surinderpal Singh
 *
 */
class SitemapRefreshVarnish
{
    protected $_locale = 'nl' ;
    protected $_localePath = '/' ;
    protected $_hostName = '' ;
    protected $_trans = null ;

    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');

        CommonMigrationFunctions::setTimeAndMemoryLimit();

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        # cycle htoruh all site database
        foreach ($connections as $key => $connection) {
            # check database is being must be site
            if($key != 'imbull') {
                try {
                    $this->refresh( $connection['dsn'],$key);

                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n" ;
                }
            }
        }

    }


    protected function refresh($dsn,$key)
    {



        if( $key == 'en') {
            $this->_localePath = '' ;
            $suffix = "" ;
        } else {
            $this->_localePath = $key."/" ;
            $suffix = "_" . strtoupper($key) ;
        }


        $connName ='doctrine_site';
        # auto load doctrine library
        spl_autoload_register(array('Doctrine', 'autoload'));


        # create coonection
        $DMC = Doctrine_Manager::connection($dsn, $connName);
        //$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

        # auto  model class
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        # cretae donctrine mager
        $manager = Doctrine_Manager::getInstance();

        # set manager attribute like table class, base classes etc
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');


        $cutsomLocale = LocaleSettings::getLocaleSettings();
        $cutsomLocale = !empty($cutsomLocale[0]['locale']) ? $cutsomLocale[0]['locale'] : 'nl_NL';

        $this->_trans = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));

        $this->_trans->addTranslation(
                array(
                        'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $suffix . '.mo',
                        'locale' => $cutsomLocale ,
                )
        );
        $this->_trans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public/' . strtolower ( $this->_localePath ) . 'language/form' . $suffix . '.mo', 
                'locale' => $cutsomLocale
                )
            );
        Zend_Registry::set('Zend_Translate', $this->_trans);

        #translating sitemaps names
        $sitemaps = FrontEnd_Helper_viewHelper::__form('form_sitemap');
        $plus = FrontEnd_Helper_viewHelper::__form('form_plus');
        $main = FrontEnd_Helper_viewHelper::__form('form_main');
        $shops = FrontEnd_Helper_viewHelper::__form('form_shops');

        $sitemap_shops = $sitemaps.'_'.$shops.'.xml';
        $sitemap_plus = $sitemaps.'_'.$plus.'.xml';
        $sitemap_main = $sitemaps.'_'.$main.'.xml';

        $loc ='' ;
        if($key != 'en') {
            $loc = $key ."/" ;
        }

        $varnishObj = new Varnish($connName);


       $shopFile =  PUBLIC_PATH.$loc.'sitemaps/'.$sitemap_shops;
      

        if(file_exists( $shopFile)) {

            // Parse the 3 xml's and add the url's to the varnish table
            $shop_urls = simplexml_load_file($shopFile);

            if (!empty($shop_urls) ) {
                foreach ($shop_urls as $url) {
                    $varnishObj->addUrl( $url->loc );
                }
            }
        }


        $mainFile = PUBLIC_PATH.$loc.'sitemaps/'.$sitemap_main ;
       
        if(file_exists( $mainFile)) {
            $main_urls = simplexml_load_file($mainFile);
            if (!empty($main_urls) ) {
                foreach ($main_urls as $url) {
                    $varnishObj->addUrl( $url->loc );
                }
            }
        }


        $bespaarFile = PUBLIC_PATH.$loc.'sitemaps/'.$sitemap_plus ;
       
        if(file_exists( $bespaarFile)) {
                $bespaar_urls = simplexml_load_file($bespaarFile);
                if (!empty($bespaar_urls)) {
                    foreach ($bespaar_urls as $url) {
                        $varnishObj->addUrl( $url->loc );
                    }
                }
        }

        #close connection
        $manager->closeConnection($DMC);

        print "\n$key : Varnish has been successfully refreshed!!!\n" ;

    }

}

    new SitemapRefreshVarnish();
