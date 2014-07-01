<?php
class FrontEnd_Helper_viewHelper
{
    public static function writeLog($message, $logfile = '')
    {
        if ($logfile == '') {
            $logDir = APPLICATION_PATH . "../logs/";
            if (!file_exists($logDir)) {
                mkdir($logDir, 0776, true);
            }
            $fileName = "default" ;
            $logfile = $logDir . $fileName;
        }
        if (($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }
        if (($remote_addr = $_SERVER['REMOTE_ADDR']) == '') {
            $remote_addr = "REMOTE_ADDR_UNKNOWN";
        }
        $date = date("M d, Y H:i:s", $time);
        if ($fd = @fopen($logfile, "a")) {
            $str = <<<EOD
            $date; $remote_addr; $message
EOD;
            $result = fwrite($fd, $str .PHP_EOL);
            fclose($fd);
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

    public static function generateShopMoneySavingGuideArticle($slug, $limit, $id)
    {
        $ShopMoneySavingGuideArticle = MoneySaving::generateShopMoneySavingGuideArticle($slug, $limit, $id);
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
        $socialMediaValue =
            array(
                'og:title'=>$headMetaValue->facebookTitle,
                'og:type'=>'website',
                'og:url'=> $headMetaValue->facebookShareUrl,
                'og:description'=>$headMetaValue->facebookDescription,
                'og:locale'=>$headMetaValue->facebookLocale,
                'og:image'=>$headMetaValue->facebookImage,
                'og:site_name'=>$site_name,
                'twitter:description'=>$headMetaValue->twitterDescription,
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
        $permalink = ltrim($_SERVER['REQUEST_URI'], '/');
        $permalink = rtrim($permalink, '/');
        preg_match("/[^\/]+$/", $permalink, $permalinkMatches);
        if (intval($permalinkMatches[0]) > 0 && intval($permalinkMatches[0]) < 4) :
            $permalink = explode('/'.$permalinkMatches[0], $permalink);
            $permalink = $permalink[0];
        elseif (intval($permalinkMatches[0]) > 3) :
            throw new Exception('Error occured');
        else:
            $permalink = $permalink;
        endif;
        $permalinkAfterQueryString = explode('?', $permalink);
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $view->headLink(array('rel' => 'canonical', 'href' => HTTP_PATH . strtolower($permalinkAfterQueryString[0])));
        if ($pageCount > 1) :
            if ($currentPage - 1 != 0) :
                if ($currentPage==2) :
                    $previousPermalink = HTTP_PATH . $permalink;
                else:
                    $previousPermalink = HTTP_PATH . $permalink .'/'. ($currentPage - 1);
                endif;
                $view->headLink(array('rel' => 'prev', 'href' => $previousPermalink));
            endif;
            if ($currentPage <= 2) :
                if ($currentPage == 1) :
                    $permalinkAfterQueryString = explode('?', $permalink);
                    $permalink = $permalinkAfterQueryString[0];
                endif;
                if ($currentPage+1 <= $pageCount):
                    $view->headLink(array('rel' => 'next', 'href' => HTTP_PATH . $permalink .'/'. ($currentPage + 1)));
                endif;
            endif;
            echo '<ul class="pagination">';
            foreach ($pagesInRange as $pageNumber):
                if ($pageNumber < 4):
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
                elseif (isset($nextPage) && $pageNumber < 4) : ?>
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
        $cacheStatusByKey = FrontEnd_Helper_viewHelper::checkCacheStatusByKey($dataKey);
        if ($cacheStatusByKey) {
            if ($replaceStringArrayCheck == '1') {
                $requestedInformation = FrontEnd_Helper_viewHelper::replaceStringArray($functionToBeCached);
            } else {
                $requestedInformation = $functionToBeCached;
            }
            FrontEnd_Helper_viewHelper::setInCache($dataKey, $requestedInformation);
        } else {
            $requestedInformation = FrontEnd_Helper_viewHelper::getFromCacheByKey($dataKey);
        }
        return $requestedInformation;
    }

    public static function viewCounter($type, $eventType, $id)
    {
        $clientIP = self::getRealIpAddress();
        $ip = ip2long($clientIP);
        $counterValue = "false";
        switch (strtolower($type)) {
            case 'article':
                $counterValue = self::checkIfThisArticleEntryExists($eventType, $id, $ip);
                break;
            case 'shop':
                $counterValue = self::checkIfThisShopEntryExists($eventType, $id, $ip);
                break;
            case 'offer':
                $counterValue = self::checkIfThisOfferEntryExists($eventType, $id, $ip);
                break;
            default:
                break;
        }
        return $counterValue;
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
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipRange=$_SERVER['HTTP_X_FORWARDED_FOR'];
            $ip=current(array_slice(explode(",", $ipRange), 0, 1));
        } else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function checkIfThisArticleEntryExists($eventType, $id, $ip)
    {
        $artcileExistsOrNot = "false";
        switch (strtolower($eventType)) {
            case 'onclick':
                $article = Doctrine_Query::create()
                    ->select('count(*) as exists')
                    ->from('ArticleViewCount')
                    ->where('deleted=0')
                    ->andWhere('onclick!=0')
                    ->andWhere('articleid="'.$id.'"')
                    ->andWhere('ip="'.$ip.'"')
                    ->fetchArray();
                if ($article[0]['exists'] == 0) {
                    $articleViewCount  = new ArticleViewCount();
                    $onClick = 1;
                    $articleViewCount->articleid = $id;
                    $articleViewCount->onclick = $onClick;
                    $articleViewCount->ip = $ip;
                    $articleViewCount->save();
                    $artcileExistsOrNot = "true";
                }
                break;
            case 'onload':
                $article = Doctrine_Query::create()
                    ->select('count(*) as exists')
                    ->from('ArticleViewCount')
                    ->where('deleted=0')
                    ->andWhere('onload!=0')
                    ->andWhere('articleid="'.$id.'"')
                    ->andWhere('ip="'.$ip.'"')
                    ->fetchArray();
                if ($article[0]['exists'] == 0) {
                    $articleViewCount  = new ArticleViewCount();
                    $onLoad = 1;
                    $articleViewCount->articleid = $id;
                    $articleViewCount->onload = $onLoad;
                    $articleViewCount->ip = $ip;
                    $articleViewCount->onclick = 0;
                    $articleViewCount->save();
                    $artcileExistsOrNot = "true";
                }
                break;
            default:
                break;
        }
        return $artcileExistsOrNot;
    }

    public function getHowToGuidesImage($howToGuideImages)
    {
        $howToGuideImagePath = '';
        $howToGuideImageAltText = '';
        if (!empty($howToGuideImages)) {
            $howToGuideImagePath =
                PUBLIC_PATH_CDN.ltrim($howToGuideImages['path'], "/")."thum_bigLogoFile_".$howToGuideImages['name'];
            $howToGuideImageAltText = $howToGuideImages['name'];
        }
        return array(
            'howToGuideImagePath' => $howToGuideImagePath,
            'howToGuideImageAltText' => $howToGuideImageAltText
        );
    }

    public static function getSplashInformation()
    {
        $splashInformation = Doctrine_Query::create()
            ->select('*')
            ->from('Splash s')
            ->fetchArray();
        return $splashInformation;
    }

    public static function getCountryNameByLocale($locale)
    {
        $countryName = '';
        if(!empty($locale)) :
            $locale = $locale == 'en' ? 'nl' : $locale;
            $locale = new Zend_Locale(strtoupper($locale));
            $countries = $locale->getTranslationList('Territory');
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
        $currentObject->view->headTitle($metaTitle);
        $currentObject->view->headMeta()->setName('description', $metaDescription);
        $currentObject->view->facebookTitle = $title;
        $currentObject->view->facebookShareUrl = HTTP_PATH_LOCALE . $permaLink;
        $currentObject->view->facebookImage = $image;
        $currentObject->view->facebookDescription = $metaDescription;
        if (LOCALE == '') {
            $facebookLocale = '';
        } else {
            $facebookLocale = LOCALE;
        }
        $currentObject->view->facebookLocale = $facebookLocale;
        $currentObject->view->twitterDescription = $metaDescription;

        if (isset($customHeader)) {
            $currentObject->view->layout()->customHeader =
                $currentObject->view->layout()->customHeader . $customHeader . "\n" ;
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
        $translation =  new Transl8_View_Helper_Translate();
        $variable = $translation->translate($variable);
        return $variable;
    }

    public static function getWebsiteLocales($frontend = '')
    {
        $locales = '';
        $websites = Website::getAllWebsites();
        foreach ($websites as $website) {
            $spiltWebsite  = explode('/', $website['name']);
            $locale = isset($spiltWebsite[1]) ?  $spiltWebsite[1] : "nl" ;
           
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
                $shopData = Offer::getAllOfferOnShop($shopId, $limit);
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
                $locale = LOCALE ;
            } else {
                $locale  = Zend_Registry::get('db_locale') == 'en' ? ''
                            : Zend_Registry::get('db_locale') ;
            }
            $key = $key. '_' .$locale;
            $cache->remove($key);
            $OutPutKey = explode('_', $key);
            $newKey = $OutPutKey[0].'_'.$OutPutKey[1].'_'.'_output'.'_'.$OutPutKey[2] ;
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
   
    public static function gethomeSections($offertype, $flag = "")
    {
        switch ($offertype) {
            case "popular":
                $result = PopularCode :: gethomePopularvoucherCode($flag);
                break;
            case "newest":
                $result = PopularVouchercodes :: getNewstoffer($flag);
                break;
            case "category":
                $result = Category :: getPopularCategories($flag);
                break;
            case "specialList":
                $result = $data = SpecialList::getfronendsplpage($flag);
                break;
            case "moneySaving":
                $result = Articles :: getmoneySavingArticles($flag);
                break;
            case "asseenin":
                $result = SeenIn :: getSeenInContent();
                break;
            case "about":
                $status = 1;
                $result = About :: getAboutContent($status);
                break;
        }
        return $result;
    }
    
    public static function checkIfThisShopEntryExists($eventType, $id, $ip)
    {
        $res = "false";
        switch (strtolower($eventType)) {
            case 'onclick':
                $data = Doctrine_Query::create()
                ->select('count(*) as exists')
                ->from('ShopViewCount')
                ->where('deleted=0')
                ->andWhere('onclick!=0')
                ->andWhere('shopid="'.$id.'"')
                ->andWhere('ip="'.$ip.'"')
                ->fetchArray();

                if ($data[0]['exists'] == 0) {
                    $cnt  = new ShopViewCount();
                    $view = 1;
                    $cnt->shopid = $id;
                    $cnt->onclick = $view;
                    $cnt->ip = $ip;
                    $cnt->save();
                    $res = "true";
                }
                break;
            case 'onload':
                $data = Doctrine_Query::create()
                ->select('count(*) as exists')
                ->from('shopviewcount')
                ->where('deleted=0')
                ->andWhere('onload!=0')
                ->andWhere('shopid="'.$id.'"')
                ->andWhere('ip="'.$ip.'"')
                ->fetchArray();
                if ($data[0]['exists'] == 0) {
                    $cnt  = new ArticleViewCount();
                    $view = 1;
                    $cnt->shopid = $id;
                    $cnt->onload = $view;
                    $cnt->ip = $ip;
                    $cnt->save();
                    $res = "true";
                }
                break;
            default:
                break;
        }
        return $res;
    }

    public static function checkIfThisOfferEntryExists($eventType, $id, $ip)
    {

        $res = "false";
        switch (strtolower($eventType)) {
            case 'onclick':
                $data = Doctrine_Query::create()
                    ->select('count(v.id) as exists')
                    ->addSelect("(SELECT  id FROM ViewCount  click WHERE click.id = v.id) as clickId")
                    ->from('ViewCount v')
                    ->where('onClick!=0')
                    ->andWhere('offerId="'.$id.'"')
                    ->andWhere('IP="'.$ip.'"')
                    ->fetchArray();
                if ($data[0]['exists'] == 0) {
                    $cnt  = new ViewCount();
                    $view = 1;
                    $cnt->offerId = $id;
                    $cnt->onClick = $view;
                    $cnt->IP = $ip;
                    $cnt->save();
                    $res = "true";
                }
                break;
        }
        $obj = new User();
        return $obj->getFamousUserDetail($eId);
    }

    public static function getAuthorId($offerId)
    {
        $userId = Doctrine_Query::create()
        ->select('o.authorId')
        ->from("Offer o")
        ->where("o.id =$offerId")
        ->fetchArray();
        return $userId;
    }

    public static function replaceKeyword(&$item, $key)
    {
        $item = str_replace(array('[month]', '[year]', '[day]'), array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY), $item);
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
            array('[month]', '[year]', '[day]', '[offers]', '[coupons]', '[accounts]'),
            array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY,
            Dashboard::getDashboardValueToDispaly("total_no_of_offers"),
            Dashboard::getDashboardValueToDispaly("total_no_of_shops_online_code"),
            Dashboard::getDashboardValueToDispaly("total_no_members")),
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
            $additionalCodes = $number - count($offers) ;
            $additionalTopVouchercodes = Offer::commongetnewestOffers('newest', $additionalCodes);
        }
        return false;
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
        $pathConstants = ''
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
                    'pathConstants' => $pathConstants
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
                FrontEnd_Helper_viewHelper::__email('email_Newsletter header'),
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
        $domainName ='http://'.$_SERVER['HTTP_HOST'];
        $topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(10);
        $topVouchercodes = FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes, 10);
        $xmlTitle =  $zendTranslate->translate('Kortingscode.nl populairste kortingscodes') ;
        $xmlDescription  = $zendTranslate->translate('Populairste kortingscodes') ;
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
            $top10Offers = $offer['offer'] ;
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
            $locale = isset($splitWebsite[1]) ?  $splitWebsite[1] : "nl" ;
            $locales[strtoupper($locale)] = $website['name'].'='.$website['status'];
        }
        return $locales;
    }

    public static function getPagePermalink()
    {
        $pagePermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');

        if (LOCALE != '') {
            $explodedPagePermalink = explode('/', $pagePermalink);
            $secondUrlParameter = isset($explodedPagePermalink[2]) ? '/'. $explodedPagePermalink[2] : '';
            $pagePermalink = $explodedPagePermalink[1].$secondUrlParameter;
        }
        
        return  $pagePermalink;
    }

    public static function redirectAddToFavouriteShop()
    {
        $favouriteShopIdFromSession = new Zend_Session_Namespace('favouriteShopId');
        if (isset($favouriteShopIdFromSession->favouriteShopId)) {
            header('location:'.HTTP_PATH_LOCALE. 'store/addtofavourite');
            exit();
        }
    }
}

