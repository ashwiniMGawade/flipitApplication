<?php
class FrontEnd_Helper_LayoutContent
{
    public static function loadFlipitHomePage($flipitUrl)
    {
        $htmlPath = '';
        $flipit = new Zend_View();
        $flipit->setBasePath(APPLICATION_PATH . '/modules/flipit/views');
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        if($flipitUrl == 'http://'.$httpScheme.'.flipit.com'
            || $flipitUrl == 'flipit.com'
            || $flipitUrl =='http://flipit.com') :
            zend_Controller_Front::getInstance()->getRequest()
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
                && zend_Controller_Front::getInstance()
                ->getRequest()->getControllerName() == 'search'):
                $robots = 'noindex, follow';
        elseif (strtolower(zend_Controller_Front::getInstance()->getRequest()->getControllerName()) == 'login'
                        && strtolower(
                            zend_Controller_Front::getInstance()
                            ->getRequest()->getActionName() == 'forgotpassword'
                        )
                ):
                $robots = 'noindex, follow';
        else:
            if(Zend_Controller_Front::getInstance()->getRequest()->getParam('page', null) > 1):
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
        $fb = new Zend_View();
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
        $twitter = new Zend_View();
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
            $homePageWidgetBannerPath = PUBLIC_PATH_CDN. trim($homePageBanner['homepagebanner_path'] . $homePageBanner['homepagebanner_name']);
            $homePageBannerHtml =
                '<div class="block-image">
                    <div class="image-holder">
                        <div class="image-frame">
                            <img src="' . $homePageWidgetBannerPath .'" alt="' . $homePageBanner['homepage_widget_banner_name'] .'">
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
            zend_Controller_Front::getInstance()->getRequest()->getControllerName()!= 'index'
            && zend_Controller_Front::getInstance()->getRequest()->getControllerName()!= 'plus'
        ) {
            $divShow = true;
        }
        return $divShow;
    }

    public static function generateMainMenu()
    {
        return $navigationString ='<nav id="nav">' . self::getUlOfMainMenu() . '</nav>';
    }
     
    public static function getUlOfMainMenu()
    {
        $mainMenu = menu::getFirstLevelMenu();
        $ulOfMainMenu =
        '<ul>';
        $classForFlipIt = LOCALE=='' ? "kc-menu" : 'flipit-menu';
        foreach ($mainMenu as $menu) {
            $cssClassForLastLi = strtolower($menu['name'])=='plus' ? $classForFlipIt: '';
            $ulOfMainMenu.=
            '<li class="' . $cssClassForLastLi .'" id="'. $menu["name"] .'">
                <a id="'. $menu["name"] . '" name="'. $menu["name"] . '" 
                    class="" href="'. HTTP_PATH_LOCALE  . $menu['url'] . '">' . ucfirst($menu["name"])
                . '</a>
            </li>';
        }
        $ulOfMainMenu .=
        '</ul>';
        return $ulOfMainMenu;
    }

    public static function generateMobileMenu()
    {
        return self::getUlOfMainMenu();
    }
    public static function getMostPopularCouponOnEarth()
    {
        $splashInformation = FrontEnd_Helper_viewHelper::getSplashInformation();
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
}
