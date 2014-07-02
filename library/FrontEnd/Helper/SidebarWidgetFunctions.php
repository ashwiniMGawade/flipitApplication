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
        $socialMediaTitle = "<h2>".$this->__translate('Follow us')."</h2>";
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
                        <li><a class='facebook' href='".$facebookPageLink."' target='_blank'></a></li>
                        <li><a class='twitter' href='".$twitterPageLink."' target='_blank'></a></li>
                        <li><a class='google' href='".$googlePlusPageLink."' target='_blank'></a></li>
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
        $allPopularCategories = Category::getPopularCategories();
        $categorySidebarWodget =
        '<div class="block">
            <div class="intro">
            <h2 class="sidebar-heading">'. $this->__translate('All Categories').'</h2></div>
                <ul class="tags">';
        for ($categoryIndex=0; $categoryIndex < count($allPopularCategories); $categoryIndex++) {
            $categorySidebarWodget.='
                    <li>
                        <a href="'. HTTP_PATH_LOCALE .
                        FrontEnd_Helper_viewHelper::__link('link_categorieen'). '/' .
                        $allPopularCategories[$categoryIndex]['category']['permaLink'].'">' .
                        $allPopularCategories[$categoryIndex]['category']['name'] .
                        '</a>' . '
                    </li>';
        }
        $categorySidebarWodget.=
                '</ul></div>';
        return $categorySidebarWodget;
    }


    public function browseByStoreWidget()
    {
        $browseByStoreWidget =
        '<div class="block">
            <div class="intro">
               <h2>'.$this->__translate('Browse by Store') .'</h2>
            </div>
            <div class="alphabet-holder">
                <ul class="alphabet">';
        foreach (range('A', 'Z') as $oneCharacter) {
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

    public function getSidebarWidget($array = array(), $page = '')
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
        if (count($pageWidgets) > 0) {
            for ($i=0; $i<count($pageWidgets[0]['widget']); $i++) {
            }
        }
        return $sidebarWidgets;
    }
    
    public function popularShopWidget()
    {
        $popularStores = self::getStoreForFrontEnd('popular', 25);
        $popularStoresContent = '<div class="block"><div class="intro">
                   <h2>'.$this->__translate('Populaire Winkels').'</h2>
                   <span>'
                   .$this->__translate('Grab a promotional code, discount code or voucher for').date(' F Y').'</span>
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
            $popularStoresContent .=
                '<li '.$class.'>
                    <a title='.$popularStores[$i]['shop']['name'].' 
                    href='.$popularStoreUrl.'>'.ucfirst(self::substring($popularStores[$i]['shop']['name'], 200))
                    .'</a>
                </li>';
        }
        $popularStoresContent .='</ul></div>';
        return $popularStoresContent;
    }
}