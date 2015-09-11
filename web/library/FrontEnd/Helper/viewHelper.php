<?php
use \HTMLPurifier;
use \Core\Domain\Factory\GuestFactory;
use \Core\Domain\Entity\Offer;

class FrontEnd_Helper_viewHelper
{
    public static function writeLog($message, $logfile = '')
    {
        /*$requestTime = Zend_Controller_Front::getInstance()->getRequest()->getServer('REQUEST_TIME');
        $remoteAddress = Zend_Controller_Front::getInstance()->getRequest()->getServer('REMOTE_ADDR');
        if ($logfile == '') {
            $logDir = APPLICATION_PATH . "../logs/";
            if (!file_exists($logDir)) {
                mkdir($logDir, 0776, true);
            }
            $fileName = "default";
            $logfile = $logDir . $fileName;
        }
        if (($time = $requestTime) == '') {
            $time = time();
        }
        if (($remote_addr = $remoteAddress) == '') {
            $remote_addr = "REMOTE_ADDR_UNKNOWN";
        }
        $date = date("M d, Y H:i:s", $time);
        if ($fileData = fopen($logfile, "a")) {
            $str = <<<EOD
            $date; $remote_addr; $message
EOD;
            $result = fwrite($fileData, $str .PHP_EOL);
            fclose($fileData);
            if ($result > 0) {
                return array('status' => true);
            } else {
                return array('status' => false, 'message' => 'Unable to write to '.$logfile.'!');
            }
        } else {
            return array('status' => false, 'message' => 'Unable to open log '.$logfile.'!');
        }*/
    }

    public static function getShopCouponCode($type, $limit, $shopId = 0)
    {
        $shopCouponCodes = '';
        switch (strtolower($type)) {
            case 'expired':
                $shopCouponCodes = \KC\Repository\Offer::getExpiredOffers($type, $limit, $shopId);
                break;
            case 'latestupdates':
                $shopCouponCodes = \KC\Repository\Offer::getLatestUpdates($type, $limit, $shopId);
                break;
            default:
                break;
        }
        return $shopCouponCodes;
    }

    public static function generateCononical($permalink)
    {
        preg_match("/^[\d]+$/", $permalink, $matches);
        if (isset($matches[0]) && intval($matches[0]) > 0) {
            $permalink = explode('/'.$matches[0], $permalink);
            $permalink = $permalink[0];
        }
        $permalinkWithoutQueryString = explode('?', $permalink);
        if (!empty($permalinkWithoutQueryString)) {
            $permalink = $permalinkWithoutQueryString[0];
        }
        if (LOCALE!='en') {
            $frontEndControllers = \Zend_Controller_Front::getInstance();
            $frontEndControllerDirectory = $frontEndControllers->getControllerDirectory();
            $moduleNames = array_keys($frontEndControllerDirectory);
            $moduleNameOrPermalink = explode('/', $permalink);
            if (in_array($moduleNameOrPermalink[0], $moduleNames)) {
                $permalink = ltrim($permalink, $moduleNameOrPermalink[0]);
                $permalink = ltrim($permalink, '/');
            }
        }
        return rtrim($permalink, '/');
    }
    
    public static function getFooterData()
    {
        return \KC\Repository\Footer::getFooter();
    }

    public static function getHeadMeta($headMetaValue)
    {
        $domainName = HTTP_HOST;
        if ($domainName == "www.kortingscode.nl") {
            $site_name = "Kortingscode.nl";
        } else {
            $site_name = "Flipit.com";
        }
        $locale = LOCALE != '' ? '/'.LOCALE : '';
        $chainLocale = \KC\Repository\Website::getWebsiteDetails('', strtolower($site_name).$locale);
        $chainLocale = !empty($chainLocale[0]) ? $chainLocale[0] : '';
        $chainLocaleChain = !empty($chainLocale['chain']) ? $chainLocale['chain'] : '';
        $ogCustomLocale = explode('_', $chainLocaleChain);
        $ogCustomLocale = isset($ogCustomLocale[1]) ? $ogCustomLocale[1] : $ogCustomLocale[0];
        $ogLocale = !empty($chainLocale) && $chainLocale['chain'] != '' ?
            strtolower($ogCustomLocale) : $headMetaValue->facebookLocale;

        $socialMediaValue =
            array(
                'og:title'=>self::replaceStringVariable($headMetaValue->facebookTitle),
                'og:type'=>'website',
                'og:url'=> $headMetaValue->facebookShareUrl,
                'og:description'=>self::replaceStringVariable(
                    $headMetaValue->facebookDescription
                ),
                'og:locale'=>$ogLocale,
                'og:image'=>$headMetaValue->facebookImage,
                'og:site_name'=>$site_name,
                'twitter:description'=>self::replaceStringVariable(
                    $headMetaValue->twitterDescription
                ),
                'twitter:site'=>$site_name
        );
        return $socialMediaValue;
    }
    
    public static function alphabetList()
    {
        $letterOrNumber = 0;
        $alphabetList = "<ul class='alphabet' id='alphabet'><li><a id='0' class='' href='#0-9'>0-9</a></li>";
        foreach (range('A', 'Z') as $letterOrNumber) {
            $lastAlphabetClass = $letterOrNumber=='Z' ? 'last' : '';
            $alphabetList .=
                "<li><a id='" . $letterOrNumber . "'  
                href='#".strtolower($letterOrNumber)."' class='".$lastAlphabetClass."'>$letterOrNumber</a>
                </li>";
        }
        $alphabetList .="</ul>";
        return $alphabetList;
    }


