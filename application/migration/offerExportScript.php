<?php
class OfferExport
{
    protected $_localePath  = '/';
    protected $_trans;
    private $dbh;
    private $filePath;
    private $locale;
    public function __construct()
    {
        require_once('ConstantForMigration.php');
        require_once('CommonMigrationFunctions.php');
        $connections = CommonMigrationFunctions::getAllConnectionStrings();
        foreach ($connections as $key => $connection) {
            if ($key != 'imbull') {
                try {
                    $this->locale = $key == 'en' ? "-NL" : "-".strtoupper($key);
                    $pathToTempExcelFolder = CommonMigrationFunctions::pathToTempExcelFolder($this->locale);
                    $this->filePath = $pathToTempExcelFolder . "offerList".$this->locale.".csv";
                    $this->runner($connection['dsn']);
                } catch (Exception $e) {
                    echo $e->getMessage()."\n\n";
                }
            }
        }
    }

    public function runner($dsn)
    {
        $filePointer = fopen($this->filePath, 'w');
        echo "Exporting Offers list into CSV for " . $this->locale . "\n";
        $this->dbh = CommonMigrationFunctions::connectionToPDO($dsn);
        $this->initOfferExportFile($filePointer);
        $this->getOffers($filePointer);
        $this->moveFilestoDataFolder();
        fclose($filePointer);
    }

    private function initOfferExportFile($filePointer)
    {
        $this->timeStampOfferExport($filePointer);
        $this->setupColumns($filePointer);
    }

    private function timeStampOfferExport($filePointer)
    {
        $currentDateAndTime = array('Genration Date and Time', date('Y-m-d H:i:s'));
        $this->writeToFile($currentDateAndTime, $filePointer);
    }

    private function setupColumns($filePointer)
    {
        $offerExportHeader = array(
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
        $this->writeToFile($offerExportHeader, $filePointer);
    }

    private function writeToFile($content, $filePointer)
    {
        fputcsv($filePointer, $content, ';');
        echo ".";
    }

    private function getOffers($filePointer)
    {
        $statement = $this->dbh->prepare($this->getSqlQuery());
        if ($statement->execute()) {
            while ($offer = $statement->fetch(PDO::FETCH_ASSOC)) {
                $offerInformation = $this->constructOffers($offer);
                $this->writeToFile($offerInformation, $filePointer);
            }
        }
    }

    private function constructOffers($offer)
    {
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
        $offerShopName = self::getOfferShopName($offer['shopName']);
        $offerShopDeepLink = self::getOfferShopDeepLink($offer['shopDeeplink']);
        $offerTermsAndConditions = self::getOfferTermsAndConditions($offer['terms']);
        $offerInformation = array(
            $offerTitle,
            $offerShopName,
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
            $offerShopDeepLink,
            $offerTermsAndConditions['termsAndConditions']
        );
        return $offerInformation;
    }

    private function moveFilestoDataFolder()
    {
        if ($this->locale == 'en') {
            $this->locale = 'excels';
        }
        CommonMigrationFunctions::copyDirectory(UPLOAD_EXCEL_TMP_PATH.$this->locale, UPLOAD_DATA_FOLDER_EXCEL_PATH.$this->locale);
        CommonMigrationFunctions::deleteDirectory(UPLOAD_EXCEL_TMP_PATH.$this->locale);
        return true;
    }

    private function getSqlQuery()
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

    protected static function getOfferShopName($shopName)
    {
        if ($shopName == ''
            || $shopName == 'undefined'
            || $shopName == null
            || $shopName == '0') {
            $shopName = '';
        } else {
            $shopName = $shopName;
        }
        return $shopName;
    }

    protected static function getOfferShopDeepLink($shopDeepLink)
    {
        if ($shopDeepLink == '' || $shopDeepLink == 'undefined'
            || $shopDeepLink == null) {
            $deeplink = '';
        } else {
            $deeplink = $shopDeepLink;
        }
        return $deeplink;
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
