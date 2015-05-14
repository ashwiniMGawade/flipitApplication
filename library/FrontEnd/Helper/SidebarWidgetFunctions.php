<?php
class FrontEnd_Helper_SidebarWidgetFunctions extends FrontEnd_Helper_viewHelper
{
    public function getSidebarWidget($array = array(), $page = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p, w, refpage')
            ->from('KC\Entity\Page', 'p')
            ->leftJoin('p.pagewidget', 'w')
            ->leftJoin('w.widget', 'refpage')
            ->where("p.permalink=".$queryBuilder->expr()->literal("$page"))
            ->andWhere('w.stauts = 1')
            ->andWhere('p.deleted = 0');
        $pageWidgets = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $sidebarWidgets = '';
        if (count($pageWidgets) > 0) {
            for ($i=0; $i<count($pageWidgets[0]['pagewidget']); $i++) {
            }
        }
        return $sidebarWidgets;
    }

    public function sidebarWidgets($widgetType, $obj)
    {
        $widgets = \KC\Repository\PageWidgets::getWidgetsByType($widgetType);
        $pageWidgets = '';
        if (!empty($widgets)) {
            foreach ($widgets as $widget) {
                $widgetTitle = strtolower($widget['widget']['function_name']);
                if ($widget['widget']['showWithDefault'] == 1) {
                    $this->getNonDefaultWidget($widget);
                } else {
                    if (!empty($widgetTitle)) {
                        $this->$widgetTitle($widgetType, $obj);
                    }
                }
            }
        }
        return $pageWidgets;
    }

    public function getNonDefaultWidget($widget)
    {
        if (!empty($widget['widget']['content'])) {
            echo str_replace('<br />', '', html_entity_decode($widget['widget']['content']));
        }
    }

    public function socialCodeWidget($widgetType, $obj)
    {
        if (!empty($obj->zendForm)) {
            echo $obj->partial('socialcode/social-code.phtml', array('zendForm' => $obj->zendForm));
        }
    }

    public function signUpWidget($widgetType, $obj)
    {
        if ($widgetType == 'no-money-shops' || $widgetType == 'money-shops') {
            if ($obj->currentStoreInformation[0]['showSignupOption']) {
                echo $obj->esi(
                    $obj->locale.'signup/signupwidget?shopId='.$obj->currentStoreInformation[0]['id']
                    .'&signupFormWidgetType=sidebarWidget&shopLogoOrDefaultImage='
                );
            }
        } else {
            echo $obj->esi(
                $obj->locale.'signup/signupwidget?shopId='.''
                .'&signupFormWidgetType=sidebarWidget&shopLogoOrDefaultImage='
            );
        }
    }

    public function shopLatestNewsWidget($widgetType, $obj)
    {
        if (!empty($obj->latestShopUpdates)) {
            echo $obj->partial('store/_latestNews.phtml', array('latestShopUpdates' => $obj->latestShopUpdates));
        }
    }

    public function popularEditorWidget($widgetType, $obj)
    {
        $howToUseGuidePermalink = "how-to/".$obj->currentStoreInformation[0]['permaLink'];
        if (!empty($obj->currentStoreInformation[0]['howtoguideslug'])) {
            $howToUseGuidePermalink =
                $obj->currentStoreInformation[0]['permaLink']. '/'
                . $obj->currentStoreInformation[0]['howtoguideslug'];
        }
        $actualUrlWithoutDoubleSlash = explode("//", $obj->currentStoreInformation[0]['actualUrl']);
        if (isset($actualUrlWithoutDoubleSlash[1])):
            $actualUrl = $actualUrlWithoutDoubleSlash[1];
        else:
            $actualUrl = $obj->currentStoreInformation[0]['actualUrl'];
        endif;
        $frontShopHeaderHelper = new FrontEnd_Helper_ShopHeaderPartialFunctions();
        $disqusReplyCounter = $frontShopHeaderHelper->getDisqusReplyCounter($obj->currentStoreInformation[0]);

        $shopEditor = '';
        if($obj->shopEditor != null):
            $shopEditor = $obj->partial(
                'store/_storeEditor.phtml',
                array(
                   'shopEditor' => array(
                        $obj->shopEditor
                    ),
                   'shop' => $obj->currentStoreInformation[0],
                   'howToUseGuidePermalink'=>$howToUseGuidePermalink,
                   'actualUrl'=>$actualUrl,
                   'disqusReplyCounter' => $disqusReplyCounter
                )
            );
        endif;
        echo $shopEditor;
    }

    public function shopChainWidget($shopChain)
    {
        $chain = '';
        if ($shopChain) {
            $chain = '<article class="block">'.$shopChain.'</article>';
        }
        echo $chain;
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
        echo $categoriesSidebarWidget;
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
        echo $popularStoresContent;
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

    public function shopsAlsoViewedWidget($widgetType, $obj)
    {
        $similarStoresViewedContent = '';
        if (!empty($obj->currentStoreInformation[0]['id'])) {
            $shopId = $obj->currentStoreInformation[0]['id'];
            $shopName = $obj->currentStoreInformation[0]['name'];
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
            }
        }
        echo $similarStoresViewedContent;
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

    public function plusTopPopularOffers($widgetType, $obj)
    {
        $topPopularOffers = '';
        if (!empty($obj->topPopularOffers)) {
            $topPopularOffers = $obj->partial(
                'plus/_topPopularOffers.phtml',
                array(
                    'topPopularOffers' => $obj->topPopularOffers,
                )
            );
        }
        echo $topPopularOffers;
    }

    public function plusRecentlyAddedArticles($widgetType, $obj)
    {
        $recentlyAddedArticles = '';
        if (!empty($obj->recentlyAddedArticles)) {
            $recentlyAddedArticles = $obj->partial(
                'plus/_recentlyAddedArticles.phtml',
                array(
                    'recentlyAddedArticles' => $obj->recentlyAddedArticles
                )
            );
        }
        echo $recentlyAddedArticles;
    }
}