    public static function renderPagination(
        $totalRecordsForPagination,
        $paginationParameter,
        $itemCountPerPage,
        $paginationRange = 3
    ) {
        $currentPageNumber = !empty($paginationParameter['page']) ? $paginationParameter['page'] : '1';
        $pagination = \Zend_Paginator::factory($totalRecordsForPagination);
        $pagination->setCurrentPageNumber($currentPageNumber);
        $pagination->setItemCountPerPage($itemCountPerPage);
        $pagination->setPageRange($paginationRange);
        return $pagination;
    }

    public static function getPagnation($pageCount, $currentPage, $redirector, $pagesInRange, $nextPage)
    {
        $requestUri = \Zend_Controller_Front::getInstance()->getRequest()->getServer('REQUEST_URI');
        $permalink = ltrim($requestUri, '/');
        $permalink = rtrim($permalink, '/');
        $permalinkWithoutQueryString = explode('?', $permalink);
        if (!empty($permalinkWithoutQueryString)) {
            $permalink = $permalinkWithoutQueryString[0];
        }
        $canocalUrl = $permalink;
        preg_match("/[^\/]+$/", $permalink, $permalinkMatches);
        if (intval($permalinkMatches[0]) > 0 && intval($permalinkMatches[0]) < 10) :
            if (intval($permalinkMatches[0]) > intval($pageCount)) :
                $permalink = explode('/'.$permalinkMatches[0], $permalink);
                $permalink = $permalink[0];
                header('location:'. HTTP_PATH.$permalink);
            elseif(intval($pageCount) == 1) :
                $baseLink = explode('/', $permalink);
                if (intval($baseLink[1]) == 1) :
                    header('location:'. HTTP_PATH.$baseLink[0]);
                    exit;
                endif;
                throw new Exception('Error occured');
                exit;
            endif;
            $permalink = explode('/'.$permalinkMatches[0], $permalink);
            if ($permalinkMatches[0]==1) {
                header('location:'. HTTP_PATH.$permalink[0]);
            }
            $permalink = $permalink[0];
        elseif (intval($permalinkMatches[0]) > 10) :
            throw new Exception('Error occured');
        else:
            $permalink = $permalink;
        endif;
        $permalinkAfterQueryString = explode('?', $permalink);
        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $view->headLink(array('rel' => 'canonical', 'href' => HTTP_PATH . strtolower($canocalUrl)));
        if ($pageCount > 1) :
            if ($currentPage < 9) :
                if ($currentPage == 1) :
                    $permalinkAfterQueryString = explode('?', $permalink);
                    $permalink = $permalinkAfterQueryString[0];
                endif;
                if ($currentPage+1 <= $pageCount):
                    $view->headLink(array('rel' => 'next', 'href' => HTTP_PATH . $permalink .'/'. ($currentPage + 1)));
                endif;
            endif;
            
            if ($currentPage - 1 != 0) :
                if ($currentPage==2) :
                    $previousPermalink = HTTP_PATH . $permalink;
                else:
                    $previousPermalink = HTTP_PATH . $permalink .'/'. ($currentPage - 1);
                endif;
                $view->headLink(array('rel' => 'prev', 'href' => $previousPermalink));
            endif;

            echo '<ul class="pagination">';
            foreach ($pagesInRange as $pageNumber):
                if ($pageNumber < 10):
                    $pageNumberAfterSlash = '';
                    if ($pageNumber > 1) :
                        $pageNumberAfterSlash = "/".$pageNumber;
                    endif;
                    ?>
                    <li class="<?php echo ($pageNumber == $currentPage) ? "active" : "" ?>">
                        <a href="<?php echo HTTP_PATH . $permalink . $pageNumberAfterSlash; ?>">
                        <?php echo $pageNumber;?> 
                        <?php 
                    if ($pageNumber == $currentPage) : ?>
                        <span class="sr-only">(current)</span>
                        <?php 
                    endif;
                    ?>
                        </a>
                    </li>
                    <?php
                elseif (isset($nextPage) && $pageNumber < 10) : ?>
                    <li class="next">
                        <a href="<?php echo HTTP_PATH . $permalink . $pageNumberAfterSlash ?>">&gt;</a>
                    </li>
                    <?php
                endif;
            endforeach;
            echo "</ul>";
        endif;
    }

    public static function getRequestedDataBySetGetCache(
        $dataKey = '',
        $relatedFunction = '',
        $replaceStringArrayCheck = '1'
    ) {
        if ($relatedFunction['function'] == '') {
            $functionToBeCached = $relatedFunction['parameters'];
        } else {
            $functionToBeCached = call_user_func_array($relatedFunction['function'], $relatedFunction['parameters']);
        }


        $cacheStatusByKey = self::checkCacheStatusByKey($dataKey);
        if ($cacheStatusByKey) {
            if ($replaceStringArrayCheck == '1') {
                $requestedInformation = self::replaceStringArray($functionToBeCached);
            } else {
                $requestedInformation = $functionToBeCached;
            }
            self::setInCache($dataKey, $requestedInformation);
        } else {
            $requestedInformation = self::getFromCacheByKey($dataKey);
        }
        return $requestedInformation;
    }

