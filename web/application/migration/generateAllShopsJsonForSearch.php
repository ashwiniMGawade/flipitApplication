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
        require_once 'ConstantForMigration.php';
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        $manager = CommonMigrationFunctions::getGlobalDbConnectionManger();

        // cycle through  all site database
        foreach ($connections as $key => $connection) {

            // check database is being must be site
            if ($key != 'imbull') {
                try {
                    $this->generateJSON($connection ['dsn'], $key);
                } catch ( Exception $e ) {

                    echo $e->getMessage();
                    echo "\n\n";
                }
                echo "\n\n";
            }
        }


    }

    protected function generateJSON($dsn, $key)
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

        $pathToDir = PUBLIC_PATH . $this->_localePath . "js/front_end/json/";

        $fileName = "shops.js";

        if(!file_exists($pathToDir))
            mkdir($pathToDir, 0775, TRUE);

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
