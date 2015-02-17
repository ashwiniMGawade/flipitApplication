<?php
/**
 * Script for exporting the shops
 *
 * @author Surinderpal Singh
 * edit by Daniel (to csv and refactored the code)
 *
 */

class VisitorExport
{
    protected $_localePath  = '/';
    protected $_trans       = null;
    private $dbh            = null;

    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');

        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        foreach ($connections as $key => $connection) {
            // check if database is a site
            if ($key != 'imbull') {
                try {
                    $this->exportVisitors($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function exportVisitors($dsn, $keyIn)
    {
        try {

            $this->dbh = CommonMigrationFunctions::connectionToPDO($dsn);
            $pathToTempExcelFolder = CommonMigrationFunctions::pathToTempExcelFolder($keyIn);
            $locale = $keyIn == 'en' ? "-NL" : "-".strtoupper($keyIn);
            $visitorFile    = $pathToTempExcelFolder . "visitorList".$locale.".csv";
            $fp             = fopen($visitorFile, 'w');

            print "Parse visitors data and save it into excel file\n";

            $currentDateAndTime = array('Genration Date and Time', date('Y-m-d H:i:s'));
            fputcsv($fp, $currentDateAndTime, ';');

            $headers = array(
                'Name',
                'Email',
                'Gender',
                'DOB',
                'Postal Code',
                'Weekly Newsletter',
                'Fashion Newsletter',
                'Travel Newsletter',
                'Code Alert',
                'Active',
                'Keyword',
                'Favorite Shops',
                'Registration Date'
            );
            fputcsv($fp, $headers, ';');

            $sql ='
                SELECT
                v.id,
                v.firstname,
                v.lastname,
                v.email,
                v.gender,
                v.dateofbirth,
                v.postalcode,
                v.weeklynewsletter,
                v.fashionnewsletter,
                v.travelnewsletter,
                v.codealert,
                v.currentlogin,
                v.active,
                v.created_at,
                (SELECT visitor_keyword.keyword
                        FROM visitor_keyword
                        WHERE visitor_keyword.visitorId = v.id ORDER BY visitor_keyword.keyword DESC LIMIT 1) AS keywords,

                (SELECT GROUP_CONCAT(shop.name)
                        FROM favorite_shop
                        LEFT JOIN shop ON favorite_shop.shopId = shop.id
                        WHERE favorite_shop.visitorId = v.id) AS favoShops

                FROM visitor v
                WHERE (v.deleted = 0)
                ORDER BY v.id DESC
            ';
            $stmt = $this->dbh->prepare($sql);
            if ($stmt->execute()) {
                while ($visitor = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $dob = ($visitor['dateofbirth'] != 'undefined'
                            || $visitor['dateofbirth'] != null
                            || $visitor['dateofbirth'] != '' ) ? $visitor['dateofbirth'] : '';

                    $postal = ($visitor['postalcode'] != 'undefined'
                            || $visitor['postalcode'] != null
                            || $visitor['postalcode'] != '' ) ? $visitor['postalcode'] : '';

                    $name           = $visitor['firstname'] . " " . $visitor['lastname'];
                    $gender         = ($visitor['gender'] == 0) ? 'Male': 'Female';
                    $weekNews       = ($visitor['weeklynewsletter'] == 1 ) ? 'Yes' : 'No';
                    $fashionNews    = ($visitor['fashionnewsletter'] == 1 ) ? 'Yes' : 'No';
                    $travelNews     = ($visitor['travelnewsletter'] == 1 ) ? 'Yes' : 'No';
                    $codeAlert      = ($visitor['codealert'] == 1 ) ? 'Yes' : 'No';
                    $active         = ($visitor['active'] == 1 ) ? 'Yes' : 'No';

                    $visitor = array(
                        $name,
                        $visitor['email'],
                        $gender,
                        $dob,
                        $postal,
                        $weekNews,
                        $fashionNews,
                        $travelNews,
                        $codeAlert,
                        $active,
                        $visitor['keywords'],
                        $visitor['favoShops'],
                        $visitor['created_at'],
                    );

                    fputcsv($fp, $visitor, ';');
                }
            }

            echo "\n $keyIn - Visitors have been exported successfully!!!";

            if ($keyIn == 'en') {
                $keyIn = 'excels';
            }

            CommonMigrationFunctions::copyDirectory(UPLOAD_EXCEL_TMP_PATH.$keyIn, UPLOAD_DATA_FOLDER_EXCEL_PATH.$keyIn);
            CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$keyIn);

        } catch (Exception $e) {
            echo $e;
        }
    }
}
new VisitorExport();