    public static function viewCounter($type, $eventType, $typeId)
    {
        $clientIP = self::getRealIpAddress();
        $properClientIpAddress = ip2long($clientIP);
        $counterValue = "false";
        switch (strtolower($type)) {
            case 'article':
                $counterValue = self::checkIfThisArticleEntryExists($eventType, $typeId, $properClientIpAddress);
                break;
            case 'shop':
                $counterValue = self::checkIfThisShopEntryExists($eventType, $typeId, $properClientIpAddress);
                break;
            case 'offer':
                $counterValue = self::checkIfThisOfferEntryExists($eventType, $typeId, $properClientIpAddress);
                break;
            default:
                break;
        }
        return $counterValue;
    }

    public static function checkIfThisArticleEntryExists($eventType, $articleId, $clientIp)
    {
        $artcileExistsOrNot = "false";
        switch (strtolower($eventType)) {
            case 'onclick':
                if (\KC\Repository\ArticleViewCount::getArticleClick($articleId, $clientIp) == 0) {
                    \KC\Repository\ArticleViewCount::saveArticleClick($articleId, $clientIp);
                    $artcileExistsOrNot = "true";
                }
                break;
            case 'onload':
                if (\KC\Repository\ArticleViewCount::getArticleOnload($articleId, $clientIp) == 0) {
                    \KC\Repository\ArticleViewCount::saveArticleOnLoad($articleId, $clientIp);
                    $artcileExistsOrNot = "true";
                }
                break;
            default:
                break;
        }
        return $artcileExistsOrNot;
    }
    
