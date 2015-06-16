<?php
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

defined('ROOT_PATH')
|| define('ROOT_PATH', realpath(dirname(dirname(dirname(__FILE__))). '/public'));

require_once(LIBRARY_PATH.'/BackEnd/Helper/viewHelper-v1.php');
require_once(LIBRARY_PATH.'/PHPExcel/PHPExcel.php');

/** Zend_Application */
require_once (LIBRARY_PATH . '/Zend/Application.php');
require_once(DOCTRINE_PATH . '/Doctrine.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');


$connections = $application->getOption('doctrine');

spl_autoload_register(array('Doctrine', 'autoload'));

$manager = Doctrine_Manager::getInstance();

$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
//$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager = Doctrine_Manager::getInstance();

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        Doctrine_Query::create()->delete('RouteRedirect')->execute();
        //$spl = explode('/', HTTP_PATH);
        //$path = $spl[0].'//' . $spl[2];
        //echo get_home_path();
        //die();
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(ROOT_PATH."/301_redirect_incl-offline-shops.xlsx");
        $worksheet = $objPHPExcel->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();

            }
            $orignalURL =  $data[$cell->getRow()]['A'];
            $redirectUrl =  $data[$cell->getRow()]['B'];
            //find by name if exist in database
            if(!empty($orignalURL)){
                $redirect = Doctrine_Core::getTable('RouteRedirect')->findOneBy('orignalurl', $orignalURL);
                if(!empty($redirect)){
                }else{
                    $redirect  =new RouteRedirect();
                }
                if($orignalURL != " "){
                    $redirect->orignalurl= 'http://www.kortingscode.nl' . $orignalURL;
                }
                if($redirectUrl != " "){
                    $redirect->redirectto= 'http://www.kortingscode.nl' . $redirectUrl;
                }
                $redirect->save();

            } else {
                echo "The  Data has been imported Successfully!!";
                exit;
            }

        }
        die('DONE');
