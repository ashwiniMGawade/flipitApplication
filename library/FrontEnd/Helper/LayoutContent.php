<?php
class FrontEnd_Helper_LayoutContent
{
    public static function loadFlipitHomePage($flipitUrl)
    {
        $htmlPath = '';
        $flipit = new \Zend_View();
        $flipit->setBasePath(APPLICATION_PATH . '/modules/flipit/views');
        $httpScheme = \FrontEnd_Helper_viewHelper::getServerNameScheme();
        if($flipitUrl == 'http://'.$httpScheme.'.flipit.com'
            || $flipitUrl == 'flipit.com'
            || $flipitUrl =='http://flipit.com') :
            \zend_Controller_Front::getInstance()->getRequest()
            ->getControllerName() == 'index'
            ? $htmlPath = 'index/index.phtml'
            : $htmlPath = 'error/error.phtml';
            return $flipitHomePage = array('viewObject' => $flipit, 'htmlPath' => $htmlPath);
        endif;
    }

    public static function loadCanonical($canonical)
    {

        $localePath = '';
        if(LOCALE!='en') :
            $localePath = HTTP_PATH_LOCALE;
        else :
            $localePath = HTTP_PATH;
        endif;
        $canonicalUrl  = '';
        if(isset($canonical)):
            if($canonical=='' || $canonical==null):
                $canonicalUrl =  array('rel' => 'canonical',
                        'href' => rtrim($localePath, '/')
                        );
            else:
                $canonicalUrl =  array('rel' => 'canonical',
                        'href' => $localePath . strtolower($canonical)
                        );
            endif;
        endif;
        return $canonicalUrl ;
    }

    public static function loadRobots($page, $robotOfDummyPages)
    {
        $robots = '';
        if(isset($page) && $page != ''
                && \zend_Controller_Front::getInstance()
                ->getRequest()->getControllerName() == 'search'):
                $robots = 'noindex, follow';
        elseif (strtolower(\zend_Controller_Front::getInstance()->getRequest()->getControllerName()) == 'login'
                        && strtolower(
                            \zend_Controller_Front::getInstance()
                            ->getRequest()->getActionName() == 'forgotpassword'
                        )
                ):
                $robots = 'noindex, follow';
        else:
            if(\Zend_Controller_Front::getInstance()->getRequest()->getParam('page', null) > 1):
                $robots = 'noindex, follow';
            else:
                if($robotOfDummyPages) :
                    $robots = $robotOfDummyPages;
                else :
                    $robots = 'index, follow';
                endif;
            endif;
        endif;
        return $robots;
    }

    public static function loadGoogleTagManager()
    {
        $googleTagManager = "<!-- Google Tag Manager -->
            <noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=GTM-W87MZ3\"
            height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-W87MZ3');</script>
            <!-- End Google Tag Manager -->";

        return $googleTagManager;
    }

    public static function loadFavicon()
    {
        $favicon = '';
        if(LOCALE=='') :
            $favicon = array( 'rel' => 'shortcut icon',
                'href' => HTTP_PATH . 'public/images/favicon.ico',
                'type' => 'image/x-icon'
            );
        else :
            $favicon =  array( 'rel' => 'shortcut icon',
                'href' => HTTP_PATH . 'public/flipit/images/favicon.ico',
                'type' => 'image/x-icon'
            );
        endif;
        return $favicon;
    }

    public static function loadFacebookMeta(
        $facebookTitle,
        $facebookShareUrl,
        $facebookbImage,
        $facebookDescription,
        $facebookLocale
    ) {
        $fb = new \Zend_View();
        $fb->setScriptPath(APPLICATION_PATH . '/layouts/scripts/');
        $fb->assign('facebookTitle', $facebookTitle);
        $fb->assign('facebookShareUrl', $facebookShareUrl);
        $fb->assign('facebookbImage', $facebookbImage);
        $fb->assign('facebookDescription', $facebookDescription);
        $fb->assign('facebookLocale', $facebookLocale);
        $facebookMetaUrl = 'facebookMeta.phtml';
        return $facebookMetaUrl;
    }
    
    public static function loadTwitterMeta($twitterDescription, $twitterSite)
    {
        $twitter = new \Zend_View();
        $twitter->setScriptPath(APPLICATION_PATH . '/layouts/scripts/');
        $twitter->assign('twitterDescription', $twitterDescription);
        $fb->assign('twitterSite', $twitterSite);
        $twitterMetaUrl = 'twitterMeta.phtml';
        return $twitterMetaUrl;
    }

    public static function homePageBanner($homePageBanner)
    {

        $homePageBannerHtml = '';
        if (!empty($homePageBanner)) {
            $homePageWidgetBannerPath = PUBLIC_PATH_CDN. trim(
                $homePageBanner[0]['homepagebanner_path']
                . $homePageBanner[0]['homepagebanner_name']
            );
            $homePageBannerHtml =
                '<div class="block-image">
                    <div class="image-holder">
                        <div class="image-frame">
                            <img class="position-cover" src="' . $homePageWidgetBannerPath .'" alt="'
                            . $homePageBanner[0]['homepage_widget_banner_name'] .'"
                            title="' . $homePageBanner[0]['homepage_widget_banner_name'] .'">
                        </div>
                    </div>
                </div>';
        }
        return $homePageBannerHtml;
    }