    public static function checkIfThisShopEntryExists($eventType, $shopId, $clientIp)
    {
        $resultStatus = "false";
        switch (strtolower($eventType)) {
            case 'onclick':
                if (\KC\Repository\ShopViewCount::getShopClick($shopId, $clientIp) == 0) {
                    \KC\Repository\ShopViewCount::getSaveShopClick($shopId, $clientIp);
                    $resultStatus = "true";
                }
                break;
            case 'onload':
                if (\KC\Repository\ShopViewCount::getShopOnload($shopId, $clientIp) == 0) {
                    \KC\Repository\ShopViewCount::getSaveShopOnload($shopId, $clientIp);
                    $resultStatus = "true";
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }

    public static function checkIfThisOfferEntryExists($eventType, $offerId, $clientIp)
    {
        $resultStatus = "false";
        switch (strtolower($eventType)) {
            case 'onclick':
                $offer = GuestFactory::getOffer()->execute(array('id' => $offerId));
                if ($offer instanceof Offer) {
                    $conditions = array(
                        'viewcount' => $offer,
                        'IP' => $clientIp
                    );
                    $viewCounts = GuestFactory::getViewCounts()->execute($conditions);
                    if (count($viewCounts) == 0) {
                        \KC\Repository\ViewCount::saveOfferClick($offerId, $clientIp);
                        $varnishObj = new \KC\Repository\Varnish();
                        $varnishObj->addUrl(HTTP_PATH_LOCALE . 'offer/offer-view-count?offerId='. $offerId);
                        $resultStatus = "true";
                    }
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }

    public static function getStoreForFrontEnd($storeType, $limit = "")
    {
        $stores = '';
        switch (strtolower($storeType)) {
            case 'popular':
                $stores = \KC\Repository\Shop::getPopularStores($limit);
                break;
            default:
                break;
        }
        return $stores;
    }

    public static function getRealIpAddress()
    {
        $clientIp = \Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_CLIENT_IP');
        $httpXForwardedFor = \Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_X_FORWARDED_FOR');

        if (!empty($clientIp)) {
            $clientIpAddress = $clientIp;
        } else if (!empty($httpXForwardedFor)) {
            $ipRange = $httpXForwardedFor;
            $clientIpAddress = current(array_slice(explode(",", $ipRange), 0, 1));
        } else {
            $clientIpAddress = \Zend_Controller_Front::getInstance()->getRequest()->getServer('REMOTE_ADDR');
        }
        return $clientIpAddress;
    }

    public function getHowToGuidesImage($howToGuideImages)
    {
        $howToGuideImagePath = '';
        $howToGuideImageAltText = '';
        if (!empty($howToGuideImages)) {
            $howToGuideImagePath =
                PUBLIC_PATH_CDN.ltrim($howToGuideImages['path'], "/").$howToGuideImages['name'];
            $howToGuideImageAltText = $howToGuideImages['name'];
        }
        return array(
            'howToGuideImagePath' => $howToGuideImagePath,
            'howToGuideImageAltText' => $howToGuideImageAltText
        );
    }

    public static function getSplashInformation()
    {
        $splash = new \KC\Repository\Splash();
        return $splash->getSplashInformation();
    }

    public static function getCountryNameByLocale($locale)
    {
        $countryName = '';
        if(!empty($locale)) :
            $locale = $locale == 'en' ? 'nl' : $locale;
            $locale = new \Zend_Locale(strtoupper($locale));
            $countries = $locale->getTranslationList('Territory', 'en');
            $countryName = ($countries[$locale->getRegion()]);
        endif;
        return $countryName;
    }

    public function getMetaTags(
        $currentObject,
        $title = '',
        $metaTitle = '',
        $metaDescription = '',
        $permaLink = '',
        $image = '',
        $customHeader = ''
    ) {
        if ($metaTitle == '') {
            $metaTitle = $title;
        }

        $facebookShareUrl = $permaLink != '' ? HTTP_PATH_LOCALE . $permaLink : rtrim(HTTP_PATH_LOCALE, '/');
        $currentObject->view->headTitle(\FrontEnd_Helper_viewHelper::replaceStringVariable($metaTitle));
        $currentObject->view->headMeta()->setName(
            'description',
            \FrontEnd_Helper_viewHelper::replaceStringVariable($metaDescription)
        );
        $currentObject->view->facebookTitle = \FrontEnd_Helper_viewHelper::replaceStringVariable($title);
        $currentObject->view->facebookShareUrl = $facebookShareUrl;
        $currentObject->view->facebookImage = $image;
        $currentObject->view->facebookDescription = \FrontEnd_Helper_viewHelper::replaceStringVariable(
            $metaDescription
        );
        if (LOCALE == '') {
            $facebookLocale = '';
        } else {
            $facebookLocale = LOCALE;
        }
        $currentObject->view->facebookLocale = $facebookLocale;
        $currentObject->view->twitterDescription = \FrontEnd_Helper_viewHelper::replaceStringVariable(
            $metaDescription
        );
        if (isset($customHeader)) {
            $currentObject->view->layout()->customHeader =
                $currentObject->view->layout()->customHeader . $customHeader . "\n";
        }
        return $currentObject;
    }
   
    public static function __link($variable)
    {
        $trans = \Zend_Registry::get('Zend_Translate');
        $variable = $trans->translate(_($variable));
        return $variable;
    }

    public static function __form($variable)
    {
        $trans = \Zend_Registry::get('Zend_Translate');
        $variable = $trans->translate(_($variable));
        return $variable;
    }

    public static function __email($variable)
    {
        $trans = \Zend_Registry::get('Zend_Translate');
        $variable = $trans->translate(_($variable));
        return $variable;
    }

    public static function __translate($variable)
    {
        $translation = new \Transl8_View_Helper_Translate();
        $variable = $translation->translate($variable);
        return $variable;
    }

    public static function getWebsiteLocales($frontend = '')
    {
        $locales = '';
        $websites = \KC\Repository\Website::getAllWebsites();
        foreach ($websites as $website) {
            $spiltWebsite  = explode('/', $website['name']);
            $locale = isset($spiltWebsite[1]) ? $spiltWebsite[1] : "nl";
           
            if ($frontend == 'true') {
                if ($website['status'] == 'online') {
                    $locales[strtoupper($locale)] = $website['name'];
                }
            } else {
                $locales[strtoupper($locale)] = $website['name'];
            }
        }

        return $locales;
    }

    public static function getAllCountriesByLocaleNames($frontend = '')
    {
        $localesList = \Zend_Locale::getLocaleList();
        $websiteLocales = self::getWebsiteLocales($frontend);

        foreach ($localesList as $localeIndex => $localeValue) {
            $localeName = explode('_', $localeIndex);
            $websiteLocale = isset($localeName[1]) ? $localeName[1] : '';
            
            if (array_key_exists($websiteLocale, $websiteLocales)) {
                $locale = new \Zend_Locale($localeIndex);
                $countries = $locale->getTranslationList('Territory', 'en');
                if ($frontend == 'true') {
                    $countriesWithLocales[strtolower($localeName[1])] = ($countries[$locale->getRegion()]);
                } else {
                    $countriesWithLocales[$localeIndex] = $websiteLocales[$websiteLocale] ." ("
                        . ($countries[$locale->getRegion()]) . ")";
                }
            }
        }
        
        return $countriesWithLocales = array_unique($countriesWithLocales);
    }

    public static function getWebsiteName($locale)
    {
        $siteName = $locale != '' ? 'Flipit' : 'Kortingscode';
        return $siteName;
    }
  
    public static function commonfrontendGetCode($type, $limit = 10, $shopId = 0, $userId = "")
    {
        switch (strtolower($type)) {
            case 'all':
                $shopData = \KC\Repository\Offer::getAllOfferOnShop($shopId);
                break;
            case 'topsixoffers':
                $shopData = \KC\Repository\Offer::getAllOfferOnShop($shopId, $limit, false, false, true);
                break;
            case 'popular':
                $shopData = \KC\Repository\Offer::commongetpopularOffers($type, $limit, $shopId, $userId);
                break;
            default:
                break;
        }
        return $shopData;
    }

    public static function checkCacheStatusByKey($key)
    {
        $key = $key. '_' .LOCALE;
        $flag = false;
        $cache = \Zend_Registry::get('cache');
        if (($result = $cache->load($key)) === false) {
            $flag = true;
        }
        return $flag;
    }
   
    public static function getFromCacheByKey($key)
    {
        $key = $key. '_' .LOCALE;
        $cache = \Zend_Registry::get('cache');
        $cache = $cache->load($key);
        return $cache;
    }

    public static function setInCache($key, $data)
    {
        $key = $key. '_' .LOCALE;
        $cache = \Zend_Registry::get('cache');
        $cache->save($data, $key);
    }

    public static function clearCacheByKeyOrAll($key)
    {
        $cache = \Zend_Registry::get('cache');
        if ($key=='all') {
            $cache->clean();
        } else {
            if (! \Zend_Registry::get('db_locale')) {
                $locale = LOCALE;
            } else {
                $locale  = \Zend_Registry::get('db_locale') == 'en' ? '' : \Zend_Registry::get('db_locale');
            }
            $key = $key. '_' .$locale;
            $cache->remove($key);
            $OutPutKey = explode('_', $key);
            $newKey = $OutPutKey[0].'_'.$OutPutKey[1].'_'.'_output'.'_'.$OutPutKey[2];
            $cache->remove($newKey);
        }
    }

    public static function getallrelatedshopsid($shopId)
    {
        $data = \KC\Repository\Offer::commongetallrelatedshopsid($shopId);
        return $data;
    }

    public static function substring($text, $length)
    {
        if (strlen($text)>$length) {
            $text = substr($text, 0, $length).'...';
        }
        return $text;
    }
    
    public static function mbSubstring($text, $length, $encoding = 'utf-8')
    {
        if (strlen($text) > $length) {
            $text = mb_substr($text, 0, $length, $encoding).'...';
        }
        return $text;
    }
   
    public static function gethomeSections($offertype, $limit = "")
    {
        switch ($offertype) {
            case "popular":
                $result = \KC\Repository\PopularCode::gethomePopularvoucherCode($limit);
                break;
            case "newest":
                $result = \KC\Repository\PopularVouchercodes::getNewstoffer($limit);
                break;
            case "category":
                $result = \KC\Repository\Category::getPopularCategories($limit);
                break;
            case "moneySaving":
                $result = \KC\Repository\Articles::getAllArticles($limit);
                break;
            case "asseenin":
                $result = \KC\Repository\SeenIn::getSeenInContent();
                break;
            case "about":
                $status = 1;
                $result = \KC\Repository\About::getAboutContent($status);
                break;
        }
        return $result;
    }
    public static function objectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = self::objectToArray($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }
    public static function replaceKeyword(&$item, $key)
    {
        $item = str_replace(
            array(
                '[month]',
                '[year]',
                '[day]'),
            array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY),
            $item
        );
    }

    public static function replaceStringArray($originalArray)
    {
        
        $originalArray = self::objectToArray($originalArray);
        $obj = new self();
        array_walk_recursive($originalArray, array($obj, 'replaceKeyword'));
        return $originalArray;
    }

    public static function replaceStringVariable($variable)
    {
        $variable = str_replace(
            array('[month]', '[year]', '[day]', '[offers]', '[coupons]', '[accounts]', '[visitors]', '[shops]'),
            array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY,
            \KC\Repository\Dashboard::getDashboardValueToDispaly("total_no_of_offers"),
            \KC\Repository\Dashboard::getDashboardValueToDispaly("total_no_of_shops_online_code"),
            \KC\Repository\Dashboard::getDashboardValueToDispaly("total_no_members"),
            \KC\Repository\Dashboard::getDashboardValueToDispaly("total_no_members"),
            \KC\Repository\Dashboard::getDashboardValueToDispaly("total_no_of_shops_online_code")),
            $variable
        );
        return $variable;
    }

    public static function replaceStringVariableForOfferTitle($variable)
    {
        $variable = str_replace(
            array('[month]', '[year]', '[day]'),
            array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY),
            $variable
        );
        return $variable;
    }

    public static function sanitize($string, $stripTags = true)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', '/tmp');
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($string);
        return $clean_html;
    }

