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



        $handle = opendir(ROOT_PATH . '/Logo/Logo');
        $rootpath = ROOT_PATH . '/Logo/Logo/';
        $pathToUpload = ROOT_PATH . 'images/upload/shop/';
        $pathUpload = 'images/upload/shop/';

        //Screen Shots
        $siteHandle = opendir(ROOT_PATH . '/Logo/Screenshot');
        $rootSitePath = ROOT_PATH . '/Logo/Screenshot/';
        $pathToUploadSiteImg = ROOT_PATH . 'images/upload/screenshot/';
        $sitePathUpload = 'images/upload/screenshot/';



        $image_array =  array(); // Array for all image names
        $siteimage_array =  array(); // Array for all site image names

        // Get all the images from the folder and store in an array-$image_array
        while($file = readdir($handle)){
            if($file !== '.' && $file !== '..'){

                $image_array[] = $file;

            }
        }

        while($fileSite = readdir($siteHandle)){
            if($fileSite !== '.' && $fileSite !== '..'){

                $siteimage_array[] = $fileSite;

            }
        }




        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(ROOT_PATH."/shopsdata.xlsx");

        $data =  array();
        $worksheet = $objPHPExcel->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {

            $i=  0;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                //$data[2]['A'] = $cell->getValue();
                $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();

            }

            $name =  $data[$cell->getRow()]['A'];
            $logo =  $data[$cell->getRow()]['B'];
            $websiteScreen =  $data[$cell->getRow()]['C'];
            $shop_text = $data[$cell->getRow()]['D'];
            $freeDel = $data[$cell->getRow()]['E'];
            $delCost = $data[$cell->getRow()]['F'];
            $returnPol = $data[$cell->getRow()]['G'];
            $delTime = $data[$cell->getRow()]['H'];

            //find by name if exist in database
            if(!empty($name)){

                $shopList = Doctrine_Core::getTable('Shop')->findOneBy('name', $name);

                if(!empty($shopList)){


                    if($shop_text != ""){

                        //$shopList->shopText = $shop_text;

                    }else{

                        //echo "lege desc voor ".$shopList['id']."\r\n";
                        //echo $shop_text."\n\r";

                    }
                    if($freeDel == 0 || $freeDel=='0'||$freeDel == 1||$freeDel == '1'){

                        //$shopList->freeDelivery = intval($freeDel);
                        //$shopList->deliveryCost = $delCost;

                    }else {

                        //$shopList->freeDelivery = intval($freeDel);
                        //$shopList->deliveryCost = " ";

                    }

                    if($returnPol != " "){

                        //$shopList->returnPolicy=$returnPol;

                    }

                    if($returnPol != " "){

                        //$shopList->Deliverytime= $delTime;

                    }



                    $key = array_search(strtolower($logo), array_map('strtolower', $image_array));


                    if(!empty($key)){

                        $file = $image_array[$key];
                        $newName = time() . "_" . $file;

                        $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);
                        $originalpath = $rootpath.$file;

                        if($ext=='jpg' || $ext == 'png' || $ext =='JPEG'|| $ext =='PNG' || $ext =='gif'){


                            $thumbpath = $pathToUpload . "thum_large_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 200, 150, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_small_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 84, 42, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_store_" . $newName;
                            BackEnd_Helper_viewHelper::resizeImageFromFolder($originalpath, 200, 100, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_big_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 234, 117, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_expired_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $shopList->logo->ext = $ext;
                            $shopList->logo->path = $pathUpload;
                            $shopList->logo->name = $newName;

                        } else{
                            echo $logo." This is an Invalid image";
                        }
                    }

                    //Website Screen shots

                    $keySite = array_search(strtolower($websiteScreen), array_map('strtolower', $siteimage_array));
                    if(!empty($keySite)){

                        $sitefile = $siteimage_array[$keySite];
                        $sitenewName = time() . "_" . $sitefile;

                        $siteExt = BackEnd_Helper_viewHelper :: getImageExtension($sitefile);
                        $originalpath = $rootSitePath.$sitefile;

                        if($siteExt=='jpg' || $siteExt == 'png' || $siteExt =='JPEG'|| $siteExt =='PNG' || $siteExt =='gif'){

                            $thumbpath = $pathToUploadSiteImg . "thum_large_" . $sitenewName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 450,0, $thumbpath, $siteExt);
                            $shopList->screenshot->ext = $siteExt;
                            $shopList->screenshot->path = $sitePathUpload;
                            $shopList->screenshot->name = $sitenewName;

                        } else{
                            echo $websiteScreen." This is an Invalid image";
                        }
                    }

                    $shopList->save();

                }else{

                    //add new shops in databases

                }
            } else {
                echo "The Shop Images Data has been imported Successfully!!";
                exit;
            }

        }
