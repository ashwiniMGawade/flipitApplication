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
            $statement = $this->dbh->prepare(self::getSqlQuery());
            if ($statement->execute()) {
                while ($offer = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $offerDates = self::getOfferDates($offer['startdate'], $offer['enddate'], $offer['created_at']);
                    $offerTitle = self::getOfferTitle($offer['title']);
                    $offerExtended = self::getOfferYesNoOptions($offer['extendedoffer']);
                    $offerExclusive = self::getOfferYesNoOptions($offer['exclusivecode']);
                    $offerEditor = self::getOfferYesNoOptions($offer['editorpicks']);
                    $offerUserGenerated = self::getOfferYesNoOptions($offer['userGenerated']);
                    $offerApproved = self::getOfferYesNoOptions($offer['approved']);
                    $offerOffline = self::getOfferYesNoOptions($offer['offline']);
                    $offerDiscountType = self::getOfferDiscountType($offer['discounttype']);
                    $offerVisability = self::getOfferVisability($offer['visability']);
                    $offerClickouts = self::getOfferClickouts($offer['Count']);
                    $offerAuthorName = self::getOfferAuthorName($offer['authorName']);
                    $offerCouponCode = self::getOfferCouponCode($offer['couponcode']);
                    $offerRefUrl = self::getOfferRefUrl($offer['refurl']);
                    $offerShopData = self::getOfferShopData($offer['shopName'], $offer['shopDeeplink']);
                    $offerTermsAndConditions = self::getOfferTermsAndConditions($offer['terms']);
                    $offerInformation = array(
                        $offerTitle,
                        $offerShopData['shopName'],
                        $offerDiscountType,
                        $offerVisability,
                        $offerExtended,
                        $offerDates['startDate'],
                        $offerDates['endDate'],
                        $offerClickouts,
                        $offerAuthorName,
                        $offerCouponCode,
                        $offerRefUrl,
                        $offerExclusive,
                        $offerEditor,
                        $offerUserGenerated,
                        $offerApproved,
                        $offerOffline,
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

    protected static function getSqlQuery()
    {
        $sqlQuery = '
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
        return $sqlQuery;
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

    protected static function getOfferYesNoOptions($columnValue)
    {
        if ($columnValue == true) {
            $value = 'Yes';
        } else {
            $value = 'No';
        }
        return $value;
    }

    protected static function getOfferTitle($offerTitle)
    {
        if ($offerTitle == '' || $offerTitle == 'undefined'
            || $offerTitle == null || $offerTitle == '0') {
            $title = '';
        } else {
            $title = $offerTitle;
        }
        return $title;
    }
    
    protected static function getOfferDiscountType($offerDiscountType)
    {
        $offerType = '';
        if ($offerDiscountType == 'CD') {
            $offerType = 'Coupon';
        } elseif ($offerDiscountType == 'SL') {
            $offerType = 'Sale';
        } else {
            $offerType ='Printable';
        }
        return $offerType;
    }

    protected static function getOfferVisability($offerVisability)
    {
        if ($offerVisability == 'DE') {
            $visability ='Default';
        } else {
            $visability ='Members';
        }
        return $visability;
    }
    
    protected static function getOfferClickouts($offerClickouts)
    {
        $clickouts = $offerClickouts;
        return $clickouts;
    }
        
    protected static function getOfferAuthorName($offerAuthorName)
    {
        if (isset($offerAuthorName)) {
            $author = $offerAuthorName;
        } else {
            $author = '';
        }
        return $author;
    }

    protected static function getOfferCouponCode($offerCouponCode)
    {
        if ($offerCouponCode == '' || $offerCouponCode == 'undefined'
            || $offerCouponCode == null) {
            $couponCode = '';
        } else {
            $couponCode = $offerCouponCode;
        }
        return $couponCode;
    }

    protected static function getOfferRefUrl($offerRefUrl)
    {
        if ($offerRefUrl == '' || $offerRefUrl == 'undefined'
                || $offerRefUrl == null) {
            $refUrl = '';
        } else {
            $refUrl = $offerRefUrl;
        }
        return $refUrl;
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
