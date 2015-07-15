<?php
/**
 * Script for importing visitors
 *
 * @author Daniel
 */
new VisitorImport();
class VisitorImport
{

    protected $_localePath  = '/';
    protected $_trans       = null;
    private $dbh            = null;

    function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        CommonMigrationFunctions::setTimeAndMemoryLimit();
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->importVisitors($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function importVisitors($dsn, $keyIn)
    {
        $doctrineSiteDbConnection = CommonMigrationFunctions::getDoctrineSiteConnection($dsn);
        $manager = CommonMigrationFunctions::loadDoctrineModels();

        $localePath               = ($keyIn == 'en') ? '' : $keyIn.'/';
        $pathToExcelImportFolder  = UPLOAD_DATA_FOLDER_EXCEL_PATH . strtolower($localePath) . 'excels/import/';
        
        foreach (glob($pathToExcelImportFolder."*.xlsx") as $xlsxDocument) {
            
            $logContent =
                date('Y-m-d H:m:i').' - import file: '.basename($xlsxDocument).' for locale: '.strtoupper($keyIn)."\n";

            try {
                $objReader      = PHPExcel_IOFactory::createReader('Excel2007');
                $objPHPExcel    = $objReader->load($xlsxDocument);
                $worksheet      = $objPHPExcel->getActiveSheet();

                $data                   = array();
                $emailArray             = array();
                $countNewVisitors       = 0;
                $countUpdatedVisitors   = 0;

                $insert = new Doctrine_Collection('Visitor');
                foreach ($worksheet->getRowIterator() as $row) {

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    foreach ($cellIterator as $cell) {
                        $data[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
                    }

                    $email      = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['A']);
                    
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $firstName  = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['B']);
                        $lastName   = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['C']);
                        $gender     =
                            (strtoupper($data[$cell->getRow()]['D']) == 'F'
                            || strtoupper($data[$cell->getRow()]['D']) == 'FEMALE')
                            ? 1
                            : 0;
                        $dob        =
                            PHPExcel_Style_NumberFormat::toFormattedString($data[$cell->getRow()]['E'], "YYYY-MM-DD");
                        $dob        = date('Y-m-d', strtotime($dob));
                        $date       =
                            PHPExcel_Style_NumberFormat::toFormattedString(
                                $data[$cell->getRow()]['F'],
                                "YYYY-MM-DD h:mm:ss"
                            );
                        $created_at = ($date) ? date('Y-m-d H:i:s', strtotime($date)) : date('Y-m-d H:i:s');
                        $keywords   = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['G']);
                        $emailExist = Doctrine_Core::getTable('Visitor')->findBy('email', $email)->toArray();
                        $keywordsArray = explode(',', $keywords);
                        if (empty($emailExist)) {
                            $countNewVisitors++;
                            $insert[$email]->firstName = $firstName;
                            $insert[$email]->lastName = $lastName;
                            $insert[$email]->created_at = $created_at;
                            $insert[$email]->email = $email;
                            $insert[$email]->gender = $gender ;
                            $insert[$email]->dateOfBirth = $dob ;
                            $insert[$email]->weeklyNewsLetter = 1;
                            $insert[$email]->password = BackEnd_Helper_viewHelper::randomPassword();
                            $insert[$email]->active = 1;
                            $insert[$email]->currentLogIn = '0000-00-00';
                            $insert[$email]->lastLogIn = '0000-00-00';
                            $insert[$email]->active_codeid = '';
                            foreach ($keywordsArray as $words) {
                                $insert[$email]->keywords[]->keyword = $words;
                            }

                        } else {
                            $countUpdatedVisitors++;
                            $updateVisitor = Doctrine_Core::getTable('Visitor')->find($emailExist[0]['id']);
                            $updateVisitor->firstName = $firstName;
                            $updateVisitor->lastName = $lastName;
                            $updateVisitor->created_at = $created_at;
                            $updateVisitor->gender = $gender;
                            $updateVisitor->dateOfBirth = $dob;
                            $updateVisitor->active = 1;
                            $updateVisitor->save();

                            $keywordCounter     = 0;
                            $insertKeyword      = new Doctrine_Collection('VisitorKeyword');
                            foreach ($keywordsArray as $words) {
                                $keywordExist = Doctrine_Query::create()
                                    ->from('VisitorKeyword')
                                    ->where("keyword = '". $words ."'")
                                    ->andWhere('visitorId = '.$emailExist[0]['id'])
                                    ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

                                if (empty($keywordExist)) {
                                    $insertKeyword[$keywordCounter]->keyword = $words;
                                    $insertKeyword[$keywordCounter]->visitorId = $emailExist[0]['id'];
                                }
                                $keywordCounter++;
                            }
                            $insertKeyword->save();
                        }
                    }
                }

                $logContent .= 'Total new users: '.$countNewVisitors."\n";
                $logContent .= 'Total updated users: '.$countUpdatedVisitors."\n\n";
                unlink($xlsxDocument);
                $insert->save();
            } catch (Exception $e) {
                $logContent .= 'Error: '.$e."\n";
            }
            echo $logContent;
            $this->writeLogFile($logContent, $pathToExcelImportFolder);
        }
        $manager->closeConnection($doctrineSiteDbConnection);
    }

    private function writeLogFile($logContent, $pathToExcelImportFolder)
    {
        $filename = $pathToExcelImportFolder.'log.txt';
        $file_content = file_exists($filename) ? file_get_contents($filename) : '';
        file_put_contents($filename, $logContent . "\n" . $file_content);
    }
}
