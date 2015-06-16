<?php
ini_set('max_execution_time',115200);
ini_set("memory_limit","1024M");
set_time_limit ( 10000 );



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
$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager = Doctrine_Manager::getInstance();
//Doctrine_Core::loadModels(APPLICATION_PATH . '/models/generated');

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        $format = 'Y-m-j H:i:s';
        $date = date($format);
        // - 4 days from today
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;
        //echo 'lessFour='.$past4Days;
        //echo 'current='	. $nowDate;
        $NewpapularCode = Doctrine_Query::create()->select('v.id, v.offerId, ((sum(v.onclick)) / (DATEDIFF(NOW(),o.startdate))) as pop, o.startdate')
                ->from('ViewCount v')
                ->leftJoin('v.offer o')
                ->leftJoin('o.shop s')
                ->where('v.updated_at <=' . "'$nowDate' AND v.updated_at >=". "'$past4Days'")
                ->andWhere('o.deleted = 0')
                ->andWhere('s.deleted = 0')
                ->andWhere('s.affliateProgram = 1')
                ->andWhere('o.offline = 0')
                ->andWhere('o.enddate > "'.$date.'"')
                ->andWhere('o.startdate <= "'.$date.'"')
                ->andWhere("o.discountType ='CD'")
                ->andWhere('o.Visability!="MEM"')
                ->groupBy('v.offerId')->orderBy('pop DESC')->limit(10)
                ->fetchArray();
        //echo "New offer";
        //echo "<pre>";
        //print_r($NewpapularCode);
        //die;
        //get last position id from database
        $lastPostionOffer = Doctrine_Query::create()->select('p.position')
                ->from('PopularCode p')->orderBy('p.position DESC')->limit(1)
                ->fetchArray();

        if(sizeof($lastPostionOffer) > 0){
        $lastPos = intval($lastPostionOffer[0]['position']) + 1;
        } else {

            $lastPos = 1;
        }

        //get all existing popular code from database
        $allExistingOffer = Doctrine_Query::create()
                ->select('p.id,o.id,p.type,p.position')->from('PopularCode p')
                ->leftJoin('p.offer o')->orderBy('p.position')->fetchArray();
        //echo "Old Popular code";
        //echo "<pre>";
        //print_r($allExistingOffer);
        //die();

        $tempold = array();
        foreach ($allExistingOffer as $popular) {

            if($popular['type'] == 'MN'){

                $tempold[] = $popular;

            }

        }
        //echo "<pre>";
        //print_r($tempold);
        //die;
        $lengthOfNewPopCode = count($NewpapularCode);
        $lengthOfOldManPopCode = count($tempold);
        $totalLength = $lengthOfNewPopCode + $lengthOfOldManPopCode;

        $length = 0;
        $lenOldMN = 0;
        $lenNewPop = 0;
        $newArray = array();
        $position = 1;

        //generate new array ( combine with existing and new popular code

        while($length < $totalLength){
            if(!empty($tempold[$lenOldMN])){

                if($tempold[$lenOldMN]['position'] == $position){

                    $Ar = array('type' => $tempold[$lenOldMN]['type'],
                            'offerId' => $tempold[$lenOldMN]['offer']['id'],
                            'position' => $tempold[$lenOldMN]['position']);

                    $newArray[$tempold[$lenOldMN]['offer']['id']] = $Ar;
                    $lenOldMN++;
                    $position++;

                } elseif (!array_key_exists($NewpapularCode[$lenNewPop]['offerId'], $newArray)) {

                    $Ar = array('type' => 'AT', 'offerId' => $NewpapularCode[$lenNewPop]['offerId'],
                            'position' => $position);
                    $newArray[$NewpapularCode[$lenNewPop]['offerId']] = $Ar;
                    $lenNewPop++;
                    $position++;

                } else{

                    $lenNewPop++;
                }

            } elseif (!array_key_exists($NewpapularCode[$lenNewPop]['offerId'], $newArray)) {

                    $Ar = array('type' => 'AT', 'offerId' => $NewpapularCode[$lenNewPop]['offerId'],
                            'position' => $position);
                    $newArray[$NewpapularCode[$lenNewPop]['offerId']] = $Ar;
                    $lenNewPop++;
                    $position++;

            }else{

                $lenNewPop++;
            }

            $length++;
        }

        //echo "LAST NEW ARRAY";
        //echo "<pre>";
        //print_r($newArray);
        //die();

        $frontendOptions = array(
                'lifetime' => 300,                   // cache lifetime
                'automatic_serialization' => true
        );

        $backendOptions = array('cache_dir' => PUBLIC_PATH.'/tmp/');

        $cache = Zend_Cache::factory('Output',
                'File',
                $frontendOptions,
                $backendOptions);

        Zend_Registry::set('cache',$cache);

        $cache = Zend_Registry::get('cache');
        $Q = Doctrine_Query::create()->delete()->from('PopularCode s')->where("s.type='AT'")->execute();
        foreach ($newArray as $p) {

            if ($p['type']!='MN') {

                //save popular code in database if new
                $pc = new PopularCode();
                $pc->type = $p['type'];
                $pc->offerId = $p['offerId'];
                $pc->position = $p['position'];
                $pc->save();

                $offerID = $p['offerId'];
                $authorId = \KC\Repsoitory\Offer::getAuthorId($offerID);

                $uid = $authorId[0]['authorId'];
                $popularcodekey ="all_". "popularcode".$uid ."_list";

                $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($popularcodekey);
                if($flag){

                } else {
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
                }


            }
        }

        // If any position is missing it fixes so that all positions must be there

        $newOfferList = Doctrine_Query::create()->select('p.*')
        ->from('PopularCode p')
        ->orderBy('p.position ASC')
        ->fetchArray();

        $newPos = 1;

        foreach ($newOfferList as $newOffer) {

            $O = Doctrine_Query::create()
            ->update('PopularCode')
            ->set('position', $newPos)
            ->where('id = ?' , $newOffer['id']);

            $O->execute();
            $newPos++;
        }


        //call cache function
        $popularcodekey1 ="10_popularShops_list";
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($popularcodekey1);
        if($flag) {

        } else {
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey1);
        }
        $popularcodekey ="all_popularcode_list";
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($popularcodekey);
        if($flag){

        } else {
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
        }


echo "The Popular Codes has been refreshed successfully!!";
