<?php
class FrontEnd_Helper_LayoutContent
{
    public static function loadFlipitHomePage($flipitUrl)
    {
        $htmlPath = '';
        $flipit = new Zend_View();
        $flipit->setBasePath(APPLICATION_PATH . '/modules/flipit/views');
        if($flipitUrl == 'http://www.flipit.com'
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

    public static function loadGoogleAnalyticsCode()
    {
        $googleAnalyticsKey = 'UA-17691171-4';
        $websiteName = 'kortingscode.nl';
        $pushSetAccount = 'UA-17691171-1';
        # set google analytics code for locale based like be/in etc
        if(LOCALE!='') :
            $googleAnalyticsKey = 'UA-17691171-3';
            $websiteName = 'flipit.com';
            if(LOCALE=='be') :
                $pushSetAccount = 'UA-17691171-5';
            else :
                $pushSetAccount = '';
            endif;
        endif;
        $googleAnalyticsCode = '';

        if(strtolower(zend_Controller_Front::getInstance()->getRequest()->getControllerName()) == 'error') :
            $googleAnalyticsCode = "<script type='text/javascript'>
            (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '$googleAnalyticsKey' , '$websiteName');
            ga('send', 'pageview');
            ga('send', 'event', 'error', '404', 'page:ref' , document.location.pathname
           + document.location.search + ':' + document.referrer  );
           </script>";
        else :
            $googleAnalyticsCode =  "<script type='text/javascript'>
            (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '$googleAnalyticsKey' , '$websiteName');
            ga('send', 'pageview');";

            if ($pushSetAccount!='') :
                $googleAnalyticsCode .= "var _gaq = _gaq || [];
               _gaq.push(['_setAccount', '$pushSetAccount']);
               _gaq.push(['_trackPageview']);
               (function () { var ga = document.createElement('script'); ga.type = 'text/javascript';
               ga.async = true; ga.src = ('https:' == document.location.protocol ?
               'https://' : 'http://')
               + 'stats.g.doubleclick.net/dc.js';
               var s = document.getElementsByTagName('script')[0];
               s.parentNode.insertBefore(ga, s); }
               )();";
            endif;
            $googleAnalyticsCode .="</script>";
        endif;
        return $googleAnalyticsCode;
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

    public static function generateMainMenu($menuType = '')
    {
        $mainMenu = menu::getFirstLevelMenu();
        $mainMenuCount = count($mainMenu);
        $mainMenuvalue = 0;
        $menuNavId = 'nav';
        $mobileMenuHeader = '';
        if ($menuType == 'mobile') {
            $menuNavId = 'menu';
            $mobileMenuHeader = '<h1>Korting pakken</h1>';
        }
        $navigationString ='<nav id="'.$menuNavId.'"><ul>'.$mobileMenuHeader;
        foreach ($mainMenu as $menu) {
            $permalink = RoutePermalink::getPermalinks($menu['url']);
            if (count($permalink) > 0) {
                $link = $permalink[0]['permalink'];
            } else {
                $link = $menu['url'];
                if ($menu['url']== FrontEnd_Helper_viewHelper::__link('link_inschrijven')) {
                    if (Auth_VisitorAdapter::hasIdentity()) {
                        $link = FrontEnd_Helper_viewHelper::__link('link_profiel');
                    } else {
                        $link = $menu['url'];
                    }
                }
            }
            if ($mainMenuvalue == $mainMenuCount) {
                $menuName = str_replace(' ', '-', trim(strtolower($menu["name"])));
                $navigationString .=
                    '<li><a rel="toggel" id="'. $menuName . '" name="'. $menuName. '" 
                    class="show_hide1" href="javascript:void(0);">' . $menu["name"] . '</a>
                    </li>';
            } else {
                $menuName = str_replace(' ', '-', trim(strtolower($menu["name"])));
                preg_match('/http:\/\//', $menu['url'], $matches);
                if (count($matches) > 0) {
                    $navigationString .=
                        '<li><a id="'. $menuName. '" name="'. $menuName. '" 
                        class="" href="'.  $menu['url'] . '">' . $menu["name"] . '</a></li>';
                } else {
                    $navigationString .=
                        '<li><a id="'. $menuName. '" name="'. $menuName. '" 
                        class="" href="'. HTTP_PATH_LOCALE  . $link . '">' . $menu["name"] . '</a>
                        </li>';
                }
            }
            $mainMenuvalue++;
        }
        $navigationString .= '</ul></nav>';
        return $navigationString;
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
