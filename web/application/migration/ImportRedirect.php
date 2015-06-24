<?php
require_once 'ConstantForMigration.php';
require_once('CommonMigrationFunctions.php');

CommonMigrationFunctions::setTimeAndMemoryLimit();

$connections = CommonMigrationFunctions::getAllConnectionStrings();
$manager = CommonMigrationFunctions::getGlobalDbConnectionManger();

$DMC = Doctrine_Manager::connection($connections['dsn'], 'doctrine_site');
//$DMC1 = Doctrine_Manager::connection($connections['imbull'], 'doctrine');

spl_autoload_register(array('Doctrine', 'modelsAutoload'));

$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

CommonMigrationFunctions::loadDoctrineModels();

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
