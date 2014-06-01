<?php

/**
 * Script for Inserting the sign up page attribute in page attribute table
 *
 * @author Amit Sharma
 *
 */
class insertPageAttribute
{
    protected $_localePath = '/';
    protected $_hostName = '';
    protected $_trans = null;


    public function __construct()
    {
    ini_set('memory_limit', '-1');

    set_time_limit(0);
    /*
    $domain1 = $_SERVER['HOSTNAME'];
    $domain = 'http://www.'.$domain1;
    */

    // Define path to application directory
    defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
            dirname(dirname(__FILE__)));

    defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));

    defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine');

    // Define application environment
    defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
            (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                    : 'production'));


    //Ensure library/ is on include_path
    set_include_path(
            implode(PATH_SEPARATOR,
                    array(realpath(APPLICATION_PATH . '/../library'),
                            get_include_path(),)));
        set_include_path(
                implode(PATH_SEPARATOR,
                        array(realpath(DOCTRINE_PATH), get_include_path(),)));

        /** Zend_Application */
        require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper.php');
        require_once(LIBRARY_PATH . '/Zend/Application.php');
        require_once(DOCTRINE_PATH . '/Doctrine.php');

        // Create application, bootstrap, and run
        $application = new Zend_Application(APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');

        $connections = $application->getOption('doctrine');
        spl_autoload_register(array('Doctrine', 'autoload'));

        $manager = Doctrine_Manager::getInstance();

        $imbull = $connections['imbull'];

        // cycle htoruh all site database
        $DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');


        // cycle htoruh all site database
        foreach ( $connections as $key => $connection ) {
            // check database is being must be site
            if ($key != 'imbull') {
                try {

                    $this->addPageAttribute( $connection ['dsn'], $key ,$imbull );

                    } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }


        $manager->closeConnection($DMC1);
    }

    protected function addPageAttribute($dsn, $key,$imbull)
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


        #define currecnt month for text with [month]
        defined('CURRENT_MONTH')
                || define('CURRENT_MONTH', $month );

                        #define currecnt year for text with [year]
        defined('CURRENT_YEAR')
        || define('CURRENT_YEAR', $year );

        #define currecnt day for text with [day]
        defined('CURRENT_DAY')
        || define('CURRENT_DAY', $day );

        defined ( 'PUBLIC_PATH' ) || define ( 'PUBLIC_PATH', dirname ( dirname ( dirname ( __FILE__ ) ) ) . "/public/" );


        //setting to no time limit,
        set_time_limit(0);

        //declaring class instance
        $data =  PageAttribute::insertPageAttribute();

        if($data){

            echo "\n";
            print "$key - Attributes have been inserted successfully!!!";
        }else{

            echo "\n";
            print "$key - Attributes have already been inserted successfully!!!";

        }


        $manager->closeConnection($DMC);




    }

}


new InsertPageAttribute();
