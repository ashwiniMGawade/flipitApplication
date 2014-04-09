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

        return $flipitHomePage = array('viewObject'=>$flipit,'htmlPath'=>$htmlPath);
        endif;
    }

    public static function loadCanonical($canonical)
    {
        # add for all locale
        $localePath = '';

        if(LOCALE!='en') :
            $localePath = HTTP_PATH_LOCALE;
        else :
            $localePath = HTTP_PATH;
        endif;

        # end code for all locale
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

        elseif (strtolower(
                zend_Controller_Front::getInstance()
                ->getRequest()->getControllerName()) == 'login'
                && strtolower(
                    zend_Controller_Front::getInstance()
                    ->getRequest()->getActionName() == 'forgotpassword'
                    )
                ):

                $robots = 'noindex, follow';

        else:
            #add noindex for every page after first page
            if(Zend_Controller_Front::getInstance()
                    ->getRequest()->getParam('page' , null) > 1) :

                $robots = 'noindex, follow';

            else:
                # robot keyword property is set by any action
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

        if(strtolower(zend_Controller_Front::getInstance()
            ->getRequest()->getControllerName()) == 'error') :

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

            $googleAnalyticsCode = 	"<script type='text/javascript'>

            (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '$googleAnalyticsKey' , '$websiteName');
            ga('send', 'pageview');";

            if($pushSetAccount!='') :

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

    public static function loadFacebookMeta($facebookTitle, $facebookShareUrl, $facebookbImage, $facebookDescription, $facebookLocale)
    {
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
}
