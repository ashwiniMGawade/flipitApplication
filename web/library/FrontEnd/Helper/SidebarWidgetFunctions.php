<?php
class FrontEnd_Helper_SidebarWidgetFunctions extends FrontEnd_Helper_viewHelper
{
    public function sidebarWidgets($widgetType, $currentView, $referenceId = '')
    {
        $widgets = \KC\Repository\PageWidgets::getFrontendWidgetList($widgetType, $referenceId);
        if (!empty($widgets)) {
            foreach ($widgets as $widget) {
                $widgetFunctionName = strtolower($widget['widget']['function_name']);
                if ($widget['widget']['showWithDefault'] == 1) {
                    $this->getNonDefaultWidget($widget);
                } else {
                    if (!empty($widgetFunctionName)) {
                        $this->$widgetFunctionName($widgetType, $currentView);
                    }
                }
            }
        }
        return;
    }

    public function getNonDefaultWidget($widget)
    {
        if (!empty($widget['widget']['content'])) {
            echo '<div class="htmlWidgetWrapper">'.str_replace('<br />', '', html_entity_decode($widget['widget']['content'])).'</div>';
        }
    }

    public function socialCodeWidget($widgetType, $currentView)
    {
        if (!empty($currentView->zendForm)) {
            echo $currentView->partial('socialcode/social-code.phtml', array('zendForm' => $currentView->zendForm));
        }
    }

    public function signUpWidget($widgetType, $currentView)
    {
        if ($widgetType == 'no-money-shops' || $widgetType == 'money-shops') {
            if ($currentView->currentStoreInformation[0]['showSignupOption']) {
                echo $currentView->esi(
                    $currentView->locale.'signup/signupwidget?shopId='.$currentView->currentStoreInformation[0]['id']
                    .'&signupFormWidgetType=sidebarWidget&shopLogoOrDefaultImage='
                );
            }
        } else {
            echo $currentView->esi(
                $currentView->locale.'signup/signupwidget?shopId='.''
                .'&signupFormWidgetType=sidebarWidget&shopLogoOrDefaultImage='
            );
        }
    }

    public function shopLatestNewsWidget($widgetType, $currentView)
    {
        if (!empty($currentView->latestShopUpdates)) {
            echo $currentView->partial('store/_latestNews.phtml', array('latestShopUpdates' => $currentView->latestShopUpdates));
        }
    }

    public function popularEditorWidget($widgetType, $currentView)
    {
        $shopEditor = '';
        if (!empty($currentView->currentStoreInformation)) {
            $howToUseGuidePermalink = "how-to/".$currentView->currentStoreInformation[0]['permaLink'];
            if (!empty($currentView->currentStoreInformation[0]['howtoguideslug'])) {
                $howToUseGuidePermalink =
                    $currentView->currentStoreInformation[0]['permaLink']. '/'
                    . $currentView->currentStoreInformation[0]['howtoguideslug'];
            }
            $actualUrl = '';
            if (!empty($currentView->currentStoreInformation[0]['actualUrl'])) {
                $actualUrlWithoutDoubleSlash = explode("//", $currentView->currentStoreInformation[0]['actualUrl']);
                if (isset($actualUrlWithoutDoubleSlash[1])) {
                    $actualUrl = $actualUrlWithoutDoubleSlash[1];
                } else {
                    $actualUrl = $currentView->currentStoreInformation[0]['actualUrl'];
                }
            }
            $frontShopHeaderHelper = new FrontEnd_Helper_ShopHeaderPartialFunctions();
            $disqusReplyCounter = $frontShopHeaderHelper->getDisqusReplyCounter($currentView->currentStoreInformation[0]);
            if ($currentView->shopEditor != null):
                $shopEditor = $currentView->partial(
                    'store/_storeEditor.phtml',
                    array(
                       'shopEditor' => array(
                            $currentView->shopEditor
                        ),
                       'shop' => $currentView->currentStoreInformation[0],
                       'howToUseGuidePermalink'=>$howToUseGuidePermalink,
                       'actualUrl'=>$actualUrl,
                       'disqusReplyCounter' => $disqusReplyCounter
                    )
                );
            endif;
        }
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

    public function shopsAlsoViewedWidget($widgetType, $currentView)
    {
        $similarStoresViewedContent = '';
        if (!empty($currentView->currentStoreInformation[0]['id'])) {
            $shopId = $currentView->currentStoreInformation[0]['id'];
            $shopName = $currentView->currentStoreInformation[0]['name'];
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

    public function getPageEditorWidget($pageType, $currentObject)
    {
        $editorWidgetInformation = self::editorWidgetInformation($pageType);
        $editorId = !empty($editorWidgetInformation[0]['editorId'])
            ? $editorWidgetInformation[0]['editorId'] : '';
        $editorInformation = self::getEditorInformation($editorId);
        if ($editorInformation != '' && $editorWidgetInformation[0]['status'] != ''):
            echo $editorInformation = $currentObject->partial(
                'partials/_editorWidget.phtml',
                array(
                   'editorInformation' => $editorInformation,
                   'editorWidgetInformation' => $editorWidgetInformation
                )
            );
        endif;
    }

    public static function editorWidgetInformation($pageType)
    {
        $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($pageType);
        $editorWidgetInformation = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                $cacheKey.'_editor_data',
                array(
                    'function' =>
                    'KC\Repository\EditorWidget::getEditorWidgetData', 'parameters' => array($pageType)
                ),
                ''
            );
        return $editorWidgetInformation;
    }

    public static function getEditorInformation($editorId)
    {
        $editorInformation = '';
        if (!empty($editorId)) {
            $editorInformation = \FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache(
                    'user_'.$editorId.'_details',
                    array(
                        'function' =>
                        'KC\Repository\User::getUserDetails', 'parameters' => array($editorId)
                    ),
                    ''
                );
        }
        return $editorInformation;
    }


    public function plusTopPopularOffers($widgetType, $currentView)
    {
        $topPopularOffers = '';
        if (!empty($currentView->topPopularOffers)) {
            $topPopularOffers = $currentView->partial(
                'plus/_topPopularOffers.phtml',
                array(
                    'topPopularOffers' => $currentView->topPopularOffers
                )
            );
        }
        echo $topPopularOffers;
    }

    public function plusRecentlyAddedArticles($widgetType, $currentView)
    {
        $recentlyAddedArticles = '';
        if (!empty($currentView->recentlyAddedArticles)) {
            $recentlyAddedArticles = '<div class="in-block">';
            $recentlyAddedArticles .= $currentView->partial(
                'plus/_recentlyAddedArticles.phtml',
                array(
                    'recentlyAddedArticles' => $currentView->recentlyAddedArticles
                )
            );
            $recentlyAddedArticles .= "</div>";
        }
        echo $recentlyAddedArticles;
    }
}
