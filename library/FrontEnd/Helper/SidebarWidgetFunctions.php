<?php
class FrontEnd_Helper_SidebarWidgetFunctions extends FrontEnd_Helper_viewHelper
{
    public function getSidebarWidgets($widgetType)
    {
        $widgets = \KC\Repository\PageWidgets::getWidgetsByType($widgetType);
        $pageWidgets = '';
        if (empty($widgets)) {
            //fallback
        } else {
            foreach ($widgets as $widget) {
                $widgetTitle = strtolower($widget['widget']['title']);
                switch ($widgetTitle) {
                    case 'popular category':
                        echo $this->popularCategoryWidget();
                        break;
                    case 'popular stores':
                        echo $this->popularShopWidget();
                        break;
                    default:
                        break;
                }
            }
        }
        return $pageWidgets;
    }

    public function shopLatestNewsWidget($latestShopUpdates, $shopName)
    {
        $lastesNews = '';
        if (!empty($latestShopUpdates)) {
            $lastesNews = '<section class="block">
                <h4>'.
                    $this->__translate('Latest news about').' '.$shopName.
                '</h4>';
            foreach ($latestShopUpdates as $latestShopUpdate) {
                $latestShopUpdate = (object) $latestShopUpdate;
                $newsStartDateObject = new Zend_Date($latestShopUpdate->created_at->format('Y-d-m'));
                $newsStartDate = $newsStartDateObject->get(Zend_Date::DATE_LONG);
                $httpStringPosition = 'http';
                $content = '';
                if ($latestShopUpdate->content!=""):
                    $content = $latestShopUpdate->content;
                else:
                    $content = "No desc found";
                endif;
                $lastesNews .= '<div class="news-box">
                    <article class="box">
                        <em class="date">'. $newsStartDate.'</em><h5>';
                if ($latestShopUpdate->url!=null && $latestShopUpdate->url!='') :
                            $lastesNews .= '<a href="';
                    if (strpos($latestShopUpdate->url, $httpStringPosition) !== false) :
                        $lastesNews .= $latestShopUpdate->url;
                    else :
                        $lastesNews .=  'http://'.$latestShopUpdate->url;
                    endif;
                    $lastesNews .= 'target="_blank">'. $latestShopUpdate->title.'</a>';
                else :
                    $lastesNews .= $latestShopUpdate->title;
                endif;
                    $lastesNews .= '</h5>
                        <p>'.
                        $content.
                        '</p>
                    </article>
                </div>';
            }
            $lastesNews .= '</section>';
        }
        return $lastesNews;
    }

    public function popularEditorWidget($shopEditor, $howToUseGuidePermalink, $actualUrl, $disqusReplyCounter, $shop)
    {
        $authorName = '';
        if (isset($shopEditor['firstName'])) {
            $authorName = FrontEnd_Helper_AuthorPartialFunctions::
                getAuthorName($shopEditor['firstName'], $shopEditor['lastName']);
        }
        $shopHeader = $this->__translate('Dealspotter for').' '.$shop['name'];
        $shopEditorPath = '';
        if (isset($shopEditor['profileimage']['name'])) {
            $shopEditorPath =
                HTTP_PATH_CDN
                .ltrim($shopEditor['profileimage']['path'], "/")
                .'' .$shopEditor['profileimage']['name'];
        }

        $shopAbout = $this->__translate('About').' '.$shop['name'];
        $howToUse = $this->howToUseGuide($shop, $actualUrl, $howToUseGuidePermalink);
        $popularEditor = '<article class="block">
            <div class="intro intro-2">
                <div class="author-info">
                    <div class="img-thumbnail">
                        <img title="'. $authorName.'" 
                            alt="'.$authorName.'" src="'. $shopEditorPath.'" class="img-responsive">
                    </div>
                    <div class="textbox">
                        <h3><?php echo $authorName; ?></h3>
                        <span class="text">'.$shopHeader.'</span>';
        if ($shop['discussions'] == 1) {
            $popularEditor .= !empty($disqusReplyCounter) ? $disqusReplyCounter : '';
        }
                    $popularEditor .= '</div>
                </div>
                <h2>'.$shopAbout.'</h2>'
                .preg_replace(
                    '/(<a\b[^><]*)>/i',
                    '$1 style="color: #0077cc;text-decoration: underline;">',
                    FrontEnd_Helper_viewHelper::replaceStringVariable($shop['shopText'])
                ).'
            </div>'
            .$howToUse.'
        </article>';
        return $popularEditor;
    }

