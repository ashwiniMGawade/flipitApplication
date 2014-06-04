<?php

class FrontEnd_Helper_viewHelper
{
    ##################################################################################
    ################## REFACTORED CODE ###############################################
    ##################################################################################
    public $zendTranslate = '';
    public function __construct() {
        $this->zendTranslate =Zend_Registry::get('Zend_Translate');
    }
    /**
     *
    * @param string $message message to write into log file
    * @param string  $logfile complete filepath
    * @return array regarding data saved or not
    */
    public static function writeLog($message, $logfile = '')
    {
        if ($logfile == '') {
            $logDir = APPLICATION_PATH . "../logs/";

            if (!file_exists( $logDir))
                mkdir( $logDir , 0776, TRUE);
            $fileName = "default" ;
            $logfile = $logDir . $fileName;
        }

        if ( ($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }

        if ( ($remote_addr = $_SERVER['REMOTE_ADDR']) == '') {
            $remote_addr = "REMOTE_ADDR_UNKNOWN";
        }

        $date = date("M d, Y H:i:s", $time);

        if ($fd = @fopen($logfile, "a")) {

            $str = <<<EOD
            $date; $remote_addr; $message
EOD;
            $result = fwrite($fd, $str .PHP_EOL);
            fclose($fd);

            if($result > 0)

                return array('status' => true);
            else
                return array('status' => false, 'message' => 'Unable to write to '.$logfile.'!');
        } else {
            return array('status' => false, 'message' => 'Unable to open log '.$logfile.'!');
        }
    }

    public function sidebarChainWidget($id, $shopName = false, $chainItemId = false)
    {
        if ($shopName) {
            $chain = Chain::returnChainData($chainItemId, $id);
            if (! $chain) {
                return false;
            }

            $httpPathLocale = trim(HTTP_PATH_LOCALE, '/');
            $httpPath = trim(HTTP_PATH, '/');
            $shopHeader = $this->__translate("is an international shop");
            $widgetText = $this->__translate("Check out the coupons and discounts from other countries when you're interested:");
            $string = <<<EOD
             <div class="intro">
                <h2>
                     {$shopName}
                 </h2>
                 <span>$widgetText</span>
            </div>
            <ul class="countries">
EOD;
             $hrefLinks = "" ;
             $hasShops = false ;
             foreach ($chain as $chainInformation) {
                
                 $hrefLinks .=  isset($chainInformation['headLink']) ? $chainInformation['headLink']. "\n" : '';
    
                 if (! empty($chainInformation['shops'])) {
                     $hasShops = true ;
                     $chainInformation = $chainInformation['shops'];
                     $image   = ltrim(sprintf("images/front_end/flags/flag_%s.jpg", $chainInformation['locale']));
                     $string .= sprintf(
                        "<li><a class='country-flags ".strtolower($chainInformation['locale'])."' href='%s' target='_blank'>".self::getCountryNameByLocale(strtolower($chainInformation['locale']))."</a></li>",
                         trim($chainInformation['url']),
                         $httpPath.'/public/'. $image
                        );
                 }
             }
                         $string .= <<<EOD
            </ul>
EOD;

          return array('string' => $string , 'headLink' => $hrefLinks, 'hasShops' => $hasShops );
        }

        return false;
    }

    /**
     * get Newest offer list for Store page from database.
     * @version 1.0
     * @return array $data
     */
    public static function getShopCouponCode($type, $limit, $shopId = 0)
    {
        $shopCouponCodes = '';
        switch (strtolower($type)) {
            case 'expired':
                $shopCouponCodes = Offer::getExpiredOffers($type, $limit, $shopId);
                break;
            case 'popular':
            //to be refactored in future
                $shopCouponCodes = Offer::commongetpopularOffers($type, $limit, $shopId = 0);
                break;
            case 'newest':
            //to be refactored in future
                $shopCouponCodes = Offer::getNewestOffers($type, $limit, $shopId);
                break;
            case 'extended':
            //to be refactored in future
                $shopCouponCodes = Offer::commongetextendedOffers($type, $limit, $shopId);
                break;
            case 'relatedshops':
            //to be refactored in future
                $shopCouponCodes = Offer::commongetrelatedshops($type, $limit, $shopId);
                break;
            case 'similarstoresandsimilarcategoriesoffers':
                $shopCouponCodes = Offer::similarStoresAndSimilarCategoriesOffers($type, $limit, $shopId);
                break;
            case 'latestupdates':
            //to be refactored in future
                $shopCouponCodes = Offer::getLatestUpdates($type, $limit, $shopId);
                break;
            default:
                break;
        }

        return $shopCouponCodes;
    }

    /**
     * Generate cononical link
     *
     * generate cononical from link and split
     *
     * @param string $link
     * @version 1.0
     */
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

    /**
     * generate MoneySaving Articles related to a shop
    * @version 1.0
    * @return array $data
    */
    public static function generateShopMoneySavingGuideArticle($slug, $limit, $id)
    {
        $ShopMoneySavingGuideArticle = MoneySaving::generateShopMoneySavingGuideArticle($slug, $limit, $id);

        return $ShopMoneySavingGuideArticle;
    }

    /**
     * generate main menu like home etc
     * @author asharma
     * @return mixed $string
     * @version 1.0
     */

    public static function generateMainMenu()
    {
        $mainMenu = menu::getFirstLevelMenu();
        $mainMenuCount = count($mainMenu);
        $mainMenuvalue = 0;
        $navigationString ='<nav id="nav"><ul>';
        foreach ($mainMenu as $menu) {
            $permalink = RoutePermalink::getPermalinks($menu['url']);

            if (count($permalink) > 0) {
                $link = $permalink[0]['permalink'];
            } else {
                $link = $menu['url'];
                if ($menu['url']== FrontEnd_Helper_viewHelper::__link('inschrijven')) {
                    if (Auth_VisitorAdapter::hasIdentity()) {
                        $link = FrontEnd_Helper_viewHelper::__link('mijn-favorieten');
                    } else {
                        $link = $menu['url'];
                    }
                }
            }
            if ($mainMenuvalue==$mainMenuCount) {
                $menuName = str_replace(' ','-',trim(strtolower($menu["name"])));
                $navigationString .= '<li><a rel="toggel" id="'. $menuName . '" name="'. $menuName. '" class="show_hide1" href="javascript:void(0);">' . $menu["name"] . '</a></li>';
            } else {
                $menuName = str_replace(' ','-',trim(strtolower($menu["name"])));
                preg_match('/http:\/\//', $menu['url'],$matches);
                if (count($matches) > 0) {
                    $navigationString .= '<li><a id="'. $menuName. '" name="'. $menuName. '" class="" href="'.  $menu['url'] . '">' . $menu["name"] . '</a></li>';
                } else {
                    $navigationString .= '<li><a id="'. $menuName. '" name="'. $menuName. '" class="" href="'. HTTP_PATH_LOCALE  . $link . '">' . $menu["name"] . '</a></li>';
                }
            }
            $mainMenuvalue++;
        }
        $navigationString .= '</ul></nav>';
        return $navigationString;
    }

    public static function getFooterData()
    { 
       $footerData = Footer::getFooter();
       return $footerData;
    }

    public static function getHeadMeta($headMetaValue)
    {
        $domainName = HTTP_HOST;
        if($domainName == "www.kortingscode.nl") {
            $site_name = "Kortingscode.nl";
        } else {
            $site_name = "Flipit.com";
        }
        $socialMediaValue = array('og:title'=>$headMetaValue->facebookTitle, 'og:type'=>'website', 'og:url'=> $headMetaValue->facebookShareUrl,
            'og:description'=>$headMetaValue->facebookDescription, 'og:locale'=>$headMetaValue->facebookLocale, 
            'og:image'=>$headMetaValue->facebookImage, 'og:site_name'=>$site_name, 'twitter:description'=>$headMetaValue->twitterDescription,
            'twitter:site'=>$site_name
        );
        return $socialMediaValue;
    }
    
    /**
     * generate search panle for searching in all store page
     * @param  char  $char
     * @return mixed $string
     * @version 1.0
     */
    public static function alphabetList()
    {
        $letterOrNumber = 0;
        $alphabetList = "<ul class='alphabet' id='alphabet'><li><a id='0' class='' href='#0-9'>0-9</a></li>";
        
        foreach (range('A', 'Z') as $letterOrNumber) {
            $lastAlphabetClass = $letterOrNumber=='Z' ? 'last' : '';
            $alphabetList .="<li><a id='" . $letterOrNumber . "'  href='#".strtolower($letterOrNumber)."' class='".$lastAlphabetClass."'>$letterOrNumber</a></li>";
        }
        $alphabetList .="</ul>";
        return $alphabetList;
    }

    public function socialMediaWidget($socialMediaUrl = '', $type = null)
    {
            $socialMediaTitle = "<h2>".$this->__translate('Follow us')."</h2>";
            $socialMedia = "
                <article class='block'>
                    <div class='social-networks'>
                        <div class='intro'>".$socialMediaTitle."</div>
                        <ul class='share-list'>
                            <li><a class='facebook' href='#'></a></li>
                            <li><a class='twitter' href='#'></a></li>
                            <li><a class='google' href='#'></a></li>
                            <li class='share-text'>".$this->__translate('Follow us for the latest vaucher codes, plus a daily digest of our biggest offers')."</li>
                        </ul>
                    </div>
                </article>";
        return $socialMedia;
    }
    
    public function getShopHeader($shop, $expiredMessage, $offerTitle)
    {
        $bounceRate = "/out/shop/".$shop['id'];
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        $shopUrl = $domainName.'out/shop/'.$shop['id'];
        $affliateProgramUrl = $shop['affliateProgram'] =='' ? $shop['actualUrl'] : $shop['affliateProgram'];
        if ($shop['affliateProgram']) :
            $affliateBounceRate = "ga('send', 'event', 'aff','$bounceRate');";
            $affliateUrl = $shopUrl;
            $affliateDisabled = '';
            $affliateClass = '';
        else:
            $affliateBounceRate = '';
            $affliateUrl = '#';
            $affliateDisabled = 'disabled="disabled"';
            $affliateClass = 'btn-disabled';
        endif;
        
        return self::getHeaderBlockContent($affliateBounceRate, $affliateUrl, $affliateDisabled, $affliateClass, $shop, $expiredMessage, $offerTitle);
    }
    
    public function getHeaderBlockContent($affliateBounceRate, $affliateUrl, $affliateDisabled, $affliateClass, $shop, $expiredMessage, $offerTitle)
    {
        $divContent ='<div class="header-block header-block-2">
                <div id="messageDiv" class="yellow-box-error-box-code" style="margin-top : 20px; display:none;"><strong></strong></div>
                <div class="icon">
                    <a target="_blank" rel="nofollow" 
                    class="text-blue-link store-header-link '.$affliateClass.'"  '.$affliateDisabled.'
                    onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'"><img class="radiusImg" src="'. PUBLIC_PATH_CDN . $shop['logo']['path'] . $shop['logo']['name']. '" alt="'.$shop['name'].'" width="176" height="89" />
                    </a>
                </div> <div class="box">';
        if ($expiredMessage !='storeDetail') {
            $shop['subTitle'] = $this->__translate('Expired').' '.$shop['name'].' '.$this->__translate('copuon code');
        } else {
            $shop['subTitle'] = $shop['subTitle'];
        }
        if ($expiredMessage !='') {
                
                $divContent .= '<h1>'.$shop['title'].'</h1>
                    <strong>'.$shop['subTitle'].'</strong>
                        <a target="_blank" rel="nofollow" 
                        class="btn text-blue-link fl store-header-link '.$affliateClass.' pop btn btn-sm btn-default" '.$affliateDisabled.'
                        onclick="'.$affliateBounceRate.'" href="'.$affliateUrl.'">'.$shop['actualUrl'].'
                        </a>'.self::getLoveAnchor($shop['id']);
        } else {
            $divContent .= '<h1>'.$offerTitle.'</h1>';
        }
                $divContent .='</div></div>';
        return $divContent ;
    }
    
    public function getLoveAnchor($shopId)
    {
        $favouriteShopId = 0;
        if (Auth_VisitorAdapter::hasIdentity()):
             $favouriteShopId=Auth_VisitorAdapter::getIdentity()->id;
        endif;
        return '<a onclick="storeAddToFeborite('.$favouriteShopId.','.$shopId.')" class="pop btn btn-sm btn-default" href="javascript:void(0)">
            <span class="glyphicon glyphicon-heart"></span>'.
            $this->__translate('Love').
        '</a>';
    }

    /**
    * render pagination links
    * @param $totalRecordsForPagination array
    * @param $paginationParameter array
    * @param $itemCountPerPage integer
    * @param $paginationRange integer
    * @version 1.0
    * @return object $pagination
    */
    public static function renderPagination($totalRecordsForPagination, $paginationParameter, $itemCountPerPage, $paginationRange = 3)
    {
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
            foreach ($pagesInRange as $pageNumber) :
                if ($pageNumber < 4 ) :
                    $pageNumberAfterSlash = '';
                    if ($pageNumber > 1) :
                        $pageNumberAfterSlash = "/".$pageNumber;
                    endif;
                    ?>
                    <li class="<?php echo ($pageNumber == $currentPage) ? "active" : "" ?>">
                        <a href="<?php echo HTTP_PATH . $permalink . $pageNumberAfterSlash; ?>">
                        <?php echo $pageNumber;?> 
                        <?php if ($pageNumber == $currentPage) : ?>
                        <span class="sr-only">(current)</span>
                        <?php endif; ?>
                        </a>
                    </li>
                    <?php
                elseif (isset($nextPage) && $pageNumber < 4) : ?>
                    <li class="next"> <a href="<?php echo HTTP_PATH . $permalink . $pageNumberAfterSlash ?>">&gt;</a></li>
                    <?php
                endif;
            endforeach;
            echo "</ul>";
        endif;
    }

    public function popularCategoryWidget()
    {
        $allPopularCategories = Category::getPopularCategories();
        $categorySidebarWodget = 
        '<div class="block">
            <div class="intro">
            <h2 class="sidebar-heading">'. $this->__translate('All Categories').'</h2></div>
                <ul class="tags">';
        for ($categoryIndex=0; $categoryIndex < count($allPopularCategories); $categoryIndex++) {
            $categorySidebarWodget.=
                    '<li>
                        <a href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen'). '/' . $allPopularCategories[$categoryIndex]['category']['permaLink'].'">'.$allPopularCategories[$categoryIndex]['category']['name']
                    .'</li>';
        }
        $categorySidebarWodget.=
                '</ul></div>'; 
        return $categorySidebarWodget;
    }

    public static function getRequestedDataBySetGetCache($dataKey = '', $relatedFunction = '', $replaceStringArrayCheck = '1')
    {
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
    /**
     * Function browseByStoreWidget.
     *
     * Common function to render sidebar widget of alphanumeric categories.
     *
     * @return $browseByStoreWidget
     */
    public function browseByStoreWidget()
    {
        $browseByStoreWidget = 
        '<div class="block">
            <div class="intro">
               <h2>'.$this->__translate('Browse by Store') .'</h2>
            </div>
            <div class="alphabet-holder">
                <ul class="alphabet">';
        foreach (range('A','Z') as $oneCharacter) {
            $redirectUrl = HTTP_PATH_LOCALE ."alle-winkels#".strtolower($oneCharacter);
            $browseByStoreWidget .= 
                    '<li>
                        <a href="' .$redirectUrl.'">'.$this->__translate($oneCharacter).'</a>
                    </li>';
        };
        $browseByStoreWidget.= 
                '</ul>
            </div>
        </div>';
        return $browseByStoreWidget;
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

    public static function getStoreForFrontEnd($storeType, $limit="")
    {
        $stores = '';
        switch (strtolower($storeType)) {
            case 'all':
                //will be refactore in future 
                $stores = Shop::getallStoresForFrontEnd();
                break;
                //will be refactore in future
            case 'recent':
                $stores = Shop::getrecentstores($limit);
                break;
                //refactored 
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
            $ip=current(array_slice(explode(",", $ipRange), 0, 1)); // if proxy returns multiple ip's. Only use the first.
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
                    ->where('deleted=0' )
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
            $howToGuideImagePath = PUBLIC_PATH_CDN.ltrim($howToGuideImages['path'],"/")."thum_bigLogoFile_".$howToGuideImages['name'];
            $howToGuideImageAltText = $howToGuideImages['name'];
        }
        return array('howToGuideImagePath' => $howToGuideImagePath, 'howToGuideImageAltText' => $howToGuideImageAltText);
    }

    public static function getMostPopularCouponOnEarth()
    {
        $splashInformation = self::getSplashInformation();
        if (!empty($splashInformation)) {
            $locale = $splashInformation[0]['locale'];
            $connectionWithSiteDatabase = BackEnd_Helper_DatabaseManager::addConnection($locale);
            $offer = new Offer($connectionWithSiteDatabase['connName']);
            $mostPopularCoupon = $offer->getSplashPagePopularCoupon($splashInformation[0]['offerId']);
            BackEnd_Helper_DatabaseManager::closeConnection($connectionWithSiteDatabase['adapter']);
            return array('locale' => $locale,'mostPopularCoupon' => $mostPopularCoupon);
        } else {
            return array('locale' => '','mostPopularCoupon' => '');
        }  
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

    public function getMetaTags($currentObject, $title = '', $metaTitle = '', $metaDescription = '', $permaLink = '', $image = '', $customHeader = '')
    {
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
            $currentObject->view->layout()->customHeader = $currentObject->view->layout()->customHeader . $customHeader . "\n" ;
        }
        return $currentObject;
    }
    
    public static function getWebsitesLocales($websites)
    {
        foreach ($websites as $website) {
            $splitWebsite  = explode('/', $website['name']);
            $locale = isset($splitWebsite[1]) ?  $splitWebsite[1] : "nl" ;
            $locales[strtoupper($locale)] = $website['name'];
        }
        return $locales;
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

    public static function __translate($variable) {
        $translation =  new Transl8_View_Helper_Translate();
        $variable = $translation->translate($variable);
        return $variable;
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################
    /**
    * get popular code list, Newest offer list, Extended offer list  from database
    * @version 1.0
    * @return array $data
    */
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
            case 'newest':
                $shopData = Offer::getNewestOffers($type, $limit, $shopId, $userId);
                break;
            case 'newestmemberonly':
                $shopData = Offer::commongetMemberOnlyOffer($type, $limit);
                break;
            case 'extended':
                $shopData = Offer::commongetextendedOffers($type, $limit, $shopId);
                break;
            case 'allcouponshowtoguide':
                $shopData = Offer::getCouponOffersHowToGuide($shopId);
                break;
            default:
                break;
        }

        return $shopData;
    }
    /**
     * check key exist in cache or not
     * @author  kraj
     * @param string $key i.e all_store_list
     */
    public static function checkCacheStatusByKey($key)
    {
        $key = $key. '_' .LOCALE;
        $flag = false;
        $cache = Zend_Registry::get('cache');
        if (($result = $cache->load($key)) === false ) {
            $flag = true;
        }
        return $flag;
    }
    /**
     * get cache value by key
     * @author  kraj
     * @param string $key i.e all_store_list
     */
    public static function getFromCacheByKey($key)
    {
        $key = $key. '_' .LOCALE;
        $cache = Zend_Registry::get('cache');
        $cache = $cache->load($key);

        return $cache;
    }
    /**
     * set data in cache by key
     * @author  kraj
     * @param string $key  i.e all_store_list
     * @param mixed  $data i.e data of the store table
     */
    public static function setInCache($key,$data)
    {
        $key = $key. '_' .LOCALE;
        $cache = Zend_Registry::get('cache');
        $cache->save($data, $key);
    }

    /**
     * @author daniel updated by kraj
     * @param string $key  i.e all_store_list
     * @param mixed  $data
     */
    public static function clearCacheByKeyOrAll($key)
    {
        $cache = Zend_Registry::get('cache');
        if ($key=='all') {

            $cache->clean();

        } else {

            # generate locale key for cache update and db_locale is registered in chain controller

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
    /**
    * get sidebar widgets for the page using permalink
     * @param  string $page
     * @author kkumar
     * @return widget html
     * @version 1.0
     */

    public function getSidebarWidget($arr=array(),$page='')
    {
        $pageWidgets = Doctrine_Query::create()
        ->select('p.id,p.slug,w.*,refpage.position')->from('Page p')
        ->leftJoin('p.widget w')
        ->leftJoin('w.refPageWidget refpage')
        ->where("p.permalink="."'$page'")
        ->andWhere('w.status=1')
        ->andWhere('w.deleted=0')
        ->fetchArray();
        $sidebarWidgets = '';
        if (count($pageWidgets)>0) {
        for ($i=0;$i<count($pageWidgets[0]['widget']);$i++) {
        if ($pageWidgets[0]['widget'][$i]['slug']=='win_a_voucher') {
         $sidebarWidgets .= self::WinVoucherWidget($arr);
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='stuur_een') {
            $sidebarWidgets .= self::DiscountCodeWidget();
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='popular_stores') {
            $sidebarWidgets .= self::popularShopWidget();
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='popular_category') {
            $sidebarWidgets .= $this->PopularCategoryWidget();
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='popular_editor') {
            $sidebarWidgets .= self::PopularEditorWidget(@$arr['userId'],$page);
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='most _popular_fashion') {
            $sidebarWidgets .= self::MostPopularFashionGuideWidget();
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='browse' && $page!='search_result') {
            $sidebarWidgets .= self::browseByStoreWidget('default',$pageWidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='browse' && $page=='search_result') {
            $sidebarWidgets .= self::browseByStoreWidget('search',$pageWidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='other_helpful_tips') {
        $sidebarWidgets .= self::otherhelpfullSavingTipsWidget();
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='join_us') {
            $sidebarWidgets .= self::joinUsWidget();
        } elseif ($pageWidgets[0]['widget'][$i]['slug']=='social_media') {
            $sidebarWidgets .= self::socialmedia('','','','widget');
        } 
      }
    }

    return $sidebarWidgets;
}

public static function getSidebarWidgetViaId($pageId,$page='default')
{
    $pagewidgets = Doctrine_Query::create()
    ->select('p.id,p.slug,w.*,refpage.position')->from('Page p')
    ->leftJoin('p.widget w')
    ->leftJoin('w.refPageWidget refpage')
    ->where('p.pageAttributeId="'.$pageId.'"')
    ->andWhere('w.status=1')
    ->andWhere('w.deleted=0')
    ->andWhere('p.deleted=0')
    ->fetchArray();
    $string = '';
    if (count($pagewidgets)>0) {

        for ($i=0;$i<count($pagewidgets[0]['widget']);$i++) {

            if ($pagewidgets[0]['widget'][$i]['slug']=='win_a_voucher') {
                $string .= self::WinVoucherWidget(@$arr);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='stuur_een') {
                $string .= self::DiscountCodeWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_stores') {
                $string .= self::popularShopWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_category') {
                $string .= self::PopularCategoryWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='most _popular_fashion') {
                $string .= self::MostPopularFashionGuideWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_editor') {
                $string .= self::PopularEditorWidget(@$arr['userId'],$page);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page!='search_result') {
                $string .= self::browseByStoreWidget('default',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page=='search_result') {
                $string .= self::browseByStoreWidget('search',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='other_helpful_tips') {
                $string .= self::otherhelpfullSavingTipsWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='join_us') {
                $string .= self::joinUsWidget();
            } else {
                $string .= str_replace( '<br />', '', $pagewidgets[0]['widget'][$i]['content'] );
            }
        }

    }

    return $string;
}

public static function getSidebarWidgetViaPageId($pageId,$page='default')
{
    $pagewidgets = Doctrine_Query::create()
    ->select('p.id,p.slug,w.*,refpage.position')->from('Page p')
    ->leftJoin('p.widget w')
    ->leftJoin('w.refPageWidget refpage')
    ->where('p.id="'.$pageId.'"')
    ->andWhere('w.status=1')
    ->andWhere('w.deleted=0')
    ->andWhere('p.deleted=0')
    ->fetchArray();
    $string = '';

    if (count($pagewidgets)>0) {

        for ($i=0;$i<count($pagewidgets[0]['widget']);$i++) {
            if ($pagewidgets[0]['widget'][$i]['slug']=='win_a_voucher') {
                $string .= self::WinVoucherWidget(@$arr);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='stuur_een') {
                $string .= self::DiscountCodeWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_stores') {
                $string .= self::popularShopWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_category') {
                $string .= self::PopularCategoryWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='most _popular_fashion') {
                $string .= self::MostPopularFashionGuideWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_editor') {
                $string .= self::PopularEditorWidget(@$arr['userId'],$page);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page!='search_result') {
                $string .= self::browseByStoreWidget('default',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page=='search_result') {
                $string .= self::browseByStoreWidget('search',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='other_helpful_tips') {
                $string .= self::otherhelpfullSavingTipsWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='join_us') {
                $string .= self::joinUsWidget();
            } else {
                $string .= str_replace( '<br />', '', html_entity_decode($pagewidgets[0]['widget'][$i]['content'],ENT_QUOTES,'UTF-8') );
            }
        }
    }

    return $string;
}

    public static function WinVoucherWidget($arr)
    {
        $trans = Zend_Registry::get('Zend_Translate');

        $string = '<div class="waardebon sidebar">
        <h4 class="sidebar-heading"><center>'.$trans->translate('Win een scooter').'</center></h4>
        <p><center>'.$trans->translate('Share Kortingscode.nl om kans te maken op deze VETTE prijs').'</center></p>
        <div class="tweet-right-outer clr" style="height: 18px;">'.self::socialmedia('','',$arr ['controllerName'],'widget').'</div>
        </div>';

        return $string;
    }

    public static function DiscountCodeWidget()
    {
        //$trans = Zend_Registry::get('Zend_Translate');
        //@$string .= '';
        //return $string;
    }
    /**
     * get widget html for store page..
     * @author Sunny patial
     * @version 1.0
     * @return array $data
     */
    public static function Storecodeofferwidget()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $str='<form name="discount_code" id="discount_code" action="#" method="POST" novalidate="novalidate"><div class="mark-spencer-bot-col1-mid-heading text-black">
                    <h2>'.$trans->translate('Stuur een kortingscode op!').'</h2>
                    </div>
                    <div class="mark-spencer-bot-col1-mid-textbox1">
                    <input style="width:195px;"  placeholder ="Zalando" name="offer_name" id="offer_name" type="text" />
                    <input id="offer_nameHidden"  name="offer_nameHidden" type="hidden"/>
                     <input id="shopId"  name="shopId" type="hidden"/>
                    </div>

                    <div class="mark-spencer-bot-col1-mid-textbox1">
                    <input style="width:195px;" placeholder ="'.$trans->translate('Vul de code in').'" name="offer_code" id="offer_code" type="text" />
                    </div>

                    <div class="mark-spencer-bot-col1-mid-textbox1">
                    <textarea name="offer_desc" id="offer_desc" class="textarea" style="width:195px; height:105px" cols="0" rows=""></textarea>
                    </div>
                    <div class="mark-spencer-bot-col1-mid-textbox1" style="margin-bottom:4px!important"> <a href="javascript:;" onClick="sendiscountCoupon()"><img src="'.HTTP_PATH.'public/images/front_end/btn-deel-de-korting.png" width="196" height="37" /></a></div></form>';

        return $str;
    }

    public static function getallrelatedshopsid($shopId)
    {
        $data = Offer::commongetallrelatedshopsid($shopId);

        return $data;

    }
    public function popularShopWidget()
    {
        $popularStores = self::getStoreForFrontEnd('popular', 25);
        $popularStoresContent = '<div class="block"><div class="intro">
                   <h2>'.$this->__translate('Populaire Winkels').'</h2>
                   <span>'.$this->__translate('Grab a promotional code, discount code or voucher for').date(' F Y').'</span>
                 </div><ul class="tags">';

        for ($i=0; $i<count($popularStores); $i++) {
            $class ='';
            if ($i%2==0) {
                $class = 'class="none"';
            }

            if ($popularStores[$i]['shop']['deepLink']!=null) {
                $popularStoreUrl = $popularStores[$i]['shop']['deepLink'];
            } elseif ($popularStores[$i]['shop']['refUrl']!=null) {
                $popularStoreUrl = $popularStores[$i]['shop']['refUrl'];
            } elseif ($popularStores[$i]['shop']['actualUrl']) {
                $popularStoreUrl = $popularStores[$i]['shop']['actualUrl'];
            } else {
                $popularStoreUrl = HTTP_PATH_LOCALE .$popularStores[$i]['shop']['permaLink'];
            }

            $popularStoreUrl = HTTP_PATH_LOCALE .$popularStores[$i]['shop']['permaLink'];
            $popularStoresContent .='<li '.$class.'><a title='.$popularStores[$i]['shop']['name'].' href='.$popularStoreUrl.'>'.ucfirst(self::substring($popularStores[$i]['shop']['name'], 200)).'</a></li>';
        }
        $popularStoresContent .='</ul></div>';

        return $popularStoresContent;
    }
    public static function substring($text,$length)
    {
        if (strlen($text)>$length) {
            $text = substr($text,0,$length).'...';
        }

        return $text;
    }

    # shorten the string jusing mb str method
    public static function mbSubstring($text,$length,$encoding ='utf-8')
    {
        if (strlen($text) > $length) {
            $text = mb_substr ( $text , 0, $length , $encoding).'...';
        }

        return $text;
    }

    /********************** Start Front end home page *************************************/

    /**
     * This Comman function To geting many type home page Sections from database for front end
     * @author Er.Kundal
     * @version 1.0
     * @return array $data
     */
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
            case "loginwedget":
                $result = self :: getloginwedget();
                break;
        }

        return $result;
    }

    /**
     * get login wedget for front end
     * @author Er.Kundal
     * @version 1.0
     * @return array $data
     */
    public static function getloginwedget()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $logindiv = '
        <h4 class="text-white">' . $trans->translate('Doe mee met kortingscode.nl') . '</h4>
        <p class="text-light-grey text-center mt10">' . $trans->translate('Krijg gratis toegang tot exclusieve Members-Only codes en kortingen!') . '</p>
        <div class="log-direct-icon">
            <img src="'.HTTP_PATH .'public/images/front_end/img-id-new.png" width="48" height="42" alt="New Icon" />
        </div>';
        if (Auth_VisitorAdapter::hasIdentity()) {
            $logindiv .='<a class="direct-login-btn-ftr"  href="' .HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('logout').'">' . $trans->translate('UITLOGGEN') .'</a>
            <span class="text-light-grey">
                <a class="text-white-link" href="' .HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('logout').'">' . $trans->translate('Logout') . '</a>
            </span>
            ';

        } else {

        $logindiv .='<a class="direct-login-btn-ftr" href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('inschrijven').'/'.FrontEnd_Helper_viewHelper::__link('stap1').'" rel="nofollow">' .  $trans->translate('DIRECT ACCOUNT AANMAKEN') .'</a>
        <span class="text-light-grey">
            ' . $trans->translate('Al member?') . ' <a class="text-white-link" href= "'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'" rel="nofollow">' . $trans->translate('Inloggen') . '</a>
        </span>
        ';

        }

        return $logindiv;
    }

    /**
     * get login wedget for footer
     * @author blal
     * @version 1.0
     * @return array $data
     */
    public static function getloginwidgetFooter()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $logindiv = '
        <h4>' . $trans->translate('Met Ã©Ã©n click nog meer voordeel!') . '</h4>
        <p class="text-gray text-center mt15">' . $trans->translate('Geen goede kortingscode gevonden? Bekijk de Members-Only kortingscodes! ') . '</p>';
        if (Auth_VisitorAdapter::hasIdentity()) {

            $logindiv .='<a class="direct-login-btn-ftr2" href="' .HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('logout').'">' . $trans->translate('UITLOGGEN') .'</a>
            <span class="text-gray text-center">
                <a  href="' .HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('login').'/'.FrontEnd_Helper_viewHelper::__link('logout').'">' . $trans->translate('Logout') . '</a>
            </span>
            ';

        } else {

        $logindiv .= '<a class="direct-login-btn-ftr2" href="' .HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('inschrijven').'/'.FrontEnd_Helper_viewHelper::__link('stap1').'" rel="nofollow">' . $trans->translate('DIRECT ACCOUNT AANMAKEN') .'</a>
        <span class="text-gray text-center">
        ' . $trans->translate('Al member?') . ' <a href='.HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('login') . ' rel="nofollow">' . $trans->translate('Inloggen') . '</a>
        </span>
        ';
        }

        return $logindiv;
    }
    /********************** End Front end home page *************************************/

    /**
     * Get popular Editor list from database for fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */
    public static function PopularEditorWidget($uId = null,$page)
    {
        $editorId = self::getMostPublishedArticlesEditor();

        $trans = Zend_Registry::get('Zend_Translate');
        $editorDetail = self :: getFamousEditorDetail($editorId);
        $userPicture = HTTP_PATH.'public/images/NoImage/NoImage_142_90.jpg';
        if (isset($editorDetail[0]['profileimage']['name']) && $editorDetail[0]['profileimage']['name']!='') {
                $userPicture = PUBLIC_PATH_CDN .$editorDetail[0]['profileimage']['path'].'thum_large_widget_'.$editorDetail[0]['profileimage']['name'];

        }
        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . '/views/scripts/');

        $string = '<div class="popularist-outer sidebar">
        <div class="popularist-heading text-black">
        <h4 class="sidebar-heading">'.$trans->translate('Populairste Editor').'</h4>
        </div>
        <div class="popularist-left">
        <img src="'.$userPicture.'">
        </div>
        <div class="popularist-right">
        <div class="popularist-right-top text-black">
        <h4><strong title="'.ucfirst(strtolower(@$editorDetail[0]['firstName'])).' '.ucfirst(strtolower(@$editorDetail[0]['lastName'])).'">'.self::substring(ucfirst(strtolower(@$editorDetail[0]['firstName'])).' '.ucfirst(strtolower(@$editorDetail[0]['lastName'])),11).'</strong></h4>
        </div>
        <div class="popularist-right-top">' . $view->render('partials/deal.phtml') .'</div>


        <div class="popularist-right-bot">
        <ul>';
        $popcate = Category::getPouparCategory();
        for ($i=0;$i<count($popcate);$i++) {
            $string .='<li><a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('categorieen').'/'.$popcate[$i]['category']['permaLink'].'">'.$popcate[$i]['category']['name'].'</a></li>';}

        $string .='</ul>
        </div>
        </div>
        </div>';

        return $string;
    }

    /**
     * This function returns the identity of editor who has most published articles
     * @author cbhopal
     * @version 1.0
     * @return array $editor
     */
    public static function getMostPublishedArticlesEditor()
    {
        $date = date('Y-m-d H:i:s');

        $editor = Doctrine_Query::create()->select('count(*) as articlesWritten ,authorid')
                                          ->from('Articles')
                                          ->where("publishdate <= '". $date ."'")
                                          ->andWhere("publish = 1")
                                          ->groupBy('authorid')
                                          ->orderBy('articlesWritten DESC')
                                          ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

        return $editor['authorid'];
    }

    /**
     * Most popular Fashion Guide Widget for fronthome page
     * @author mkaur
     * @version 1.0
     * @return array $data
     */
    public static function MostPopularFashionGuideWidget()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $articles = Category:: generateMostReadArticle();
        $string ='<div class="mostpopular-outer sidebar">
        <div class="mostpopular-heading text-black">
        <h4 class="sidebar-heading">'.$trans->translate('Most popular Fashion Guides').'</h4>
        </div>
        <!-- Most Popular Col1 Starts -->';
        for ($i=0;$i<count($articles);$i++) {

            $img = '';
                $img = PUBLIC_PATH_CDN.$articles[$i]['articles']['articleImage']['path']."thum_article_medium_".$articles[$i]['articles']['articleImage']['name'];

        $string.='<div class="mostpopular-col1">
                    <div class="rediusnone1"><a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('plus').'/'.$articles[$i]['articles']['permalink'].'" class="popular_article">' . '<img src="' . $img . '"></a></div>
                    <div><a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('plus').'/'.$articles[$i]['articles']['permalink'].'" class="popular_article">' . $articles[$i]['articles']['title'].'</a></div></div>';
        }
        $string.='<!-- Most Popular Col1 Ends -->
        </div>';

        return $string;
    }

    /**
     * Most popular Fashion Guide article fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */

     public static function MostPopularFashionGuide()
     {
            $data = Articles::MostPopularFashionGuide();

            return $data;
    }

    /**
     * Other helpful money saving Widget for fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */

    public static function otherhelpfullSavingTipsWidget()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $articles = self::otherhelpfullSavingTips();

        $string ='<div class="mostpopular-outer sidebar">
        <h4 class="sidebar-heading">'.$trans->translate('Other Helpful Saving Tips').'</h4>
        <!-- Most Popular Col1 Starts -->';

      for ($i=0;$i<count($articles);$i++) {

        $img = PUBLIC_PATH_CDN.$articles[$i]['thumbnail']['path']."thum_article_samll_".$articles[$i]['thumbnail']['name'];

        $string.='<div class="mostpopular-col1">
        <span class="mostpopular-col1-img1">
            <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('plus').'/'.$articles[$i]['permalink'].'"><img  src="'.$img.'" alt="'.$articles[$i]['title'].'"></a>
        </span>
        <span class="mostpopular-col1-text">
            <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('plus').'/'.$articles[$i]['permalink'].'">'.$articles[$i]['title'].'</a>
        </span>
        </div>';
        }
        $string.='<!-- Most Popular Col1 Ends -->
        </div>';

        return $string;
    }

    /**
     * Other helpful money saving fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */

    public static function otherhelpfullSavingTips()
    {
        $data = Articles::otherhelpfullSavingTips();

        return $data;
    }

    /**
     * join us widget
     * @param  char  $char
     * @author kkumar
     * @return mixed $string
     * @version 1.0
     */

    public static function joinUsWidget()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $string = '
        <div class="join-us sidebar">
            <h4 class="sidebar-heading">'.$trans->translate('Join Us!').'</h4>
            <p>'.$trans->translate('Spot deals, send them in, earn Flips and become the best Deal Hunter of the Universe!').'</p>


            <span class="blue-btn">
                <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('inschrijven').'/'.FrontEnd_Helper_viewHelper::__link('stap1').'" rel="nofollow">
                    <span><strong>'.$trans->translate('Sign up!').'</strong></span>
                </a>
            </span>
        </div>';

        return $string;
    }

        /**
     * Welcome rocket
     * @param  char  $char
     * @author kkumar
     * @return mixed $string
     * @version 1.0
     */

    public static function welcomeRocket()
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $string = '
        <div class="join-us sidebar">
            <p>
                <img alt="" src="/public/images/upload/ckeditor/images/welkom.png" style="width: 214px; height: 200px; margin: 8px;">
            </p>
            <span class="blue-btn">
                <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('inschrijven').'/'.FrontEnd_Helper_viewHelper::__link('stap1').'" rel="nofollow">
                    <span>'.$trans->translate('Gratis Inschrijven!').'</span>
                </a>
            </span>
        </div>';

        return $string;
    }

    public static function getEditorDetail($uId)
    {
        $obj = new User();

        return $obj->getUserDetail($uId);
    }

    /**
     * This will get the detail of famous article editor
     * @param  integer $eId
     * @return array
     */
    public static function getFamousEditorDetail($eId)
    {
        $obj = new User();

        return $obj->getFamousUserDetail($eId);

    }

    /**
     * generate submenu
     * @author kraj
     * @return mixed $mainUl
     * @version 1.0
     */
  public static function generateSecondNav()
  {
        $trans = Zend_Registry::get('Zend_Translate');
        //call to self function and get first level menu from database
        $data = menu::getFirstLevelMenu();

        $mainUl = '<div class="nav-container hide" style="display: none;">';
        $mainUl .= '<div class="new-outer">';
          $mainUl .= '<div class="row" id="links-submenu">';
        //$i = 1;
        foreach ($data as $menus) {

                //call to self function and get second level menu from database
                 $second =menu::getLevelSecond($menus['id']);
                foreach ($second as $s) {

                                    $mainUl .= '<ul class="column_two">';//start ul for main menu mean first level

                                    $mainUl .= '<li class="new_heading" ><a href="' . HTTP_PATH_LOCALE.ltrim($s['url'], '/') . '" class="">'  . $s['name'] . '</a></li>';
                                    //call to self function and get third level menu from database
                                    $third =self::getLevelThird($s['id']);
                                    $i = 1;
                                    foreach ($third  as $t) {

                                                if ($i <= 7) {

                                                    $mainUl .= '<li><a href="' . HTTP_PATH_LOCALE.ltrim($t['url'], '/') . '" class="">'  .$t['name'] . '</a></li>';
                                                    } else {

                                                    $mainUl .= '<li><a href="#" class="">'.$trans->translate('Read More').'</a></li>';
                                                break;
                                            }

                                            $i++;

                                    }
                                $mainUl .="</ul>";//close main url level first
                        }

                    }
        $mainUl .="<div class='clr'></div>";
        $mainUl .="<div class='sub-nav-bot-link'><a href='".HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('plus')."'><img src='".HTTP_PATH."public/images/front_end/sub-nav-money-icon.png' width='' height='' alt='' style='margin: 0 7px 2px 0;'/>".$trans->translate('Alle pluss')."</a> &raquo;</div>";
        $mainUl .="</div>";//close row

        $mainUl .="</div>";//close new-outer
        $mainUl .="</div>";//nav-container

        return $mainUl;
  }

  /**
   * get menu from datbase by parent on third level
   * @author kraj
   * @version 1.0
   * @return array $th
   */
  public static function getLevelThird($id)
  {
        $third =menu::getLevelThird($id);

        return $third ;
  }

  /**
   * Get popular Category list from database for fronthome page
   * @author kkumar
   * @version 1.0
   * @return array $data
   */

  public static function getPopularCategories($flag,$type='popular')
  {
    $data = Doctrine_Query::create()
        ->select('p.id,o.name,o.categoryiconid,i.type,i.path,i.name,p.type,p.position,p.categoryId')
        ->from('PopularCategory p')
        ->leftJoin('p.category o')
        ->leftJoin('o.categoryicon i')
        ->where('o.deleted=0' )
        ->andWhere('o.status= 1' )
        ->orderBy("p.position ASC")->limit($flag)->fetchArray();

     return $data;

  }
  /**
   * generate MS Articles related to a shop
   * @author Raman
   * @version 1.0
   * @return array $data
   */
    public static function generateMSArticleShop($slug, $limit, $id)
    {
        $data = MoneySaving::generateMSArticleShop($slug, $limit, $id);

        return $data;
    }
  
  /**
   * Check if the an Shop id and ip exist in shopViewCount Table
   * @author Raman
   * @version 1.0
   * $id shop id
   * $ip ip address of local machine
   * @return array $data
   */

  public static function checkIfThisShopEntryExists($eventType, $id, $ip)
  {
    $res = "false";
    switch (strtolower($eventType)) {

        case 'onclick':
            //die("raman");
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
            ->where('deleted=0' )
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

  /**
   * Check if the an Offer id and ip exist in offer ViewCount Table
   * @author Raman
   * @version 1.0
   * $id offer id
   * $ip ip address of local machine
   * @return array $data
   */

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

        case 'onload':

            $data = Doctrine_Query::create()
            ->select('count(*) as exists')
            ->from('ViewCount')
            ->where('onLoad!=0')
            ->andWhere('offerId="'.$id.'"')
            ->andWhere('IP="'.$ip.'"')
            ->fetchArray();

            if ($data[0]['exists'] == 0) {

                $cnt  = new ViewCount();
                $view = 1;
                $cnt->offerId = $id;
                $cnt->onLoad = $view;
                $cnt->IP = $ip;
                $cnt->save();
                $res = "true";
            }

            break;

        default:
        break;
    }

    return $res;

  }

  /**
   * Get user Id from offer id
   * @author Raman
   * @return array $userId
   * @version 1.0
   */
  public static function getAuthorId($offerId)
  {
    $userId = Doctrine_Query::create()
    ->select('o.authorId')
    ->from("Offer o")
    ->where("o.id =$offerId")
    ->fetchArray();

    return $userId;
  }

  /**
   * function to use in replaceStringArray
   * @author Raman
   * @version 1.0
   */

  public static function replaceKeyword(&$item, $key)
  {
    $item = str_replace(array('[month]', '[year]', '[day]'), array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY), $item);

  }

  /**
   * function for replacing a srting in an array with another string
   * @author Raman
   * @return array $array
   * @version 1.0
   */
  public static function replaceStringArray($originalArray)
  {
    $obj = new self();
    array_walk_recursive($originalArray, array($obj, 'replaceKeyword'));

    return $originalArray;

  }

  /**
   * function for replacing a srting in a variable with another string
   * @author Raman
   * @return array $variable
   * @version 1.0
   */
  public static function replaceStringVariable($variable)
  {
     $variable = str_replace(array('[month]', '[year]', '[day]','[offers]','[coupons]','[accounts]'),
                    array(CURRENT_MONTH, CURRENT_YEAR, CURRENT_DAY,

                        # total amount of offers in country
                        Dashboard::getDashboardValueToDispaly("total_no_of_offers"),

                        # total number of coupons in country
                        Dashboard::getDashboardValueToDispaly("total_no_of_shops_online_code"),

                        # total number of (e-mail adresses) accounts in country
                        Dashboard::getDashboardValueToDispaly("total_no_members") ), $variable);

    return $variable;
  }

  public static function generatCononical($link)
  {
        $permalink = $link;
        preg_match("/^[\d]+$/", $permalink, $matches);

        if (intval(@$matches[0]) > 0) {
            $permalink = explode('/'.$matches[0],$permalink);
            $permalink = $permalink[0];
        } else {
            $permalink = $permalink;
        }

        $splitVal = explode('?', $permalink);
        //echo "<pre>";print_r($splitVal);die;

        if (!empty($splitVal)) {

            $permalink = $splitVal[0];

        } else {

            $permalink = $permalink;
        }

        if (LOCALE!='en') {

            $front = Zend_Controller_Front::getInstance();
            $cd = $front->getControllerDirectory();
            $moduleNames = array_keys($cd);
            $routeProp = explode( '/' , $permalink) ;

            if (in_array($routeProp[0] , $moduleNames)) {
                $tempLang  = ltrim($permalink , $routeProp[0]);
                $permalink  = ltrim($tempLang , '/');
            }

        }
        //die($permalink);
        return rtrim($permalink, '/');
  }

  /**
   * Remove all content after  ?
   * @param unknown_type $json
   */
  public static function generateSocialLink($link)
  {
    $splitVal = explode('?', $link);
    if (!empty($splitVal)) {

        $link= $splitVal[0] ;

    }

    return $link;
  }
  /**
   * Generate cononical link
   *
   * generate cononical from link and split
   *
   * @param string $link
   * @version 1.0
   */

  public static function generatCononicalForSignUp($link)
  {
    $plink = $link;

    if (LOCALE!="") {
        $front = Zend_Controller_Front::getInstance();
        $cd = $front->getControllerDirectory();
        $moduleNames = array_keys($cd);
        $permalink = ltrim($_SERVER['REQUEST_URI'], '/');

        $splitVal = explode('?', $link);
        if (!empty($splitVal)) {

            $permalink = $splitVal[0] ;

        } else {

            $permalink = $link;
        }


        $routeProp = explode( '/' , $permalink) ;

        $tempLang1  = rtrim($routeProp[0] , '/') ;
        $tempLang2  = ltrim($permalink , $tempLang1) ;
        $tempLang  = ltrim($tempLang2 , '/') ;

        //remove an email from the URL
        $perArray = explode('/', $tempLang);
        $originalString = "";
        foreach($perArray as $arrayPer):

            $decodedEmail = base64_decode($arrayPer);
            if (filter_var($decodedEmail, FILTER_VALIDATE_EMAIL)) {

                $originalString = $arrayPer;
                // valid address
            }

        endforeach;

        $perWithoutEmail = rtrim(str_replace($originalString, "", $tempLang), '/');

        if (in_array($routeProp[0] , $moduleNames)) {

            $plink = $perWithoutEmail;

        }

    } else {

        $splitVal = explode('?', $link);
        if (!empty($splitVal)) {

            $permalink = $splitVal[0] ;
            $perArray = explode('/', $permalink);

            //remove an email from the URL
            $originalString = "";
            foreach($perArray as $arrayPer):

                $decodedEmail = base64_decode($arrayPer);
                if (filter_var($decodedEmail, FILTER_VALIDATE_EMAIL)) {

                    $originalString = $arrayPer;
                    // valid address
                }

            endforeach;

            $perWithoutEmail = rtrim(str_replace($originalString, "", $permalink), '/');
            $permalink = $perWithoutEmail;
        } else {

            $permalink = $link;


        }

        $plink = $permalink;
    }

    return $plink;
  }

    ######## Refactored End ##############
  /**
   * Generate cononical link for search page
   *
   * generate cononical from link and split
   *
   * @param string $link
   * @version 1.0
   */
  public static function generatCononicalForSearch($link)
  {

    $plink = null ;
    if (LOCALE!='en') {
        $front = Zend_Controller_Front::getInstance();
        $cd = $front->getControllerDirectory();
        $moduleNames = array_keys($cd);
        $permalink = ltrim($_SERVER['REQUEST_URI'], '/');

        $splitVal = explode('?', $link);
        if (!empty($splitVal)) {

            $permalink = $splitVal[0] ;

        } else {

            $permalink = $link;
        }

        $routeProp = explode( '/' , $permalink) ;

        $routeProp = explode( '/' , $permalink) ;

        $tempLang1  = rtrim($routeProp[0] , '/') ;
        $tempLang2  = ltrim($permalink , $tempLang1) ;
        $tempLang  = ltrim($tempLang2 , '/') ;

        if (in_array($routeProp[0] , $moduleNames)) {

            $tempLang3 = explode( '/' , $tempLang) ;
            $plink = $tempLang3[0];
        }

    } else {

        $splitVal = explode('?', $link);
        if (!empty($splitVal)) {

            $permalink = $splitVal[0] ;

        } else {

            $permalink = $link;
        }

        $tempLang3 = explode( '/' , $permalink) ;
        $plink = $tempLang3[0];
    }

    return $plink;
  }

  public static function setValueOfJs($json)
  { ?>
        <script type="text/javascript">
            var json = <?php echo $json;?>
            var jsonData = {};
            jsonData['nl_NL_frontend_js'] = json;
            var gt = new Gettext({ "domain" : "nl_NL_frontend_js" , "locale_data" : jsonData });
        </script>
    <?php
  }

    /**
     * sanitize
     *
     * it will return sanetized string which prevent sql injection and other volunerabilities
     *
     * @param  string  $string    stri9ng value to sanetize
     * @param  boolena $stripTags set fale if keep html or xml tags
     * @return string
     *
     * @author sp singh
     */
    public static function sanitize($string, $stripTags = true)
    {

        $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@[\\\]@'   // Strip out slashes
        );

        $string = preg_replace($search, array('',''), $string);

        $string = htmlspecialchars($string,ENT_QUOTES, 'UTF-8');

        $string = trim(rtrim(rtrim($string)));
        $string = mysql_real_escape_string($string);

        return $string;
    }

