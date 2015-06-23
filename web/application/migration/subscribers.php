<?php
// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH',
        dirname(dirname(__FILE__)));

defined('PUBLIC_PATH')
|| define('PUBLIC_PATH',
        dirname(dirname(dirname(__FILE__)))."/public");

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

//echo LIBRARY_PATH;
//echo DOCTRINE_PATH;
//die;
require_once(LIBRARY_PATH.'/FrontEnd/Helper/viewHelper-v1.php');
require_once (LIBRARY_PATH . '/Zend/Application.php');
require_once(DOCTRINE_PATH . '/Doctrine.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');

$connections = $application->getOption('doctrine');

spl_autoload_register(array('Doctrine', 'autoload'));

$manager = Doctrine_Manager::getInstance();

$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');

spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager = Doctrine_Manager::getInstance();
//Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
Doctrine_Core::loadModels(APPLICATION_PATH . '/models');




// every visitor no weekly email
$update = Doctrine_Query::create()->update('Visitor')
                                    ->set('weeklynewsletter', "0")
                                        ->execute();
// parse the csv with the e-mail addresses
$csv 	= PUBLIC_PATH.'/excels/members_Kortingscode_Apr_29_2013.csv';
$file = fopen($csv,"r");
$count = 0;
while(! feof($file) ){
    $email 		= null;
    $keyword 	= null;
    $data 		= fgetcsv($file,0,";");
    if (!empty($data[0])) {
        $email 		= strtolower(trim($data[0]));
        $keyword 	= strtolower(trim($data[1]));
    }
    if($email){
        // search in visitors db
        $visitor = Doctrine_Query::create()->select()
                                    ->from("Visitor")
                                    ->where("email LIKE ?", "%$email%")
                                                                    ->fetchArray();
        if ($visitor) {
            echo $visitor[0]['email']." is gevonden! \n";
            // update newsletter for user
            $update = Doctrine_Query::create()->update('Visitor')
                                                            ->set('weeklynewsletter', "1")
                                                                ->where('id = "'.$visitor[0]['id'].'"')
                                                                ->execute();
            // add keyword(?) as origin for users
            $visitorKeyword = new VisitorKeyword();
            $visitorKeyword->keyword 		= $keyword;
            $visitorKeyword->visitorId 	= $visitor[0]['id'];
            $visitorKeyword->save();

            $count++;

        }else{
            echo $email." Gaan we aanmaken! \n";
            $visitorObj = new Visitor();
            $visitorObj->email 						= $email;
            $visitorObj->password 				= md5('welkom');
            $visitorObj->weeklyNewsLetter = true;
            $visitorObj->save();
        }
    }

}
echo 'Total op 1: '.$count;
fclose($file);