    public function howToUseGuide($shop, $actualUrl, $howToUseGuidePermalink)
    {
        $howToGude = '';
        $domainName = LOCALE == '' ? HTTP_PATH : HTTP_PATH_LOCALE;
        $shopUrl = $shop['affliateProgram'] == '1' ?
            '<a href="'.$domainName.'out/shop/'.$shop['id'].'" rel="nofollow" target="_blank">'.$actualUrl.'</a>' :
             $actualUrl;
        $howToGude = '<ul class="add-info">';
        if ($shop['howToUse']):
            $howToGude .= '<li class="question">
                <h4>'. $this->__translate('How to use code').'</h4>
                <p>'.$this->__translate('Read our full').
                    '<a href="'. HTTP_PATH_LOCALE.$howToUseGuidePermalink.'#guide">'.$shop['name'].' '.$this->__translate('Promotional Code').'</a>'
                   .$this->__translate('help guide').
                '</p>
            </li>';
        endif;
            $howToGude .= '<li class="web">
                <h4>'.$this->__translate('Official website').'</h4>'.$shopUrl.'
            </li>
        </ul>';
        return $howToGude;
    }

    public function shopChainWidget($shopChain)
    {
        $chain = '';
        if ($shopChain) {
            $chain = '<article class="block">'.$shopChain.'</article>';
        }
        return $chain;
    }

    public function sidebarChainWidget($id, $shopName = false, $chainItemId = false)
    {
        if ($shopName) {
            $chain = KC\Repository\Chain::returnChainData($chainItemId, $id);
            if (! $chain) {
                return false;
            }
            $httpPathLocale = trim(HTTP_PATH_LOCALE, '/');
            $httpPath = trim(HTTP_PATH, '/');
            $shopHeader = $this->__translate("is an international shop");
            $widgetText =
                $this->__translate("Check out the coupons and discounts from other countries when you're interested:");
            $string = <<<EOD
             <div class="intro">
                <h4>
                     {$shopName}
                </h4>
                 <span>$widgetText</span>
            </div>
            <ul class="countries">
EOD;
            $hrefLinks = "" ;
            $hasShops = false ;
            foreach ($chain as $chainInformation) {
                $hrefLinks .=  isset($chainInformation['headLink']) ? $chainInformation['headLink']. "\n" : '';
                if (!empty($chainInformation['shops'])) {
                    $hasShops = true ;
                    $chainInformation = $chainInformation['shops'];
                    $image   = ltrim(sprintf("images/front_end/flags/flag_%s.jpg", $chainInformation['locale']));
                    $string .= sprintf(
                        "<li>
                        <a class='country-flags ".strtolower($chainInformation['locale'])."' 
                        href='%s' target='_blank'>"
                        .self::getCountryNameByLocale(strtolower($chainInformation['locale']))."</a>
                        </li>",
                        trim($chainInformation['url']),
                        $httpPath.'/public/'. $image
                    );
                }
            }
            $string .= <<<EOD
            </ul>
EOD;
            return array('string' => $string, 'headLink' => $hrefLinks, 'hasShops' => $hasShops);
        }
        return false;
    }
    
    public function popularCategoryWidget()
    {
        $allCategories = FrontEnd_Helper_viewHelper::getCategories(KC\Repository\Category::getAllCategories());
        $allCategories = array_slice($allCategories, 0, 10, true);
        $categoriesSidebarWidget =
        '<div class="block">
            <div class="intro">
            <h4 class="sidebar-heading">'. $this->__translate('All Categories').'</h4></div>
                <ul class="tags">';
        foreach ($allCategories as $category) {

            $categoriesSidebarWidget.='
                    <li>
                        <a href="'. HTTP_PATH_LOCALE .
                        FrontEnd_Helper_viewHelper::__link('link_categorieen'). '/'
                        . $category['permaLink'].'">' . $category['name'] .
                        '</a>' . '
                    </li>';
        }
        $categoriesSidebarWidget.=
                '</ul></div>';
        return $categoriesSidebarWidget;
    }

