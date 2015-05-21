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
            $statement = $this->dbh->prepare($sql);
            if ($statement->execute()) {
                while ($offer = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $offerDates = self::getOfferDates($offer['startdate'], $offer['enddate'], $offer['created_at']);
                    $offerYesNoOptions = self::getOfferYesNoOptions($offer);
                    $offerCommonData = self::getOfferCommonData($offer);
                    $offerShopData = self::getOfferShopData($offer['shopName'], $offer['shopDeeplink']);
                    $offerTermsAndConditions = self::getOfferTermsAndConditions($offer['terms']);
                    $offerInformation = array(
                        $offerCommonData['title'],
                        $offerShopData['shopName'],
                        $offerCommonData['offerType'],
                        $offerCommonData['visability'],
                        $offerYesNoOptions['extended'],
                        $offerDates['startDate'],
                        $offerDates['endDate'],
                        $offerCommonData['clickouts'],
                        $offerCommonData['author'],
                        $offerCommonData['couponCode'],
                        $offerCommonData['refUrl'],
                        $offerYesNoOptions['exclusive'],
                        $offerYesNoOptions['editor'],
                        $offerYesNoOptions['usergenerated'],
                        $offerYesNoOptions['approved'],
                        $offerYesNoOptions['offline'],
                        $offerDates['createdAt'],
                        $offerShopData['deeplink'],
                        $offerTermsAndConditions['termsAndConditions']
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

    protected static function getOfferDates($startDate, $endDate, $createdAtDate)
    {
        $startDate = date("d-m-Y", strtotime($startDate));
        $endDate = date("d-m-Y", strtotime($endDate));
        if ($createdAtDate == '' || $createdAtDate == 'undefined'
            || $createdAtDate == null) {
            $createdAt = '';
        } else {
            $createdAt = date("d-m-Y", strtotime($createdAtDate));
        }
        return array('startDate'=> $startDate, 'endDate'=>$endDate, 'createdAt'=>$createdAt);
    }

    protected static function getOfferYesNoOptions($offer)
    {
        if ($offer['extendedoffer'] == true) {
            $extended = 'Yes';
        } else {
            $extended = 'No';
        }
        if ($offer['exclusivecode'] == true) {
            $exclusive = 'Yes';
        } else {
            $exclusive = 'No';
        }
        if ($offer['editorpicks'] == true) {
            $editor = 'Yes';
        } else {
            $editor = 'No';
        }
        if ($offer['userGenerated'] == true) {
            $usergenerated = 'Yes';
        } else {
            $usergenerated = 'No';
        }
        if ($offer['approved'] == true) {
            $approved = 'Yes';
        } else {
            $approved = 'No';
        }
        if ($offer['offline'] == true) {
            $offline = 'Yes';
        } else {
            $offline = 'No';
        }
        return array('extended'=>$extended, 'exclusive'=>$exclusive, 'editor'=>$editor,
            'usergenerated'=>$usergenerated, 'approved'=>$approved, 'offline'=>$offline);
    }

    protected static function getOfferCommonData($offer)
    {
        if ($offer['title'] == '' || $offer['title'] == 'undefined'
            || $offer['title'] == null || $offer['title'] == '0') {
            $title = '';
        } else {
            $title = $offer['title'];
        }
        $offerType = '';
        if ($offer['discounttype'] == 'CD') {
            $offerType = 'Coupon';
        } elseif ($offer['discounttype'] == 'SL') {
            $offerType = 'Sale';
        } else {
            $offerType ='Printable';
        }
        if ($offer['visability'] == 'DE') {
            $visability ='Default';
        } else {
            $visability ='Members';
        }
        $clickouts = $offer['Count'];
        if (isset($offer['authorName'])) {
            $author = $offer['authorName'];
        } else {
            $author = '';
        }
        if ($offer['couponcode'] == '' || $offer['couponcode'] == 'undefined'
            || $offer['couponcode'] == null) {
            $couponCode = '';
        } else {
            $couponCode = $offer['couponcode'];
        }
        if ($offer['refurl'] == '' || $offer['refurl'] == 'undefined'
                || $offer['refurl'] == null) {
            $refUrl = '';
        } else {
            $refUrl = $offer['refurl'];
        }
        return array('title'=>$title, 'offerType'=>$offerType, 'visability'=>$visability,
            'clickouts'=>$clickouts, 'author'=>$author, 'couponCode'=>$couponCode, 'refUrl'=>$refUrl);
    }

    protected static function getOfferShopData($shopName, $shopDeepLink)
    {
        if ($shopName == ''
            || $shopName == 'undefined'
            || $shopName == null
            || $shopName == '0') {
            $shopName = '';
        } else {
            $shopName = $shopName;
        }
        if ($shopDeepLink == '' || $shopDeepLink == 'undefined'
            || $shopDeepLink == null) {
            $deeplink = '';
        } else {
            $deeplink = $shopDeepLink;
        }
        return array('shopName'=>$shopName, 'deeplink'=>$deeplink);
    }
    
    protected static function getOfferTermsAndConditions($terms)
    {
        $termsAndConditions = '';
        if (!empty($terms)) {
            $termsAndConditions = $terms;
        }
        return array('termsAndConditions'=>$termsAndConditions);
    }
}
new OfferExport();