    public static function getDbConnectionDetails()
    {
        $application = new \Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        // bootstrap doctrine
        //$application->getBootstrap()->bootstrap('doctrine');
        $doctrineConnections = $application->getBootstrap()->getOptions('doctrine');
        foreach ($doctrineConnections['doctrine'] as $connection) {
            $dbConnection = $connection;
        }
        $splitDbName = explode('//', $dbConnection);
        $splitDbName = explode('/', $splitDbName[1]);
        $dbName = $splitDbName[1];
        $splitDbPasswordAndHost = explode(':', $splitDbName[0]);
        $splitDbPassword = explode('@', $splitDbPasswordAndHost[1]);
        $dbUserName = $splitDbPasswordAndHost[0];
        $dbUserPassword = $splitDbPassword[0];
        $dbHost = $splitDbPassword[1];
        $mysqlConnection = mysqli_connect($dbHost, $dbUserName, $dbUserPassword, $dbName);
        return $mysqlConnection;
    }
    
    public static function fillupTopCodeWithNewest($offers, $number)
    {
        if (count($offers) < $number) {
            $offers = array();
            $additionalCodes = $number - count($offers);
            $additionalTopVouchercodes = \KC\Repository\Offer::getCommonNewestOffers('newest', $additionalCodes);
            foreach ($additionalTopVouchercodes as $additionalTopVouchercodekey => $additionalTopVouchercodevalue) {
                $offers[] = array(
                    'id'=> $additionalTopVouchercodevalue['shopOffers']['id'],
                    'permalink' => $additionalTopVouchercodevalue['shopOffers']['permaLink'],
                    'offer' => $additionalTopVouchercodevalue
                );
            }
        }
        return $offers;
    }

