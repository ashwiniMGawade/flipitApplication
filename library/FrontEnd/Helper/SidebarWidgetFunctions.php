<?php
class FrontEnd_Helper_SidebarWidgetFunctions extends FrontEnd_Helper_viewHelper
{
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

    public function socialMediaWidget($socialMediaUrl = '', $type = null)
    {
        $socialMediaTitle = "<h4>".$this->__translate('Follow us')."</h4>";
        $facebookPageLink = 'https://www.facebook.com/kortingsbonnen';
        $twitterPageLink = 'https://twitter.com/codekorting';
        $googlePlusPageLink = 'https://plus.google.com/+KortingscodeNl';

        if (LOCALE != '') {
            $facebookPageLink = 'https://www.facebook.com/flipitcom';
            $twitterPageLink = 'https://twitter.com/Flipit';
            $googlePlusPageLink = 'https://plus.google.com/104667362431888724932/about';
        }

        $socialMedia = "
            <article class='block'>
                <div class='social-networks'>
                    <div class='intro'>".$socialMediaTitle."</div>
                    <ul class='share-list'>
                        <li><a class='facebook' href='".$facebookPageLink."' target='_blank' rel='nofollow'></a></li>
                        <li><a class='twitter' href='".$twitterPageLink."' target='_blank' rel='nofollow'></a></li>
                        <li><a class='google' href='".$googlePlusPageLink."' target='_blank' rel='nofollow'></a></li>
                        <li class='share-text'>"
                        .$this->__translate('Follow us for the latest vaucher codes, plus a daily digest of our biggest offers')
                        ."</li>
                    </ul>
                </div>
            </article>";
        return $socialMedia;
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
    
    public function popularShopWidget()
    {
        $popularStores = \KC\Repository\Shop::getAllPopularStores(20);
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
            if ($popularStores[$i]['popularshops']['deepLink']!=null) {
                $popularStoreUrl = $popularStores[$i]['popularshops']['deepLink'];
            } elseif ($popularStores[$i]['popularshops']['refUrl']!=null) {
                $popularStoreUrl = $popularStores[$i]['popularshops']['refUrl'];
            } elseif ($popularStores[$i]['popularshops']['actualUrl']) {
                $popularStoreUrl = $popularStores[$i]['popularshops']['actualUrl'];
            } else {
                $popularStoreUrl = HTTP_PATH_LOCALE .$popularStores[$i]['popularshops']['permaLink'];
            }
            $popularStoreUrl = HTTP_PATH_LOCALE .$popularStores[$i]['popularshops']['permaLink'];
            $popularStoresContent .=
                '<li '.$class.'>
                    <a title='.$popularStores[$i]['popularshops']['name'].' 
                    href='.$popularStoreUrl.'>'.ucfirst(self::substring($popularStores[$i]['popularshops']['name'], 200))
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
                href='.$storeDetails[0]['permaLink'].'>'.ucfirst(self::substring($storeDetails[0]['name'], 200))
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