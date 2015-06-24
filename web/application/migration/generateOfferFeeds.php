<?php
//error_reporting(E_ALL|E_STRICT);
/**
 * Rss feeds  Generation
 *
 * @author Surinderpal Singh
 *
 */
class generateOfferFeeds
{
    protected $_locale = 'nl' ;
    protected $_localePath = '/' ;
    protected $_hostName = '' ;
    protected $_trans = null ;
    protected $_logo = null ;
    protected $_method = null ;
    protected $_modules = null ;
    protected $_public_cdn_path = '';

    public function __call($method, $args)
    {
        print "The method \"{$method}\" does not exists!!!\n";
    }

    public function __construct($method)
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();
        # cycle htoruh all site database
        foreach ($connections as $key => $connection) {
            # check database is being must be site
            if ($key != 'imbull') {
                try {
                    $this->createConnection($connection['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    echo "\n\n" ;
                }
            }
        }
    }

    protected function createConnection($dsn, $key)
    {

        if ($key == 'en') {
            $this->_localePath = '' ;
            $this->_hostName = "http://www.kortingscode.nl" ;
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
            $suffix = "" ;
            $this->_public_cdn_path = 'http://img.kortingscode.nl/public/';
        } else {

            $this->_localePath = $key."/" ;
            $this->_hostName = "http://www.flipit.com" . '/'.$key ;
            $this->_logo = "http://www.flipit.com" . "/public/images/front_end/flip-logo.png";
            $suffix = "_" . strtoupper($key) ;
            $this->_public_cdn_path = 'http://img.flipit.com/public/'.$this->_localePath;
        }

        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');
        $settings = LocaleSettings::getLocaleSettings();
        $cutsomLocale = !empty( $settings[0]['locale']) ? $settings[0]['locale'] : 'nl_NL';
        $this->_trans = new Zend_Translate(array(
                'adapter' => 'gettext',
                'disableNotices' => true));

        $this->_trans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/fallback/frontend_php' . $suffix . '.mo',
                'locale' => $cutsomLocale,
            )
        );

        $this->_trans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public/'. strtolower($this->_localePath).'language/form' . $suffix . '.mo',
                'locale' => $cutsomLocale,
            )
        );

        $this->_trans->addTranslation(
            array(
                'content' => APPLICATION_PATH.'/../public/'.strtolower($this->_localePath).'language/po_links' . $suffix . '.mo',
                'locale' => $cutsomLocale ,
            )
        );
        Zend_Registry::set('Zend_Translate', $this->_trans);
        Zend_Registry::set('Zend_Locale', $cutsomLocale);
        call_user_func(array($this, $this->_method));
        $manager->closeConnection($DMC);
    }
    protected function newOffers()
    {
        $offers = Offer::getNewestOffersForRSS();
        $entries = array();

        foreach ($offers as $offer) {
            $shopImage = '<img src="'.$this->_public_cdn_path.ltrim($offer['shop']['logo']['shopImagePath'], '/')
            .'thum_big_'.$offer['shop']['logo']['shopImageName'].'" alt="'.$offer['shop']['logo']['shopImageName'].'">';
            $offerTermsWithShopImage = $offer['terms'].$shopImage;
            $entry = array(
                        'title'       => $offer['title'] ,
                        'link'        => $this->_hostName . '/' . $offer['permalink'],
                        'description' => "{$offerTermsWithShopImage}",
                        'lastUpdate' =>  strtotime($offer['lastUpdate']),
                        'guid' => $offer['id'] );
            if ($entry) {
                array_push($entries, $entry);
            }
        }

        $feedData = array(
                'title'=> FrontEnd_Helper_viewHelper::__form('form_Newest offers') ,
                'link'=> $this->_hostName ,
                'language' =>  str_replace('_', '-', Zend_Registry::get('Zend_Locale')) ,
                'charset'=>'UTF-8',
                'image'	=> $this->_logo,
                'entries'=>$entries
        );
        $feed = Zend_Feed::importArray($feedData, 'rss');
        $rssDirectory = PUBLIC_PATH. $this->_localePath ."rss/";
        $fileName = FrontEnd_Helper_viewHelper::__form('form_newest-offers');
        $offerXml = $rssDirectory. "{$fileName}.xml";

        if (!file_exists($rssDirectory)) {
            mkdir($rssDirectory, 0775, TRUE);
        }

        if (file_exists($offerXml)) {
            unlink($offerXml);
        }

        $rssFeed = $feed->saveXML();
        $offerHandle = fopen($offerXml, 'w');
        fwrite($offerHandle, $rssFeed);
        fclose($offerHandle);
        print trim($this->_hostName, '/')." - RSS feed for newest offers has been created successfully!!!\n" ;

    }

    protected function popularOffers()
    {
        $offers = Offer::getTopOffers(10);
        $entries = array();
        foreach ($offers as $offer) {
            $shopImage = '<img src="'.$this->_public_cdn_path.ltrim($offer['shop']['logo']['path'], '/')
            .'thum_big_'.$offer['shop']['logo']['name'].'" alt="'.$offer['shop']['logo']['name'].'">';
            $terms = isset($offer['termandcondition'][0]) ? $offer['termandcondition'][0]['content'] : '' ;
            $offerTermsWithShopImage = $terms.$shopImage;
            $entry = array(
                        'title'       => $offer['title'] ,
                        'link'        => $this->_hostName . '/' . $offer['shop']['permalink'],
                        'description' => "$offerTermsWithShopImage" ,
                        'lastUpdate' =>  strtotime($offer['lastUpdate']),
                        'guid' => $offer['id'] );
            if ($entry) {
                array_push($entries, $entry);
            }
        }

        $feedData = array(
                'title'=> FrontEnd_Helper_viewHelper::__form('form_Popular offers') ,
                'link'=> $this->_hostName ,
                'language' =>  str_replace('_', '-', Zend_Registry::get('Zend_Locale')) ,
                'charset'=>'UTF-8',
                'image'	=> $this->_logo,
                'entries'=>$entries
        );
        $feed = Zend_Feed::importArray($feedData, 'rss');
        $rssDirectory = PUBLIC_PATH. $this->_localePath ."rss/";
        $fileName = FrontEnd_Helper_viewHelper::__form('form_popular-offers');
        $offerXml = $rssDirectory. "{$fileName}.xml";

        if (!file_exists($rssDirectory)) {
            mkdir($rssDirectory, 0775, TRUE);
        }

        if (file_exists($offerXml)) {
            unlink($offerXml);
        }

        $rssFeed = $feed->saveXML();
        $offerHandle = fopen($offerXml, 'w');
        fwrite($offerHandle, $rssFeed);
        fclose($offerHandle);
        print print trim($this->_hostName, '/')." - RSS feed  for popular offers has been created successfully!!! \n";
    }
}

$locale = '';
$method = isset($argv[1]) ? $argv[1] : null;

if ($method) {
    new GenerateOfferFeeds($method);
} else {
    print "The method not passed";

}