    public static function sendMandrillNewsletterByBatch(
        $topVouchercodes,
        $categoryVouchers,
        $categoryInformation,
        $mandrillNewsletterSubject,
        $mandrillSenderEmailAddress,
        $mandrillSenderName,
        $recipientMetaData,
        $mandrillMergeVars,
        $mandrillUsersList,
        $footerContent,
        $pathConstants = '',
        $emailHeaderText = '',
        $codeAlert = '',
        $newsLetterHeaderImage = '',
        $newsLetterFooterImage = ''
    ) {
        $basePath = new \Zend_View();
        $basePath->setBasePath(APPLICATION_PATH . '/views/');
        $content = array(
            'name'    => 'content',
            'content' => $basePath->partial(
                'emails/emailLayout.phtml',
                array(
                    'topVouchercodes' => $topVouchercodes,
                    'categoryVouchers' => $categoryVouchers,
                    'categoryInformation' => $categoryInformation,
                    'pathConstants' => $pathConstants,
                    'codeAlert' => $codeAlert,
                    'mandrillNewsletterSubject' => $mandrillNewsletterSubject,
                    'newsLetterHeaderImage' => $newsLetterHeaderImage,
                    'testStatus' => 'doc2'
                )
            )
        );
        sort($mandrillUsersList);
        sort($recipientMetaData);
        sort($mandrillMergeVars);
        $mandrillUsersLists = array_chunk($mandrillUsersList, 500);
        $recipientMetaData = array_chunk($recipientMetaData, 500);
        $mandrillMergeVars = array_chunk($mandrillMergeVars, 500);
        foreach ($mandrillUsersLists as $mandrillUsersKey => $mandrillUsersEmailList) {
            $mailer = new \FrontEnd_Helper_Mailer($pathConstants);
            $mailer->send(
                $mandrillSenderName,
                $mandrillSenderEmailAddress,
                $mandrillUsersEmailList,
                $mandrillUsersEmailList,
                $mandrillNewsletterSubject,
                $content,
                $emailHeaderText,
                !empty($recipientMetaData[$mandrillUsersKey]) ? $recipientMetaData[$mandrillUsersKey] : '',
                $mandrillMergeVars[$mandrillUsersKey],
                $footerContent,
                $pathConstants,
                $newsLetterFooterImage
            );
        }
        return true;
    }

    public static function top10Xml($feedCheck = false)
    {
        $zendTranslate = \Zend_Registry::get('Zend_Translate');
        $domainName ='http://'.\Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_HOST');
        $topVouchercodes = \KC\Repository\PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(10);
        $topVouchercodes = self::fillupTopCodeWithNewest($topVouchercodes, 10);
        $xmlTitle =  $zendTranslate->translate('Kortingscode.nl populairste kortingscodes');
        $xmlDescription  = $zendTranslate->translate('Populairste kortingscodes');
        $xml = new \XMLWriter();
        $xml->openURI('php://output');
        $xml->startDocument('1.0');
        $xml->setIndent(2);
        if ($feedCheck == true) {
            $xml->startElement('rss');
            $xml->writeAttribute('version', '2.0');
            $xml->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
        }
        $xml->startElement("channel");
        $xml->writeElement('title', $xmlTitle);
        $xml->writeElement('description', $xmlDescription);
        $xml->writeElement('link', $domainName);
        $xml->writeElement('language', 'nl');
        $shopName = 'shopname';
        $description = 'title';
        if ($feedCheck == true) {
            $shopName = 'title';
            $description = 'description';
        }
        foreach ($topVouchercodes as $offer) {
            $top10Offers = isset($offer['offer']) ? $offer['offer'] : $offer['popularcode'];
            $xml->startElement("item");
            $xml->writeElement($shopName, $top10Offers['shopOffers']['name']);
            if (mb_strlen($top10Offers['title'], 'UTF-8') > 42) {
                $xml->writeElement($description, mb_substr($top10Offers['title'], 0, 42, 'UTF-8')."...");
            } else {
                $xml->writeElement($description, $top10Offers['title']);
            }
            $xml->writeElement('link', $domainName . '/' . $top10Offers['shopOffers']['permaLink']);
            $xml->endElement();
        }
        if ($feedCheck == false) {
             $xml->writeElement('More', 'nl');
            $xml->writeElement('moreLink', $domainName);
        }
        $xml->endElement();
        if ($feedCheck == true) {
            $xml->endElement();
        }
        $xml->endDocument();
        $xml->flush();
    }

    public static function getWebsitesLocales($websites)
    {
        foreach ($websites as $website) {
            $splitWebsite  = explode('/', $website['name']);
            $locale = isset($splitWebsite[1]) ? $splitWebsite[1] : "nl";
            $locales[strtoupper($locale)] = $website['name'].'='.$website['status'];
        }
        return $locales;
    }

