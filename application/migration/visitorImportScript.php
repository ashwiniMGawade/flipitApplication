<?php
/**
 * Script for importing visitors
 *
 * @author Daniel
 */

new VisitorImport();

class VisitorImport {

    protected $_localePath  = '/';
    protected $_trans       = null;
    private $dbh            = null;

    function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        
        foreach ( $connections as $key => $connection ) {
            // check if database is a site
            if ($key != 'imbull') {
                try {
                    $this->importShops( $connection ['dsn'], $key);
                } catch ( Exception $e ) {
                    echo $e->getMessage ()."\n\n";
                }
            }
        }
    }

    protected function importShops($dsn, $keyIn)
    {
        $DMC = Doctrine_Manager::connection($dsn, 'doctrine_site');
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models');

        $localePath         = ($keyIn == 'en') ? '' : $keyIn.'/';
        $pathToExcelFolder  = UPLOAD_DATA_FOLDER_EXCEL_PATH . strtolower($localePath) . 'excels/import/';

        foreach (glob($pathToExcelFolder."*.xlsx") as $xlsxDocument) {
            echo "\n\n";
            echo 'Importing '.$xlsxDocument;

            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($xlsxDocument);
            $worksheet = $objPHPExcel->getActiveSheet();

            $data =  array();
            $emailArray = array();
            $i = 0;

            $insert = new Doctrine_Collection('Visitor');

            foreach ($worksheet->getRowIterator() as $row) {

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $data[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
                }

                if($i > 0){


                    $email =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['A']);

                    $firstName =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['B']);

                    $lastName =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['C']);


                    $gender = $data[$cell->getRow()]['D'];

                    if( strtoupper($gender) == 'F' || strtoupper($gender) == 'FEMALE')
                    {
                        $gender = 1 ;
                    } else {

                        $gender = 0 ;
                    }


                    $dob =  PHPExcel_Style_NumberFormat::toFormattedString($data[$cell->getRow()]['E'], "YYYY-MM-DD");
                    $dob  = date('Y-m-d',strtotime($dob));



                    $date = PHPExcel_Style_NumberFormat::toFormattedString($data[$cell->getRow()]['F'], "YYYY-MM-DD h:mm:ss");


                    if($date)
                    {
                        $created_at = date('Y-m-d H:i:s',strtotime($date));

                    } else{

                        $created_at = date('Y-m-d H:i:s');
                    }


                    $keywords = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['G']);

                    $emailExist = Doctrine_Core::getTable('Visitor')->findBy('email', $email)->toArray();


                    //echo $firstName.' '.$lastName.' '.$email;
                    //echo "\n\n";

                    if(empty($emailExist)){


                        /**
                         * use email as index to avoid duplicate email error in query
                         * as email would be always unique
                         */
                        $insert[$email]->firstName = $firstName;
                        $insert[$email]->lastName = $lastName;
                        $insert[$email]->created_at = $created_at;
                        $insert[$email]->email = $email;
                        $insert[$email]->gender = $gender ;
                        $insert[$email]->dateOfBirth = $dob ;
                        $insert[$email]->weeklyNewsLetter = 1;
                        $insert[$email]->password = BackEnd_Helper_viewHelper::randomPassword();
                        $insert[$email]->active = 1;

                        $kw = explode(',',$keywords);
                        foreach ($kw as $words){
                            $insert[$email]->keywords[]->keyword = $words;
                        }

                    } else {


                        $insertKeyword = new Doctrine_Collection('VisitorKeyword');


                        $updateWeekNews = Doctrine_Query::create()->update('Visitor')
                                                                  ->set('weeklyNewsLetter',1)
                                                                  ->set('firstName','?' , $firstName )
                                                                  ->set('lastName', '?' ,$lastName)
                                                                  ->set('created_at', '?' , $created_at)
                                                                  ->set('dateOfBirth','?',$dob)
                                                                  ->set('gender', '?', $gender)
                                                                  ->set('active','?',1)
                                                                  ->where('id = '.$emailExist[0]['id'])
                                                                  ->execute();
                        $j = 0;
                        $kw = explode(',',$keywords);
                        foreach ($kw as $words) {

                            $keywordExist = Doctrine_Query::create()->from('VisitorKeyword')
                                                                  ->where("keyword = '". $words ."'")
                                                                  ->andWhere('visitorId = '.$emailExist[0]['id'])
                                                                  ->fetchOne(null,Doctrine::HYDRATE_ARRAY);

                            if(empty($keywordExist)) {
                                $insertKeyword[$j]->keyword = $words;
                                $insertKeyword[$j]->visitorId = $emailExist[0]['id'];
                            }

                            $j++;
                        }

                        $insertKeyword->save();
                    }
                }
                $i++;

            }

            unlink($xlsxDocument);
            $insert->save();
        }
    }
}