    public static function showMainContainerDiv()
    {
        $divShow = false;
        if (
            \zend_Controller_Front::getInstance()->getRequest()->getControllerName()!= 'index'
            && \zend_Controller_Front::getInstance()->getRequest()->getControllerName()!= 'plus'
        ) {
            $divShow = true;
        }
        return $divShow;
    }

    public static function generateMainMenu()
    {
        return $navigationString ='<nav id="nav">' . self::getUlOfMainMenu() . '</nav>';
    }
     
    public static function getUlOfMainMenu($navigation = '')
    {
        $mainMenu = menu::getFirstLevelMenu($navigation);
        $classForFlipIt = LOCALE == '' ? "kc-menu" : 'flipit-menu';
        $ulOfMainMenu =
        '<ul>';
        if ($navigation == 'mobile') {
            $ulOfMainMenu .=
            '<li>
                <a href="'. HTTP_PATH_LOCALE.'">'.FrontEnd_Helper_viewHelper::__translate('Home').' </a>
            </li>';
        }

        foreach ($mainMenu as $menu) {
            if ($navigation == 'mobile') {
                $cssClassForLastLi = strtolower($menu['name']) == FrontEnd_Helper_viewHelper::__translate('category')
                ? $classForFlipIt: '';
            } else {
                $cssClassForLastLi = strtolower($menu['name']) == 'plus' ? $classForFlipIt: '';
            }
            $stringReplacedMenuUrlVariable = str_replace("-", "", $menu['url']);
            $stringReplacedtop20Variable = str_replace("-", "", FrontEnd_Helper_viewHelper::__link('link_top-20'));
            if ($stringReplacedMenuUrlVariable === $stringReplacedtop20Variable && $navigation == 'mobile') {
                $ulOfMainMenu.=
                    '<li class="' . $cssClassForLastLi .'" id="'. $menu["name"] .'">
                        <a id="'. $menu["name"] . '" name="'. $menu["name"] . '" 
                            class="" href="'. HTTP_PATH_LOCALE  . $menu['url'] . '">' . ucfirst($menu["name"])
                        . '</a>
                    </li>
                    <li class="' . $cssClassForLastLi .'" id="plus">
                        <a id="plus" name="plus" 
                            class="" href="'. HTTP_PATH_LOCALE  . 'plus">' . ucfirst('plus')
                        . '</a>
                    </li>';
            } else {
                $ulOfMainMenu.=
                '<li class="' . $cssClassForLastLi .'" id="'. $menu["name"] .'">
                    <a id="'. $menu["name"] . '" name="'. $menu["name"] . '" 
                        class="" href="'. HTTP_PATH_LOCALE  . $menu['url'] . '">' . ucfirst($menu["name"])
                    . '</a>';
                
                if (strpos($menu['url'], '09-e')) {
                    $ulOfMainMenu.=self::generateTopShopsDropdown();
                }
                $ulOfMainMenu.='</li>';
            }
        }
        
        $ulOfMainMenu .=
        '</ul>';
        return $ulOfMainMenu;
    }

    public static function generateTopShopsDropdown()
    {
        $topShops = self::getTopShopForDropdown();
       
        $topShopsDropdown =
        '<div class="drop-box">
            <div class="inner-box">
                <ul class="info-area">';
        $shopsPerColumn = 1;
        foreach ($topShops as $topShop) {
            if ($shopsPerColumn == 7 || $shopsPerColumn == 13 || $shopsPerColumn== 19 || $shopsPerColumn == 25) {
                $topShopsDropdown .='</ul><ul class="info-area">';
            }
            $topShopsDropdown .='<li><a href="'. HTTP_PATH_LOCALE. $topShop['shop']['permaLink']. '">'. $topShop['shop']['name'] . '</a></li>';
            $shopsPerColumn++;
        }
        $topShopsDropdown.=
            '</ul></div>
            <a class="btn" 
                href="'. HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('link_alle-winkels-09-e').'">'
                .FrontEnd_Helper_viewHelper::__translate('View all shops'). '</a>
        </div>';
        return $topShopsDropdown;
    }

    public static function getTopShopForDropdown()
    {
        $topShops = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            "all_popularShopsForDropdown_list",
            array(
                'function' => 'Shop::getPopularStoresForDropDown',
                'parameters' => array(30)
            ),
            ''
        );
        return $topShops;
    }

    public static function generateMobileMenu($navigation)
    {
        return self::getUlOfMainMenu($navigation);
    }

    public static function getMostPopularCouponOnEarth()
    {
        $splashInformation = \FrontEnd_Helper_viewHelper::getSplashInformation();
        if (!empty($splashInformation)) {
            $locale = $splashInformation[0]['locale'];
            //$connectionWithSiteDatabase = \BackEnd_Helper_DatabaseManager::addConnection($locale);
            //$offer = new \KC\Repository\Offer($connectionWithSiteDatabase['connName']);
            $offer = new \KC\Repository\Offer();
            $mostPopularCoupon = $offer->getSplashPagePopularCoupon($splashInformation[0]['offerId']);
            //\BackEnd_Helper_DatabaseManager::closeConnection($connectionWithSiteDatabase['adapter']);
            return array('locale' => $locale,'mostPopularCoupon' => $mostPopularCoupon);
        } else {
            return array('locale' => '','mostPopularCoupon' => '');
        }
    }
}