    public function popularShopWidget()
    {
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'shop_popularShopForWidget_list',
            array(
                'function' => '\KC\Repository\Shop::getAllPopularStoresForSidebarWidget',
                'parameters' => array(20)
            )
        );
        $popularStoresContent = '<div class="block"><div class="intro">
                   <h4>'.$this->__translate('Populaire Winkels').'</h4>
                   <span>'
                   .$this->__translate('Grab a promotional code, discount code or voucher').'</span>
                 </div><ul class="tags">';
        for ($i=0; $i<count($popularStores); $i++) {
            $class ='';
            if ($i%2==0) {
                $class = 'class="none"';
            }
            if ($popularStores[$i]['deepLink']!=null) {
                $popularStoreUrl = $popularStores[$i]['deepLink'];
            } elseif ($popularStores[$i]['refUrl']!=null) {
                $popularStoreUrl = $popularStores[$i]['refUrl'];
            } elseif ($popularStores[$i]['actualUrl']) {
                $popularStoreUrl = $popularStores[$i]['actualUrl'];
            } else {
                $popularStoreUrl = HTTP_PATH_LOCALE .$popularStores[$i]['permaLink'];
            }
            $popularStoreUrl = HTTP_PATH_LOCALE .$popularStores[$i]['permaLink'];
            $popularStoresContent .=
                '<li '.$class.'>
                    <a title='.$popularStores[$i]['name'].' 
                    href='.$popularStoreUrl.'>'.ucfirst(self::substring($popularStores[$i]['name'], 200))
                    .'</a>
                </li>';
        }
        $popularStoresContent .='</ul></div>';
        return $popularStoresContent;
    }
    
    public static function getShopsByFallback($storeIds)
    {
        foreach ($storeIds as $storeId) {
            $storeExists =  \KC\Repository\Shop::getShopData($storeId);
            if ($storeExists) {
                $cacheStatus = true;
            } else {
                $cacheStatus = false;
                return $cacheStatus;
            }
        }
        return $cacheStatus;
    }

    public function shopsAlsoViewedWidget($shopId, $shopName)
    {
        $shopsAlsoViewed =  \KC\Repository\Shop::getShopsAlsoViewed($shopId);
        if ($shopsAlsoViewed[0]['shopsViewedIds'] != '') {
            $similarStoresViewedContent = self::getSimilarStoresViewedDivContent($shopName);
            $storeIds = explode(',', $shopsAlsoViewed[0]['shopsViewedIds']);
            $storePresent = self::getShopsByFallback($storeIds);
            if ($storePresent) {
                foreach ($storeIds as $storeId) {
                    $similarStoresViewedContent .= self::addLiOfSimilarStoresViewedContent($storeId);
                }
            } else {
                $topFiveSimilarShopsViewed =  \KC\Repository\Shop::getSimilarShopsForAlsoViewedWidget($shopId, 5);
                foreach ($topFiveSimilarShopsViewed as $similarShopId) {
                    $similarStoresViewedContent .= self::addLiOfSimilarStoresViewedContent($similarShopId);
                }
            }
            $similarStoresViewedContent .='</ul></div>';
        } else {
            $similarStoresViewedContent = '';
        }
        return $similarStoresViewedContent;
    }

    public static function addLiOfSimilarStoresViewedContent($shopId)
    {
        $storeDetails = \KC\Repository\Shop::getShopInformation($shopId);
        $similarStoresViewedContent =
            '<li>
                <a title='.$storeDetails[0]['name'].' 
                href='.HTTP_PATH_LOCALE.$storeDetails[0]['permaLink'].'>'.ucfirst(self::substring($storeDetails[0]['name'], 200))
                .'</a>
            </li>';
        return $similarStoresViewedContent;
    }

    public function getSimilarStoresViewedDivContent($shopName)
    {
        return $similarStoresViewedContent =
        '<div class="block">
            <div class="intro">
                <h4>'.
                str_replace('[shop]', $shopName, $this->__translate('Other people who have viewed [shop] also viewed'))
                .'</h4>
            </div>
        <ul class="tags">';
    }
}