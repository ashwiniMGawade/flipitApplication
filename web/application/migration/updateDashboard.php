<?php

/**
 * Update the dashboard
 *
 * @author Raman
 *
 */
class updateDashboard
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_trans = null;

    public function __construct()
    {
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');

        CommonMigrationFunctions::setTimeAndMemoryLimit();

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();

        $imbull = $connections['imbull'];

        // cycle htoruh all site database
        foreach ( $connections as $key => $connection ) {
            // check database is being must be site
            if ($key != 'imbull') {
                try {

                    $this->updateDashboardTable ( $connection ['dsn'], $key ,$imbull );
                    $this->updateOfferViewcount($connection ['dsn'], $key);

                    $this->updateShopViewcount($connection ['dsn'], $key);
                } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }

        }
    }

    protected function updateDashboardTable($dsn, $key,$imbull)
    {
        if ($key == 'en') {
            $this->_localePath = '';
            $this->_hostName = "http://www.kortingscode.nl";
            $this->_logo = $this->_hostName . "/public/images/front_end/logo-popup.png";
        } else {
            $this->_localePath = $key . "/";
            $this->_hostName = "http://www.flipit.com";
        }

        defined('PUBLIC_PATH')
        || define('PUBLIC_PATH',
                dirname(dirname(dirname(__FILE__)))."/public/");

        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $manager = Doctrine_Manager::getInstance();
        //Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        //$cutsomLocale = Signupmaxaccount::getAllMaxAccounts ();
        //$cutsomLocale = ! empty ( $cutsomLocale [0] ['locale'] ) ? $cutsomLocale [0] ['locale'] : 'nl_NL';

        $this->_trans = new Zend_Translate ( array ('adapter' => 'gettext', 'disableNotices' => true ) );

        defined ( 'PUBLIC_PATH' ) || define ( 'PUBLIC_PATH', dirname ( dirname ( dirname ( __FILE__ ) ) ) . "/public/" );

        //$this->_trans->addTranslation ( array ('content' => PUBLIC_PATH . strtolower ( $this->_localePath ) . 'language/po_links.mo', 'locale' => $cutsomLocale ) );

        //Zend_Registry::set('Zend_Translate', $this->_trans);

        //setting to no time limit,
        set_time_limit(0);

        //declaring class instance
        $dashboard = new Dashboard();

        //Getting data for dashboard table
        $noOfOffers = $dashboard->amountOfOffersCreatedLastWeek();
        $noOfShops = $dashboard->amountOfShopsCreatedLastWeek();
        $noOfClickouts = $dashboard->amountOfClickoutsLastWeek();
        $noOfSubscribers = $dashboard->amountOfSubscribersLastWeek();

        $totNoOfOffers = $dashboard->totalAmountOfOffers();
        $totNoOfShops = $dashboard->totalAmountOfShops();
        $totNoOfshopsCodeOnline = $dashboard->totalAmountOfShopsCodeOnline();
        $totNoOfshopsCodeOnlineThisWeek = $dashboard->totalAmountOfShopsCodeOnlineThisWeek();
        $totNoOfshopsCodeOnlineLastWeek = $dashboard->totalAmountOfShopsCodeOnlineLastWeek();
        $totNoOfSubscribers = $dashboard->totalAmountOfSubscribers();
        

        $p1 = $noOfOffers['amountOffers'];
        $p2 = $noOfShops['amountshops'];
        $p3 = $noOfClickouts['amountclickouts'];
        $p4 = $noOfSubscribers['amountsubs'];
        $p5 = $totNoOfOffers['amountOffers'];
        $p6 = $totNoOfShops['amountshops'];
        $p7 = $totNoOfshopsCodeOnline['amountshops'];
        $p8 = $totNoOfshopsCodeOnlineThisWeek['amountshops'];
        $p9 = $totNoOfshopsCodeOnlineLastWeek['amountshops'];
        $p10 = $totNoOfSubscribers['amountsubs'];
        $moneyShopRatio = $dashboard->getMoneyShopRatio();
        //Update dashboard table with current data

        $dashboard->updateDashboard($p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $moneyShopRatio);

        $manager->closeConnection($DMC);
        echo "\n";
        print "$key - Dashboard has been updated successfully!!!";


        //$DMC1 = Doctrine_Manager::connection($imbull, 'doctrine');
    }


    /**
     * updateOfferViewcount
     *
     * count clicks per offer and update total clicks in offer table
     *
     * @param connection string $dsn
     * @param database to connect $key
     *
     */
    protected function updateOfferViewcount($dsn, $key)
    {
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $manager = Doctrine_Manager::getInstance();
        //Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');



        //setting to no time limit,
        set_time_limit(0);

        Offer::updateTotalViewCount();

        $manager->closeConnection($DMC);
        echo "\n";
        print "$key - Offer clicks  has been updated successfully!!!";


        //$DMC1 = Doctrine_Manager::connection($imbull, 'doctrine');
    }

    /**
     * updateShopViewcount
     *
     * count clicks per shop and update total clicks in shop table
     *
     * @param connection string $dsn
     * @param database to connect $key
     *
     */
    protected function updateShopViewcount($dsn, $key)
    {
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $manager = Doctrine_Manager::getInstance();
        //Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');



        //setting to no time limit,
        set_time_limit(0);


        Shop::updateTotalViewCount();
        $manager->closeConnection($DMC);
        echo "\n";
        print "$key - Shop clicks  has been updated successfully!!!";


        //$DMC1 = Doctrine_Manager::connection($imbull, 'doctrine');
    }
}


new UpdateDashboard();