    public static function getPagePermalink()
    {
        $pagePermalink = ltrim(\Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $explodedPagePermalink = explode('/', $pagePermalink);

        if (LOCALE != '') {
            $secondUrlParameter = isset($explodedPagePermalink[2]) ? '/'. $explodedPagePermalink[2] : '';
            $secondUrlParameter = isset($explodedPagePermalink[2]) && intval($explodedPagePermalink[2])
                ? '' : $secondUrlParameter;
            $pagePermalink = $explodedPagePermalink[1].$secondUrlParameter;
        } else {
            $secondUrlParameter = isset($explodedPagePermalink[1]) ? '/'. $explodedPagePermalink[1] : '';
            $secondUrlParameter = isset($explodedPagePermalink[1]) && intval($explodedPagePermalink[1])
                ? '' : $secondUrlParameter;
            $pagePermalink = $explodedPagePermalink[0].$secondUrlParameter;
        }
        $splitPermalinkFromQueryString = explode('?', $pagePermalink);
        return  $splitPermalinkFromQueryString[0];
    }

    public static function redirectAddToFavouriteShop()
    {
        $favouriteShopIdFromSession = new \Zend_Session_Namespace('favouriteShopId');
        if (isset($favouriteShopIdFromSession->favouriteShopId)) {
            header(
                'location:'.HTTP_PATH_LOCALE. 'store/addtofavourite?permalink='
                .\FrontEnd_Helper_viewHelper::__link('link_mijn-favorieten').'&shopId='
                . $favouriteShopIdFromSession->favouriteShopId
            );
            exit();
        }
    }

    public static function getModuleName()
    {
        $requestedUrl = ltrim(REQUEST_URI, '/');
        $splitedModuleName = preg_split('/[\/\?]+/', $requestedUrl);
        return rtrim($splitedModuleName[0], '/');
    }

    public static function setErrorPageParameters($currentObject)
    {
        $currentObject->getResponse()->setHttpResponseCode(404);
        $currentObject->view->popularShops = \KC\Repository\Shop::getPopularStores(12);
        $websitesWithLocales = self::getWebsitesLocales(\KC\Repository\Website::getAllWebsites());
        $currentObject->view->flipitLocales = $websitesWithLocales;
    }

    public static function renderFlipitErrorPage()
    {
        $flipitViewPath = APPLICATION_PATH . '/modules/flipit/views/';
        $flipitErrorViewPath = new \Zend_View();
        $flipitErrorViewPath->setBasePath($flipitViewPath);
        return $flipitErrorViewPath;
    }

    public static function setEmailLogos($locale = '', $publicLocalePath = '', $publicPath = '', $logoName = '')
    {
        $documentRoot = dirname(dirname(dirname(dirname(__FILE__))));

        if (file_exists($documentRoot.'/public/'.$locale.'images/front_end/emails/'.$logoName.'.png')) {
            $emailLogo = $publicLocalePath.'emails/'.$logoName.'.png';
        } else {
            $emailLogo = $locale != '' ? $publicPath.'emails/'.$logoName.'-flipit.png'
                : $publicPath.'emails/'.$logoName.'.png';
        }
        return $emailLogo;
    }

    public static function getRefererHostUrl()
    {
        $refererUrl = parse_url($_SERVER['HTTP_REFERER']);
        return isset($refererUrl['host']) ? $refererUrl['host'] : '';
    }

    public static function getServerNameScheme()
    {
        if (php_sapi_name() != 'cli') {
            $httpUrlScheme = parse_url($_SERVER['HTTP_HOST']);
            $httpUrlScheme = isset($httpUrlScheme['path']) ? explode('.', $httpUrlScheme['path']) : '';
            $httpUrlScheme = isset($httpUrlScheme[0]) ? $httpUrlScheme[0] : 'www';
        } else {
            $httpUrlScheme = 'www';
        }
        
        return $httpUrlScheme;
    }

    public static function getPermalinkAfterRemovingSpecialChracter($permalink)
    {
        $cacheKey = preg_replace("/[\/\&_~,`@!(){}:'*+^%#$?#.=-]/", "", $permalink);
        return $cacheKey;
    }

    public function sortNamesByOrder($unSortedNames, $order)
    {
        $sortedNames = array();
        if ($order == 'asc') {
            asort($unSortedNames);
        } else {
            rsort($unSortedNames);
        }
        foreach ($unSortedNames as $val) {
            $sortedNames[] = $val;
        }
        return  $sortedNames;
    }
	
    public static function getPermalinkAfterRemovingSpecialCharacterAndReplacedWithHyphen($keyword)
    {
        $keyword = preg_replace("/[\/\&_~,`@!(){}:'*+^%#$?#.=-]/", "-", $keyword);
        return $keyword;
    }

    public static function getSinupImage()
    {
        $documentRoot = dirname(dirname(dirname(dirname(__FILE__))));
        if (file_exists($documentRoot.'/public/'. LOCALE .'/images/front_end/gratis.png')) {
            $getSinupImage = PUBLIC_PATH.'images/front_end/gratis.png';
        } else {
            $getSinupImage = '/public/images/front_end/gratis.png';
        }
        return $getSinupImage;
    }

    public static function accountTabPanel($firstname = '')
    {
        $brandsClass = zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'yourbrands' ? 'active' : '';
        $offersClass = zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'youroffers' ? 'active' : '';
        $profileClass = zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'profile' ? 'active' : '';

        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        if(LOCALE == ''):
            $websiteName = 'http://'.$httpScheme.'.kortingscode.nl';
        else :
            $websiteName = 'http://'.$httpScheme.'.flipit.com/'.LOCALE;
        endif;

        $tabContent = '<div class="title">
            <a href="'.$websiteName.'" class="btn blue btn-primary">'.self::__translate('shop verder met korting').'</a>
            <div class="title-frame">
                <h1>'.self::__translate('Hey').' '.$firstname.', '.self::__translate('hoe is het met je?').'</h1>
                '.self::__translate('Je vindt hier een overzicht van je persoonlijke gegevens, pas ze aan waar nodig! Beheer').' 
                <a href="'.HTTP_PATH_LOCALE . self::__link('link_mijn-favorieten').'">'.self::__translate('hier').'</a> '
                .self::__translate('je favoriete webwinkels en bekijk').' <br>'.self::__translate('op').' <a href="'.HTTP_PATH_LOCALE
                . self::__link('link_mijn-favorieten') . "/"
                . self::__link('link_memberonlycodes').'">'
                .self::__translate('deze pagina').'</a>
                 '.self::__translate('je favoriete codes en al onze member acties.').'
            </div>
        </div>
        <div class="sub-buttons">
            <div class="sub-holder">
                <a href="'.HTTP_PATH_LOCALE
                . self::__link('link_mijn-favorieten') . "/"
                . self::__link('link_memberonlycodes').'" class="offers '.$offersClass.'">'
                . self::__translate('Recommended Offers').'</a>
                <a href="'.HTTP_PATH_LOCALE . self::__link('link_mijn-favorieten').'" 
                    class="brands '.$brandsClass.'">'. self::__translate('Favourite Brands').'</a>
                <a href="'.HTTP_PATH_LOCALE
                    . self::__link('link_inschrijven') . "/"
                    . self::__link('link_profiel').'" class="settings '.$profileClass.'">'
                    . self::__translate('Settings').'</a>
            </div>
        </div>';

        return $tabContent;
    }

    public static function getLightBoxFirstTextText($shopName, $lightBoxFirstText)
    {
        $shopFirstText = $shopName . " " . self::__translate('your favorite shop!');
        if (!empty($shopLightBoxText)) {
            $shopFirstText = str_replace('[shop]', $shopName, $lightBoxFirstText);
        }
        return $shopFirstText;
    }

    public static function getLightBoxSecondTextText($shopName, $lightBoxSecondText)
    {
        $shopSecondText = self::__translate('Stay up to date and receive code alerts from'). " " . $shopName;
        if (!empty($lightBoxSecondText)) {
            $shopSecondText = str_replace('[shop]', $shopName, $lightBoxSecondText);
        }
        return $shopSecondText;
    }

    public static function getEditorText($shopName, $text, $ballonText)
    {
        $editorText = self::__translate('Hello');
        if (!empty($ballonText)) {
            $editorText = array();
            foreach ($ballonText as $text) {
                $editorText[] = str_replace('[shop]', $shopName, $text['ballontext']);
            }
        } else {
            $editorText = str_replace('[shop]', $shopName, $text);
        }
        return $editorText;
    }

    public static function getCurrentDate()
    {
        $currentDate = new Zend_Date();
        $currentMonth = $currentDate->get(Zend_Date::MONTH);
        $currentYear = $currentDate->get(Zend_Date::YEAR);
        $currentDay = $currentDate->get(Zend_Date::DAY);
        $currentDateFormation = $currentYear.'-'.$currentMonth.'-'.$currentDay;
        return $currentDateFormation;
    }

    public static function getCategories($categories)
    {
        $categoryRemoveIndex = '';
        foreach ($categories as $category) {
            $category[0]['totalCoupons']  = $category['totalCoupons'];
            $categoryRemoveIndex[] = $category[0];
        }
        return $categoryRemoveIndex;
    }

    public static function getTwoReasons($reasons)
    {
        foreach ($reasons as $reason) {
            $twoReasons ='
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-2 six-reason-image">
                    </div>
                    <div class="col-md-10">
                        <strong>'. $reason[0] . '</strong>
                        <p>'. $reason[1]. '</p>
                    </div>
                </div>
            </div>';
        }
        return $twoReasons;
    }

    public static function exceedMemoryLimitAndExcutionTime()
    {
        set_time_limit(10000);
        ini_set('max_execution_time', 115200);
        ini_set("memory_limit", "1024M");
        return true;
    }

    public static function getCaptchaKey($keyName)
    {
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $frontControllerObject = $application->getOption('resources');
        $captchaKey = $frontControllerObject['frontController']['params'][$keyName];
        return  $captchaKey;
    }

    public static function convertOfferTimeToServerTime($offerTime)
    {
        $localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        $localeTimezone = !empty($localeSettings[0]['timezone']) ? $localeSettings[0]['timezone'] : 'Europe/Amsterdam';
        $refreshTime = new DateTime($offerTime, new DateTimeZone($localeTimezone));
        return $refreshTime->format('Y-m-d H:i:s');
    }

    public static function convertCurrentTimeToServerTime()
    {
        $localeSettings = KC\Repository\LocaleSettings::getLocaleSettings();
        $localeTimezone = !empty($localeSettings[0]['timezone']) ? $localeSettings[0]['timezone'] : 'Europe/Amsterdam';
        $refreshTime = new DateTime('now', new DateTimeZone($localeTimezone));
        return $refreshTime->format('Y-m-d H:i:s');
    }

    public static function getOfferPopupLink($offer)
    {
        $offer = (object) $offer;
        $onClick = '';
        if($offer->discountType == 'CD') {
            $onClick .= "gtmDataBuilder('voucherClickout', 'Code', 'List', 'Offer', " . $offer->id . ");";
        } else if($offer->discountType == 'SL') {
            $onClick .= "gtmDataBuilder('voucherClickout', 'Deal', 'List', 'Offer', " . $offer->id . ");";
        }
        $offerBounceRate = "/out/offer/".$offer->id;
        $offerPartial = new \FrontEnd_Helper_OffersPartialFunctions();
        $urlToShow = $offerPartial->getUrlToShow($offer);
        $popupLink = $offerPartial->getPopupLink($offer, $urlToShow);
        $onClick .= "OpenInNewTab('".HTTP_PATH_LOCALE.$offer->shopOffers['permaLink'].$popupLink."')";
        $offerShopPermalinkAnchor = '<a  id="'.$offer->id.'"
                href="'.$urlToShow.'" vote="0" rel="nofollow" 
                target="_self" onClick="'.$onClick.'">';
        return $offerShopPermalinkAnchor;
    }
}