    /**
     * retrunCountryName
     *
     * return country name for requested locale
     *
     * @param string $siteLocale country locale
     *
     * @example retrunCountryName('BE') will return Belgien
     */
    public static function retrunCountryName($siteLocale)
    {

        $locale = Signupmaxaccount::getAllMaxAccounts();
        $locale = !empty($locale[0]['locale']) ? $locale[0]['locale'] : 'NL';

        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);

        return $countries[$siteLocale];

    }

        /**
     * fillupTopCodeWithNewest
     *
     * This function is used to fill up popular offers with newest offer if popular offers are less then required number
     *
     * @param array   $offer totla offers
     * @param integer $muber required length of codes
     * @author sp Singh
     * @version 1.0
     */
    public static function fillupTopCodeWithNewest($offers,$number)
    {
         # if top korting are less than $number then add newest code to fill up the list upto $number
            if (count($offers) < $number ) {
                # the limit of popular oces
                if(count($offers) < $number )
                    $additionalCodes = $number - count($offers) ;

                    # GET TOP 5 POPULAR CODE
                    $additionalTopVouchercodes = Offer::commongetnewestOffers('newest', $additionalCodes);
        }

        return false;
    }


    /**
    * sendMandrillNewsletterByBatch
    *
    * This function is used to send mandrill newsletter in batches of 500
    *
    * @param array $mandrillNewsletterSubject mandrill newsletter subject
    * @param array $mandrillSenderEmailAddress
    * @param array $mandrillSenderName
    * @param array $recipientMetaData contains receipent email and referrer
    * @param array $globalMergeVars contains shop permalinks
    * @param array $mandrillMergeVars contains email, login link and login with unsubscribe
    * @param array $templateName template name of mandrill
    * @param array $templateContent
    * @param object $mandrill
    * @param array $mandrillUsersList
    * @version 1.0
    */
    public static function sendMandrillNewsletterByBatch(
        $mandrillNewsletterSubject,
        $mandrillSenderEmailAddress,
        $mandrillSenderName,
        $recipientMetaData,
        $globalMergeVars,
        $mandrillMergeVars,
        $templateName,
        $templateContent,
        $mandrill,
        $mandrillUsersList
    )
    {
        sort($mandrillUsersList);
        sort($recipientMetaData);
        sort($mandrillMergeVars);
        $mandrillUsersLists  = array_chunk($mandrillUsersList, 500);
        $recipientMetaData   = array_chunk($recipientMetaData, 500);
        $mandrillMergeVars    = array_chunk($mandrillMergeVars, 500);

        foreach ($mandrillUsersLists as $mandrillUsersKey => $mandrillUsersEmailList) {
            $mandrillMessage = array(
                'subject'    => $mandrillNewsletterSubject,
                'from_email' => $mandrillSenderEmailAddress,
                'from_name'  => $mandrillSenderName,
                'to'         => $mandrillUsersEmailList,
                'inline_css' => true,
                "recipient_metadata" => $recipientMetaData[$mandrillUsersKey],
                'global_merge_vars' => $globalMergeVars,
                'merge_vars' => $mandrillMergeVars[$mandrillUsersKey]
            );
            $mandrill->messages->sendTemplate($templateName, $templateContent, $mandrillMessage);
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
}