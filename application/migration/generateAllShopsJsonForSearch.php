<?php

/**
 * Generate json file of all shops for frontend serach
 *
 * @author Surinderpal Singh
 *
 */
class CreateShopsJSON
{
    protected $_localePath = '';


    public function __construct()
    {
        ini_set('memory_limit', '-1');

        set_time_limit(0);


        // Define path to application directory
        defined('APPLICATION_PATH')
        || define('APPLICATION_PATH',
                dirname(dirname(__FILE__)));

        defined('LIBRARY_PATH')
        || define('LIBRARY_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/library'));

        defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1');

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
        require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper-v1.php');
        require_once (LIBRARY_PATH . '/Zend/Application.php');
        require_once(DOCTRINE_PATH . '/Doctrine.php');

        // Create application, bootstrap, and run
        $application = new Zend_Application(APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');

        $connections = $application->getOption('doctrine');
        spl_autoload_register(array('Doctrine', 'autoload'));

        $manager = Doctrine_Manager::getInstance();

        $imbull = $connections['imbull'];

        // cycle through  all site database
        foreach ( $connections as $key => $connection ) {

            // check database is being must be site
            if ($key != 'imbull') {
                try {
                    $this->generateJSON ( $connection ['dsn'], $key ,$imbull );
                } catch ( Exception $e ) {

                    echo $e->getMessage ();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }


    }

    protected function generateJSON($dsn, $key,$imbull)
    {
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $manager = Doctrine_Manager::getInstance();
        //Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        $shop =  Shop::getAllStores();
        //setting to no time limit,
        set_time_limit(0);


        if ($key == 'en') {
            $this->_localePath = '';
        } else {
            $this->_localePath = $key . "/";
        }

        defined('PUBLIC_PATH')
        || define('PUBLIC_PATH',
                dirname(dirname(dirname(__FILE__)))."/public/");

        $pathToDir = PUBLIC_PATH . $this->_localePath . "js/front_end/json/";

        $fileName = "shops.js";

        if(!file_exists($pathToDir))
            mkdir($pathToDir, 776, TRUE);

        $pathToJSONFile = $pathToDir . $fileName ;


        $shops = array() ;
        foreach ($shop as $value) {
            $shops[] = array(	'label'=> $value['name'] ,
                                'value'=> $value['name'],
                                'permalink'=> $value['permalink'],
                                'id'=> $value['id']);
        }

        $jsonHandle = fopen($pathToJSONFile, 'w');
        $shopsData = " var shopsJSON = " . Zend_Json::encode($shops);
        fwrite($jsonHandle, $shopsData);
        fclose($jsonHandle);


        // echo "\n";
        // print "$key - JSON file for shops has been created successfully!!!";

        $manager->closeConnection($DMC);

    }

}


new CreateShopsJSON ();
