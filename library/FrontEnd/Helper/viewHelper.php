<?php
class FrontEnd_Helper_viewHelper
{
    public static function writeLog($message, $logfile = '')
    {
        $requestTime = Zend_Controller_Front::getInstance()->getRequest()->getServer('REQUEST_TIME');
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
        }
    }

    public static function getShopCouponCode($type, $limit, $shopId = 0)
    {
        $shopCouponCodes = '';
        switch (strtolower($type)) {
            case 'expired':
                $shopCouponCodes = Offer::getExpiredOffers($type, $limit, $shopId);
                break;
            case 'similarstoresandsimilarcategoriesoffers':
                $shopCouponCodes = Offer::similarStoresAndSimilarCategoriesOffers($type, $limit, $shopId);
                break;
            case 'latestupdates':
                $shopCouponCodes = Offer::getLatestUpdates($type, $limit, $shopId);
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
            $frontEndControllers = Zend_Controller_Front::getInstance();
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

    public static function generateShopMoneySavingGuideArticle($slug, $limit, $articleId)
    {
        $ShopMoneySavingGuideArticle = MoneySaving::generateShopMoneySavingGuideArticle($slug, $limit, $articleId);
        return $ShopMoneySavingGuideArticle;
    }
    
    public static function getFooterData()
    {
        return Footer::getFooter();
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
        $chainLocale = Website::getWebsiteDetails('', strtolower($site_name).$locale);
        $ogCustomLocale = explode('_', $chainLocale['chain']);
        $ogCustomLocale = isset($ogCustomLocale[1]) ? $ogCustomLocale[1] : $ogCustomLocale[0];
        $ogLocale = !empty($chainLocale) && $chainLocale['chain'] != '' ?
            strtolower($ogCustomLocale) : $headMetaValue->facebookLocale;

        $socialMediaValue =
            array(
                'og:title'=>FrontEnd_Helper_viewHelper::replaceStringVariable($headMetaValue->facebookTitle),
                'og:type'=>'website',
                'og:url'=> $headMetaValue->facebookShareUrl,
                'og:description'=>FrontEnd_Helper_viewHelper::replaceStringVariable(
                    $headMetaValue->facebookDescription
                ),
                'og:locale'=>$ogLocale,
                'og:image'=>$headMetaValue->facebookImage,
                'og:site_name'=>$site_name,
                'twitter:description'=>FrontEnd_Helper_viewHelper::replaceStringVariable(
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
        $pagination = Zend_Paginator::factory($totalRecordsForPagination);
        $pagination->setCurrentPageNumber($currentPageNumber);
        $pagination->setItemCountPerPage($itemCountPerPage);
        $pagination->setPageRange($paginationRange);
        return $pagination;
    }

    public static function getPagnation($pageCount, $currentPage, $redirector, $pagesInRange, $nextPage)
    {
        $requestUri = Zend_Controller_Front::getInstance()->getRequest()->getServer('REQUEST_URI');
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
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
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
                if (ArticleViewCount::getArticleClick($articleId, $clientIp) == 0) {
                    ArticleViewCount::saveArticleClick($articleId, $clientIp);
                    $artcileExistsOrNot = "true";
                }
                break;
            case 'onload':
                if (ArticleViewCount::getArticleOnload($articleId, $clientIp) == 0) {
                    ArticleViewCount::saveArticleOnLoad($articleId, $clientIp);
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
                if (ShopViewCount::getShopClick($shopId, $clientIp) == 0) {
                    ShopViewCount::getSaveShopClick($shopId, $clientIp);
                    $resultStatus = "true";
                }
                break;
            case 'onload':
                if (ShopViewCount::getShopOnload($shopId, $clientIp) == 0) {
                    ShopViewCount::getSaveShopOnload($shopId, $clientIp);
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
                if (ViewCount::getOfferClick($offerId, $clientIp) == 0) {
                    ViewCount::saveOfferClick($offerId, $clientIp);
                    $resultStatus = "true";
                }
                break;
            case 'onload':
                if (ViewCount::getOfferOnload($offerId, $clientIp) == 0) {
                    ViewCount::saveOfferOnload($offerId, $clientIp);
                    $resultStatus = "true";
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
                $stores = Shop::getPopularStores($limit);
                break;
            default:
                break;
        }
        return $stores;
    }

    public static function getRealIpAddress()
    {
        $clientIp = Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_CLIENT_IP');
        $httpXForwardedFor = Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_X_FORWARDED_FOR');

        if (!empty($clientIp)) {
            $clinetIp = $clientIp;
        } else if (!empty($httpXForwardedFor)) {
            $ipRange = $httpXForwardedFor;
            $clinetIp = current(array_slice(explode(",", $ipRange), 0, 1));
        } else {
            $clinetIp = Zend_Controller_Front::getInstance()->getRequest()->getServer('REMOTE_ADDR');
        }
        return $clinetIp;
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
        $splash = new Splash();
        return $splash->getSplashInformation();
    }

    public static function getCountryNameByLocale($locale)
    {
        $countryName = '';
        if(!empty($locale)) :
            $locale = $locale == 'en' ? 'nl' : $locale;
            $locale = new Zend_Locale(strtoupper($locale));
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
        $currentObject->view->headTitle(FrontEnd_Helper_viewHelper::replaceStringVariable($metaTitle));
        $currentObject->view->headMeta()->setName(
            'description',
            FrontEnd_Helper_viewHelper::replaceStringVariable($metaDescription)
        );
        $currentObject->view->facebookTitle = FrontEnd_Helper_viewHelper::replaceStringVariable($title);
        $currentObject->view->facebookShareUrl = $facebookShareUrl;
        $currentObject->view->facebookImage = $image;
        $currentObject->view->facebookDescription = FrontEnd_Helper_viewHelper::replaceStringVariable(
            $metaDescription
        );
        if (LOCALE == '') {
            $facebookLocale = '';
        } else {
            $facebookLocale = LOCALE;
        }
        $currentObject->view->facebookLocale = $facebookLocale;
        $currentObject->view->twitterDescription = FrontEnd_Helper_viewHelper::replaceStringVariable(
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
        $trans = Zend_Registry::get('Zend_Translate');
        $variable = $trans->translate(_($variable));
        return $variable;
    }

    public static function __form($variable)
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $variable = $trans->translate(_($variable));
        return $variable;
    }

    public static function __email($variable)
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $variable = $trans->translate(_($variable));
        return $variable;
    }

    public static function __translate($variable)
    {
        $translation = new Transl8_View_Helper_Translate();
        $variable = $translation->translate($variable);
        return $variable;
    }

    public static function getWebsiteLocales($frontend = '')
    {
        $locales = '';
        $websites = Website::getAllWebsites();
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
        $localesList = Zend_Locale::getLocaleList();
        $websiteLocales = self::getWebsiteLocales($frontend);

        foreach ($localesList as $localeIndex => $localeValue) {
            $localeName = explode('_', $localeIndex);
            $websiteLocale = isset($localeName[1]) ? $localeName[1] : '';
            
            if (array_key_exists($websiteLocale, $websiteLocales)) {
                $locale = new Zend_Locale($localeIndex);
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
                $shopData = Offer::getAllOfferOnShop($shopId);
                break;
            case 'topsixoffers':
                $shopData = Offer::getAllOfferOnShop($shopId, $limit, false, false, true);
                break;
            case 'popular':
                $shopData = Offer::commongetpopularOffers($type, $limit, $shopId, $userId);
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
        $cache = Zend_Registry::get('cache');
        if (($result = $cache->load($key)) === false) {
            $flag = true;
        }
        return $flag;
    }
   
    public static function getFromCacheByKey($key)
    {
        $key = $key. '_' .LOCALE;
        $cache = Zend_Registry::get('cache');
        $cache = $cache->load($key);
        return $cache;
    }

    public static function setInCache($key, $data)
    {
        $key = $key. '_' .LOCALE;
        $cache = Zend_Registry::get('cache');
        $cache->save($data, $key);
    }

    public static function clearCacheByKeyOrAll($key)
    {
        $cache = Zend_Registry::get('cache');
        if ($key=='all') {
            $cache->clean();
        } else {
            if (! Zend_Registry::get('db_locale')) {
                $locale = LOCALE;
            } else {
                $locale  = Zend_Registry::get('db_locale') == 'en' ? '' : Zend_Registry::get('db_locale');
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
        $data = Offer::commongetallrelatedshopsid($shopId);
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
                $result = PopularCode::gethomePopularvoucherCode($limit);
                break;
            case "newest":
                $result = PopularVouchercodes::getNewstoffer($limit);
                break;
            case "category":
                $result = Category::getPopularCategories($limit);
                break;
            case "specialList":
                $result = $data = SpecialList::getfronendsplpage($limit);
                break;
            case "moneySaving":
                $result = Articles::getmoneySavingArticles($limit);
                break;
            case "asseenin":
                $result = SeenIn::getSeenInContent();
                break;
            case "about":
                $status = 1;
                $result = About::getAboutContent($status);
                break;
        }
        return $result;
    }

    public static function getAuthorId($offerId)
    {
        $userId = Doctrine_Query::create()
        ->select('o.authorId')
        ->from("Offer o")
        ->where("o.id =".$offerId)
        ->fetchArray();
        return $userId;
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
        $obj = new self();
        array_walk_recursive($originalArray, array($obj, 'replaceKeyword'));
        return $originalArray;
    }

    public static function replaceStringVariable($variable)
    {
        $variable = str_replace(
            array('[month]', '[year]', '[day]', '[offers]', '[coupons]', '[accounts]', '[visitors]', '[shops]'),
            array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY,
            Dashboard::getDashboardValueToDispaly("total_no_of_offers"),
            Dashboard::getDashboardValueToDispaly("total_no_of_shops_online_code"),
            Dashboard::getDashboardValueToDispaly("total_no_members"),
            Dashboard::getDashboardValueToDispaly("total_no_members"),
            Dashboard::getDashboardValueToDispaly("total_no_of_shops_online_code")),
            $variable
        );
        return $variable;
    }

    public static function sanitize($string, $stripTags = true)
    {
        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
            '@[\\\]@'   // Strip out slashes
        );
        $string = preg_replace($search, array('',''), $string);
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        $string = trim(rtrim(rtrim($string)));
        $string = mysqli_real_escape_string(self::getDbConnectionDetails(), $string);
        return $string;
    }

    public static function getDbConnectionDetails()
    {
        foreach (Doctrine_Manager::getInstance()->getConnections() as $connection) {
            $dbConnection = $connection->getOptions();
            preg_match('/host=(.*);/', $dbConnection['dsn'], $host);
        }
        $splitDbName = explode('=', $dbConnection['dsn']);
        $dbName = $splitDbName[2];
        $dbUserName = $dbConnection['username'];
        $dbUserPassword = $dbConnection['password'];
        $dbHost = $host[1];
        $mysqlConnection = mysqli_connect($dbHost, $dbUserName, $dbUserPassword, $dbName);
        return $mysqlConnection;
    }
    
    public static function fillupTopCodeWithNewest($offers, $number)
    {
        if (count($offers) < $number) {
            $additionalCodes = $number - count($offers);
            $additionalTopVouchercodes = Offer::getCommonNewestOffers('newest', $additionalCodes);
            foreach ($additionalTopVouchercodes as $additionalTopVouchercodekey => $additionalTopVouchercodevalue) {
                $offers[] = array(
                    'id'=> $additionalTopVouchercodevalue['shop']['id'],
                    'permalink' => $additionalTopVouchercodevalue['shop']['permalink'],
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
        $codeAlert = ''
    ) {
        $basePath = new Zend_View();
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
                    'mandrillNewsletterSubject' => $mandrillNewsletterSubject
                )
            )
        );
        sort($mandrillUsersList);
        sort($recipientMetaData);
        sort($mandrillMergeVars);
        $mandrillUsersLists  = array_chunk($mandrillUsersList, 500);
        $recipientMetaData   = array_chunk($recipientMetaData, 500);
        $mandrillMergeVars    = array_chunk($mandrillMergeVars, 500);
        foreach ($mandrillUsersLists as $mandrillUsersKey => $mandrillUsersEmailList) {
            $mailer = new FrontEnd_Helper_Mailer($pathConstants);
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
                $pathConstants
            );
        }
        return true;
    }

    public static function top10Xml($feedCheck = false)
    {
        $zendTranslate = Zend_Registry::get('Zend_Translate');
        $domainName ='http://'.Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_HOST');
        $topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(10);
        $topVouchercodes = self::fillupTopCodeWithNewest($topVouchercodes, 10);
        $xmlTitle =  $zendTranslate->translate('Kortingscode.nl populairste kortingscodes');
        $xmlDescription  = $zendTranslate->translate('Populairste kortingscodes');
        $xml = new XMLWriter();
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
            $top10Offers = $offer['offer'];
            $xml->startElement("item");
            $xml->writeElement($shopName, $top10Offers['shop']['name']);
            if (mb_strlen($top10Offers['title'], 'UTF-8') > 42) {
                $xml->writeElement($description, mb_substr($top10Offers['title'], 0, 42, 'UTF-8')."...");
            } else {
                $xml->writeElement($description, $top10Offers['title']);
            }
            $xml->writeElement('link', $domainName . '/' . $top10Offers['shop']['permaLink']);
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
        $pagePermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
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
        $favouriteShopIdFromSession = new Zend_Session_Namespace('favouriteShopId');
        if (isset($favouriteShopIdFromSession->favouriteShopId)) {
            header(
                'location:'.HTTP_PATH_LOCALE. 'store/addtofavourite?permalink='
                .FrontEnd_Helper_viewHelper::__link('link_mijn-favorieten').'&shopId='
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
        $currentObject->view->popularShops = Shop::getPopularStores(12);
        $websitesWithLocales = self::getWebsitesLocales(Website::getAllWebsites());
        $currentObject->view->flipitLocales = $websitesWithLocales;
    }

    public static function renderFlipitErrorPage()
    {
        $flipitViewPath = APPLICATION_PATH . '/modules/flipit/views/';
        $flipitErrorViewPath = new Zend_View();
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
}