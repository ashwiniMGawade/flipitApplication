<?php

/**
 * SiteMaps Generation
 *
 * @author Surinderpal Singh
 *
 */
class createSiteMaps
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_translate = null;


    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('databaseConnectionForMigrations.php');
        foreach ( $databaseConnections as $key => $connection ) {

            if ($key != 'imbull') {
                try {

                    $this->generateMaps ( $connection ['dsn'], $key ,$imbull );
                } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }
    }

    protected function generateMaps($dsn, $key,$imbull)
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

        $domainForRobot = $this->_hostName . '/public/' . $this->_localePath;
        $pathToXMLFile = PUBLIC_PATH . $this->_localePath;
        $DoctrineConnectionManager = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $connectionManager = Doctrine_Manager::getInstance();
        $connectionManager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $connectionManager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $connectionManager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        $databaseLocale = LocaleSettings::getLocaleSettings();
        $databaseLocale = !empty($databaseLocale [0] ['locale'] ) ? $databaseLocale [0] ['locale'] : 'nl_NL';
        $this->_translate = new Zend_Translate(array('adapter' => 'gettext', 'disableNotices' => true));
        $this->_translate->addTranslation(array('content' => PUBLIC_PATH . strtolower ( $this->_localePath ) . 'language/po_links' . $locale . '.mo', 'locale' => $databaseLocale));
        $this->_translate->addTranslation(array('content' => PUBLIC_PATH . strtolower ( $this->_localePath ) . 'language/form' . $locale . '.mo', 'locale' => $databaseLocale));
        $pathToXMLFile = PUBLIC_PATH . $this->_localePath;
        Zend_Registry::set('Zend_Translate', $this->_translate);
        set_time_limit(0);
        $sitemap = new PHPSitemap_sitemap();
        $sitemaps = FrontEnd_Helper_viewHelper::__form('form_sitemap');
        $plus = FrontEnd_Helper_viewHelper::__form('form_plus');
        $main = FrontEnd_Helper_viewHelper::__form('form_main');
        $shops = FrontEnd_Helper_viewHelper::__form('form_shops');
        $info = FrontEnd_Helper_viewHelper::__link('link_info');
        $rssfeed = FrontEnd_Helper_viewHelper::__link('link_rssfeed');
        $zoeken = FrontEnd_Helper_viewHelper::__link('link_zoeken');
        $login = FrontEnd_Helper_viewHelper::__link('link_login');
        $signup = FrontEnd_Helper_viewHelper::__link('link_inschrijven');
        $sitemap_shops = $sitemaps.'_'.$shops.'.xml';
        $sitemap_plus = $sitemaps.'_'.$plus.'.xml';
        $sitemap_main = $sitemaps.'_'.$main.'.xml';
        $localeSlash = $locale != "" ? "/" : "";

        if ($key == 'en') {
            $robotSitemapShops = 'http://a63398.hostedsitemaps.com/3927973/sitemap.xml';
            $robotSitemapPlus = 'http://a63398.hostedsitemaps.com/3927973/sitemap_images.xml';
            $robotSitemapMain = 'http://a63398.hostedsitemaps.com/3927973/sitemap_video.xml';
        } else {
            $robotSitemapShops = $domainForRobot."sitemaps/".$sitemap_shops;
            $robotSitemapPlus = $domainForRobot."sitemaps/".$sitemap_plus;
            $robotSitemapMain = $domainForRobot."sitemaps/".$sitemap_main;
        }

        $shopmap = $sitemap->generate_shops_sitemap($this->_hostName, $key);
        $mainDir = $pathToXMLFile."sitemaps/";

        if(!file_exists($mainDir))
            mkdir($mainDir, 0776, TRUE);

        $shopFile = $pathToXMLFile."sitemaps/".$sitemap_shops;
        $shopHandle = fopen($shopFile, 'w');
        fwrite($shopHandle, $shopmap);

        echo "\n";
        print "$key - Sitemap for Online shops has been created successfully!!!";
        fclose($shopHandle);

        $guidemap = $sitemap->generateGuidesSitemap($this->_hostName, $key);
        $guideFile = $pathToXMLFile."sitemaps/".$sitemap_plus;
        $guideHandle = fopen($guideFile, 'w');
        fwrite($guideHandle, $guidemap);

        echo "\n";
        print "$key - Sitemap for Guides has been created successfully!!!";
        fclose($guideHandle);
        $mainmap = $sitemap->generate_main_sitemap($this->_hostName, $key);
        $mainFile = $pathToXMLFile."sitemaps/".$sitemap_main;
        $mainHandle = fopen($mainFile, 'w');
        fwrite($mainHandle, $mainmap);
        $connectionManager->closeConnection($DoctrineConnectionManager);
        echo "\n";
        print "$key - Main sitemap has been created successfully!!!";
        fclose($mainHandle);
    }
}
new CreateSiteMaps();
