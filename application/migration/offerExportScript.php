<?php
class OfferExport
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
            if ($key != 'imbull') {
                try {
                    $this->exportOffers($connection ['dsn'], $key);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    protected function exportOffers($dsn, $keyIn)
    {
        try {
            $this->dbh = CommonMigrationFunctions::connectionToPDO($dsn);
            $pathToTempExcelFolder = CommonMigrationFunctions::pathToTempExcelFolder($keyIn);
            $locale = $keyIn == 'en' ? "-NL" : "-".strtoupper($keyIn);
            $offerFile = $pathToTempExcelFolder . "offerList".$locale.".csv";
            $fileOpen = fopen($offerFile, 'w');
            print "Parse Offers data and save it into excel file\n";
            $currentDateAndTime = array('Genration Date and Time', date('Y-m-d H:i:s'));
            fputcsv($fileOpen, $currentDateAndTime, ';');
            $headers = array(
                'Title',
                'Shop',
                'Type',
                'Visibility',
                'Extended',
                'Start',
                'End',
                'Clickouts',
                'Author',
                'Coupon Code',
                'Ref URL',
                'Exclusive',
                'Editor Picks',
                'User Generated',
                'Approved',
                'Offline',
                'Created At',
                'Deeplink',
                'Terms & Conditions'
            );
            fputcsv($fileOpen, $headers, ';');
            $sql ='
                SELECT o.*,
                s.name as shopName,
                s.deeplink as shopDeeplink,
                t.content as terms,
                (SELECT COUNT(*) FROM view_count v WHERE (v.offerid = o.id)) AS Count
                FROM offer o 
                LEFT JOIN shop s ON o.shopid = s.id LEFT JOIN term_and_condition t ON o.id = t.offerid
                WHERE (o.deleted = 0 AND o.usergenerated= 0)
                ORDER BY o.id DESC
            ';
            $stmt = $this->dbh->prepare($sql);
            if ($stmt->execute()) {
                while ($offer = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //echo "<pre>";print_r($offer);die;
                    $title = '';
                    if ($offer['title'] == '' || $offer['title'] == 'undefined'
                        || $offer['title'] == null || $offer['title'] == '0') {
                        $title = '';
                    } else {
                        $title = $offer['title'];
                    }
                    $shopName = '';
                    if ($offer['shopName'] == ''
                        || $offer['shopName'] == 'undefined'
                        || $offer['shopName'] == null
                        || $offer['shopName'] == '0') {
                        $shopName = '';
                    } else {
                        $shopName = $offer['shopName'];
                    }
                    $offerType = '';
                    if ($offer['discounttype'] == 'CD') {
                        $offerType = 'Coupon';
                    } elseif ($offer['discounttype'] == 'SL') {
                        $offerType = 'Sale';
                    } else {
                        $offerType ='Printable';
                    }
                    $visability = '';
                    if ($offer['visability'] == 'DE') {
                        $visability ='Default';
                    } else {
                        $visability ='Members';
                    }
                    $extended = '';
                    if ($offer['extendedoffer'] == true) {
                        $extended = 'Yes';
                    } else {
                        $extended = 'No';
                    }
                    $startDate = date("d-m-Y", strtotime($offer['startdate']));
                    $endDate = date("d-m-Y", strtotime($offer['enddate']));
                    $clickouts = $offer['Count'];
                    $author = '';
                    if (isset($offer['authorName'])) {
                        $author = $offer['authorName'];
                    } else {
                        $author = '';
                    }
                    $couponCode = '';
                    if ($offer['couponcode'] == '' || $offer['couponcode'] == 'undefined'
                        || $offer['couponcode'] == null) {
                        $couponCode = '';
                    } else {
                        $couponCode = $offer['couponcode'];
                    }
                    $refUrl = '';
                    if ($offer['refurl'] == '' || $offer['refurl'] == 'undefined'
                            || $offer['refurl'] == null) {
                        $refUrl = '';
                    } else {
                        $refUrl = $offer['refurl'];
                    }
                    $exclusive = '';
                    if ($offer['exclusivecode'] == true) {
                        $exclusive = 'Yes';
                    } else {
                        $exclusive = 'No';
                    }
                    $editor = '';
                    if ($offer['editorpicks'] == true) {
                        $editor = 'Yes';
                    } else {
                        $editor = 'No';
                    }
                    $usergenerated = '';
                    if ($offer['userGenerated'] == true) {
                        $usergenerated = 'Yes';
                    } else {
                        $usergenerated = 'No';
                    }
                    $approved = '';
                    if ($offer['approved'] == true) {
                        $approved = 'Yes';
                    } else {
                        $approved = 'No';
                    }
                    $offline = '';
                    if ($offer['offline'] == true) {
                        $offline = 'Yes';
                    } else {
                        $offline = 'No';
                    }
                    $created_at = '';
                    if ($offer['created_at'] == '' || $offer['created_at'] == 'undefined'
                        || $offer['created_at'] == null) {
                        $created_at = '';
                    } else {
                        $created_at = date("d-m-Y", strtotime($offer['created_at']));
                    }
                    $deeplink = '';
                    if ($offer['shopDeeplink'] == '' || $offer['shopDeeplink'] == 'undefined'
                        || $offer['shopDeeplink'] == null) {
                        $deeplink = '';
                    } else {
                        $deeplink = $offer['shopDeeplink'];
                    }
                    $termsAndConditions = '';
                    if (!empty($offer['terms'])) {
                        $termsAndConditions = $offer['terms'][0]['content'];
                    }
                    $offerInformation = array(
                        $title,
                        $shopName,
                        $offerType,
                        $visability,
                        $extended,
                        $startDate,
                        $endDate,
                        $clickouts,
                        $author,
                        $couponCode,
                        $refUrl,
                        $exclusive,
                        $editor,
                        $usergenerated,
                        $approved,
                        $offline,
                        $created_at,
                        $deeplink,
                        $termsAndConditions
                    );
                    fputcsv($fileOpen, $offerInformation, ';');
                }
            }
            echo "\n $keyIn - Offers have been exported successfully!!!";
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
new OfferExport();
