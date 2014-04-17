<?php

class FrontEnd_Helper_viewHelper
{
    ##################################################################################
    ################## REFACTORED CODE ###############################################
    ##################################################################################
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


    /**
     * sideChainWidget
     *
     * create sidebar chain widegt on shop page which show a chain related to that country
     *
     * @param integer $id          shop id
     * @param integer $chainItemId chain item id to fetch chain
     * @param string  $shopName    shop name
     *
     *
     * @return string
     */
    public static function sidebarChainWidget($id, $shopName = false, $chainItemId = false)
    {
        if ($shopName) {
            $chain = Chain::returnChainData($chainItemId, $id);
            if (! $chain) {
                return false;
            }

            $httpPathLocale = trim(HTTP_PATH_LOCALE, '/');
            $httpPath = trim(HTTP_PATH, '/');
            $getTranslate = Zend_Registry::get('Zend_Translate');
            $shopHeader = $getTranslate->translate("is an international shop");
            $widgetText = $getTranslate->translate("Check out the coupons and discounts from other countries when you're interested:");
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
			            "<li><a class='font14' href='%s' target='_blank'><span class='flag-cont'><img src='%s' /></span></a></li>",
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

        if (intval(@$matches[0]) > 0) {
            $permalink = explode('/'.$matches[0], $permalink);
            $permalink = $permalink[0];
        } else {
            $permalink = $permalink;
        }

        $splitPermalinkValue = explode('?', $permalink);

        if (!empty($splitPermalinkValue)) {
            $permalink = $splitPermalinkValue[0];
        } else {
            $permalink = $permalink;
        }

        if (LOCALE!='en') {
            $frontEndControllers = Zend_Controller_Front::getInstance();
            $controllerDirectory = $frontEndControllers->getControllerDirectory();
            $moduleNames = array_keys($controllerDirectory);
            $routeProperty = explode('/', $permalink);

            if (in_array($routeProperty[0], $moduleNames)) {
                $tempLanguage = ltrim($permalink, $routeProperty[0]);
                $permalink = ltrim($tempLanguage, '/');
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
        $value = 1;
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
            if ($value==$mainMenuCount) {
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
            $value++;
        }
        $navigationString .= '</ul></nav>';

        return $navigationString;
    }
    
    /**
     * Get footer data
     * @author Asharma
     * @version 1.0
     * @return array $data
     */
    public static function getFooterData()
    {
    	$footerData = Footer::getFooter();
    
    	return $footerData;
    
    }
    
    public static function getHeadMeta($headMetaValue)
    {
    	$domainName = HTTP_HOST;
    	if($domainName == "www.kortingscode.nl")
    	{
    		$site_name = "Kortingscode.nl";
    	}
    	else
    	{
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
    public static function storeSearchPanel()
    {
        $store = 0;
        $storeCharacter = "'".$store."'";
        $storeSearch = "<ul class='alphabet'><li><a id='0' class='' href='#0-9'>0-9</a></li>";
        
        foreach (range('A', 'Z') as $store) {
            $lastStoreClass = $store=='Z' ? 'last' : '';
            $storeCharacter = "'".$store."'";
            $storeSearch .="<li><a id='" . $store . "'  href='#".strtolower($store)."' class='".$lastStoreClass."'>$store</a></li>";
        }
        $storeSearch .="</ul>";
        return $storeSearch;
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
            case 'popular':
                $shopData = Offer::commongetpopularOffers($type, $limit, $shopId, $userId);
                break;
            case 'newest':
                $shopData = Offer::commongetnewestOffers($type, $limit, $shopId, $userId);
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
     * Common function to render sidebar widget of alphanumeric categories
     * @author cbhopal updated by kraj
     * @version 1.0
     * @param
     * @return string
     */
    public static function sideWidgetCalculator($type='default',$postion=1)
    {
        $margin_top = '';
        $trans = Zend_Registry::get('Zend_Translate');
        if ($postion!=0) {
        $margin_top ='style ="margin-top:20px;"';
        }
        if ($type=='search') {
            $string = "<div class='zoek-az-outer' ".$margin_top.">
            <div class='zoek-az-heading'>
            <h4>".$trans->translate('Zoek winkels A-Z')."</h4>
            </div>
            <div class='az-outer'>";

                foreach (range('A','Z') as $i) {
                    $string .= "<a href='" .HTTP_PATH_LOCALE ."alle-winkels#$i'><div class='az-col1 text-white'>
                    <h4>".$trans->translate($i)."</h4>
                    </div></a>";
                };
            $string .= "<a href='" .HTTP_PATH_LOCALE ."store/index/char/0'><div class='az-col1 text-white'>
            <h6><strong>123</strong></h6>
            </div></a>

            </div>

            <a class='mt20 fl' href='javascript:void(0);'><img src='".HTTP_PATH."public/images/front_end/blue-btn2.png' width='194' height='33' /></a>

            </div>";
        } else {

            $string = '<div class="gedeeld sidebar" >
            <h4 class="sidebar-heading">'.$trans->translate('Zoek winkels A-Z').'</h4>
            <div class="zoke-inner">';
            foreach (range('A','Z') as $i) {

                    $c = "'" . $i . "'";
                    $string .= '<a href="javascript:void(0)"  onclick="characterRedirect('. $c. ');" class="zoke-box">'.$i.'</a>';
                };
            $nine = "'abc'";
            $string .='<a href="javascript:void(0)" onclick="characterRedirect('.$nine.');" class="zoke-box">0-9</a>
            </div>
            </div>';
        }

        return $string;
    }

    /**
   * Common function for social media.
   * @author Raman updated by cbhopal
   * @version 1.0
   * @version 1.1
   * @param $url string
   * @param $title string
   * @param $controller string
   * @param $type string
   * @return string
   */
    public static function socialmediaSmall($url, $socialMediaTitle, $controller , $type = null)
    {

        if(strtolower($controller) == 'store' || strtolower($controller) == 'moneysavingguide'):
            $socialMediaUrl = HTTP_PATH . ltrim($_SERVER['REQUEST_URI'],'/') ;
            $socialMediaUrl = self::generateSocialLink($socialMediaUrl);
        else:
            $socialMediaUrl = HTTP_PATH;
        endif;

        if($type == 'widget'):
        $socialMedia="<div class='flike-outer'>
        <div id='fb-root'></div>
        <div class='fb-like' data-href='".$socialMediaUrl."' data-send='false' data-width='44' data-layout='box_count' data-show-faces='false'>&nbsp;
        </div>
        </div>
        <div class='flike-outer'>
        <a href='https://twitter.com/share' data-count='vertical' class='twitter-share-button' data-url='".$socialMediaUrl."' data-lang='nl' data-count = 'none'></a>
        </div>
        <div class='flike-outer'><div class='g-plus' data-action='share' data-annotation='vertical-bubble' data-height='60'></div>
        </div>";
        elseif ($type == 'popup'):
            $socialMedia="<div class='flike-outer'>
                            <div id='fb-root'></div>
                            <div class='fb-like' data-href='".$socialMediaUrl."' data-send='false' data-width='44' data-layout='box_count' data-show-faces='false'>&nbsp;
                            </div>
                            </div>
                            <div class='flike-outer'>
                            <a href='https://twitter.com/share' data-url='".$socialMediaUrl."' data-count='vertical' class='twitter-share-button'  data-lang='nl' data-count = 'none'></a>
                            </div>
                            <div class='flike-outer'>
                            <div class='g-plus'   data-href='".$socialMediaUrl."'  data-action='share' data-annotation='vertical-bubble' data-height='60'></div>
            </div>";
        else:

        $socialMedia = "<article class='block'>
        <div class='social-likes'>
            <div class='intro'>
                ".$socialMediaTitle."
            </div>
            <ul class='share-list'>
            <li>
                    <div id='fb-root'></div>
                    <div class='fb-like' data-href='".$socialMediaUrl."' data-send='false' data-layout='box_count' data-width='50' data-show-faces='false'>&nbsp;</div>
             </li>
                <li>
                    <a href='https://twitter.com/share' data-url='".$socialMediaUrl."' data-count='vertical' class='twitter-share-button'  data-lang='nl'></a>
                </li>
                <li>
                    <div class='g-plus' data-href='".$socialMediaUrl."' data-action='share' data-annotation='vertical-bubble' data-height='60'></div>
                </li>
            </ul>
        </div></article>";
        endif;

        return $socialMedia;
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
        if ( ($result = $cache->load($key)) === false ) {

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
     * get store from database according to type
     * @author kraj modified by Raman
     * @param  string  $storeType
     * @param  integer $limit
     * @return array   $data
     */
    public static function getStoreForFrontEnd($storeType, $limit="")
    {
        $data = '';
        switch (strtolower($storeType)) {
            case 'all':
                $data = Shop::getallStoresForFrontEnd();
                break;
            case 'recent':
                $data = Shop::getrecentstores($limit);
            break;
            case 'popular':
                $data = Shop::getPopularStore($limit);
                break;
            default:
            break;
        }

        return $data;
    }
    /**
    * get sidebar widgets for the page using permalink
     * @param  string $page
     * @author kkumar
     * @return widget html
     * @version 1.0
     */

    public static function getSidebarWidget($arr=array(),$page='')
    {
        $pagewidgets = Doctrine_Query::create()
        ->select('p.id,p.slug,w.*,refpage.position')->from('Page p')
        ->leftJoin('p.widget w')
        ->leftJoin('w.refPageWidget refpage')
        ->where("p.permalink="."'$page'")
        ->andWhere('w.status=1')
        ->andWhere('w.deleted=0')
        ->fetchArray();

        $string = '';
        if (count($pagewidgets)>0) {
        for ($i=0;$i<count($pagewidgets[0]['widget']);$i++) {
        if ($pagewidgets[0]['widget'][$i]['slug']=='win_a_voucher') {
         $string .= self::WinVoucherWidget($arr);
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='stuur_een') {
            $string .= self::DiscountCodeWidget();
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_stores') {
            $string .= self::PopularWinkelsWidget();
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_category') {
            $string .= self::PopularCategoryWidget();
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_editor') {
            $string .= self::PopularEditorWidget(@$arr['userId'],$page);
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='most _popular_fashion') {
            $string .= self::MostPopularFashionGuideWidget();
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page!='search_result') {
            $string .= self::sideWidgetCalculator('default',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page=='search_result') {
            $string .= self::sideWidgetCalculator('search',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='other_helpful_tips') {
        $string .= self::otherhelpfullSavingTipsWidget();
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='join_us') {
            $string .= self::joinUsWidget();
        } elseif ($pagewidgets[0]['widget'][$i]['slug']=='social_media') {
            $string .= self::socialmedia('','','','widget');
        } else {
            $string .= str_replace( '<br />', '', html_entity_decode($pagewidgets[0]['widget'][$i]['content']) );
        }
      }
    }

    return $string;
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
                $string .= self::PopularWinkelsWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_category') {
                $string .= self::PopularCategoryWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='most _popular_fashion') {
                $string .= self::MostPopularFashionGuideWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_editor') {
                $string .= self::PopularEditorWidget(@$arr['userId'],$page);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page!='search_result') {
                $string .= self::sideWidgetCalculator('default',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page=='search_result') {
                $string .= self::sideWidgetCalculator('search',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
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
                $string .= self::PopularWinkelsWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_category') {
                $string .= self::PopularCategoryWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='most _popular_fashion') {
                $string .= self::MostPopularFashionGuideWidget();
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='popular_editor') {
                $string .= self::PopularEditorWidget(@$arr['userId'],$page);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page!='search_result') {
                $string .= self::sideWidgetCalculator('default',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
            } elseif ($pagewidgets[0]['widget'][$i]['slug']=='browse' && $page=='search_result') {
                $string .= self::sideWidgetCalculator('search',$pagewidgets[0]['widget'][$i]['refPageWidget'][0]['position']);
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
    public static function PopularWinkelsWidget()
    {
        $zendTranslate = Zend_Registry::get('Zend_Translate');
        $popularStores = self::getStoreForFrontEnd('popular', 25);
        $popularStoresContent = '<div class="intro">
                   <h2>'.$zendTranslate->translate('Populaire Winkels').'</h2>
                   <span>'.$zendTranslate->translate('Grab a promotional code, discount code or voucher for').date(' F Y').'</span>
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
        $popularStoresContent .='</ul>';

        return $popularStoresContent;
    }

    public static function PopularCategoryWidget()
    {
        $popularCategory = Category::getPopulerCategory(8);
        $trans = Zend_Registry::get('Zend_Translate');
        $string = '<div class="right-categorieen sidebar">
                 <h4 class="sidebar-heading">'.$trans->translate('Sidebar categories title').'</h4>';
    for ($i=0;$i<count($popularCategory);$i++) {

            $img = PUBLIC_PATH_CDN.$popularCategory[$i]['category']['categoryicon']['path'].'thum_small_'. $popularCategory[$i]['category']['categoryicon']['name'];

        $bg_none = '';
        $string.='<div class="right-categorieen-col1 '.$bg_none.'">
                        <div class=" right-categorieen-col1-left"><a href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen'). '/' . $popularCategory[$i]['category']['permaLink'].'" class="popular_article"><img src="'.$img.'" alt="'.$popularCategory[$i]['category']['name'].'"/></a></div>
                        <div class=" right-categorieen-col1-right"><a href="'.HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen'). '/' . $popularCategory[$i]['category']['permaLink'].'" class="popular_article">'.$popularCategory[$i]['category']['name'].'</a></div>
                    </div>';

    }
        $getPermLinkCategory = Page::getPageFromPageAttribute(9);
               $string.='<div class="fr mt10">'.$trans->translate('Ga naar').' <a class="text-blue-link" href='. HTTP_PATH_LOCALE .FrontEnd_Helper_viewHelper::__link('categorieen').'>'.$trans->translate('Alle Categorieen').'</a></div>
               </div>';

        return $string;
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

    /**********************	Start Front end home page *************************************/

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
                $result = Category :: getPopulerCategory($flag);
                break;
            case "specialList":
                $result = $data = SpecialList::getfronendsplpage($flag);
                break;
            case "moneySaving":
                $result = Articles :: getmoneySavingArticle($flag);
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
        <h4>' . $trans->translate('Met één click nog meer voordeel!') . '</h4>
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
    /**********************	End Front end home page *************************************/

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
                $img = PUBLIC_PATH_CDN.$articles[$i]['articles']['ArtIcon']['path']."thum_article_medium_".$articles[$i]['articles']['ArtIcon']['name'];

        $string.='<div class="mostpopular-col1">
                    <div class="rediusnone1"><a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer').'/'.$articles[$i]['articles']['permalink'].'" class="popular_article">' . '<img src="' . $img . '"></a></div>
                    <div><a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer').'/'.$articles[$i]['articles']['permalink'].'" class="popular_article">' . $articles[$i]['articles']['title'].'</a></div></div>';
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
            <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer').'/'.$articles[$i]['permalink'].'"><img  src="'.$img.'" alt="'.$articles[$i]['title'].'"></a>
        </span>
        <span class="mostpopular-col1-text">
            <a href="'.HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer').'/'.$articles[$i]['permalink'].'">'.$articles[$i]['title'].'</a>
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
        $mainUl .="<div class='sub-nav-bot-link'><a href='".HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer')."'><img src='".HTTP_PATH."public/images/front_end/sub-nav-money-icon.png' width='' height='' alt='' style='margin: 0 7px 2px 0;'/>".$trans->translate('Alle bespaarwijzers')."</a> &raquo;</div>";
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
   * Common function for social media
   * @author Raman updated by kraj
   * @version 1.0
   * @version 1.1
   * @param $url string
   * @param $title string
   * @param $controller string
   * @param $type string
   * @return string
   */
  public static function socialmedia($url, $title, $controller , $type = null)
  {

    if(strtolower($controller) == 'store' || strtolower($controller) == 'moneysavingguide'):
        $url = HTTP_PATH . ltrim($_SERVER['REQUEST_URI'],'/') ;
        $url = self::generateSocialLink($url);
    else:
        $url = HTTP_PATH;
    endif;

    if($type == 'widget'):
    $string="<div class='flike-outer' style='width: 56px; overflow: hidden; margin: 0px;'>
    <div id='fb-root' style='margin-top:-42px;'></div>
    <div class='fb-like' data-href='".$url."' data-send='false' data-width='44' data-layout='box_count' data-show-faces='false'>&nbsp;
    </div>
    </div>
    <div class='flike-outer' style='margin : 0px; padding-right: 6px;'>
    <a href='https://twitter.com/share' class='twitter-share-button' data-url='".$url."' data-lang='nl' data-count = 'none'></a>
    </div>
    <div class='flike-outer' style='margin : 0px;'><div class='g-plusone' data-size='medium' data-annotation='none'></div>
    </div>";
    elseif ($type == 'popup'):
    $string="<div class='flike-outer' style='width: 52px; overflow: hidden; margin: 0px;'>
    <div id='fb-root' style='margin-top:-41px;'></div>
    <div class='fb-like' data-href='".$url."' data-send='false' data-width='44' data-layout='box_count' data-show-faces='false'>&nbsp;
    </div>
    </div>
    <div class='flike-outer' style='margin : 0px; padding-right: 6px;'>
    <a href='https://twitter.com/share' data-url='".$url."' class='twitter-share-button'  data-lang='nl' data-count = 'none'></a>
    </div>
    <div class='flike-outer' style='margin : 0px;'><div class='g-plusone'  data-href='".$url."' data-size='medium' data-annotation='none'></div>
    </div>";
    else:
    //<!-- Social Links Starts -->
    $string="<div class='social-likes social-likes-new'>
    <div class='social-likes-heading social-likes-heading-new'>
    <p>".$title."</p>
    </div>
    <div class='flike-outer'>
    <div id='fb-root'></div>
    <div class='fb-like' data-href='".$url."' data-send='false' data-layout='button_count' data-width='50' data-show-faces='false'>&nbsp;
    </div>
    </div>
    <div class='flike-outer'>
    <a href='https://twitter.com/share' data-url='".$url."' class='twitter-share-button'  data-lang='" . LOCALE ."'></a>
    </div>
    <div class='flike-outer'><div class='g-plusone'  data-href='".$url."' data-size='medium' data-annotation='none'></div>
    </div>
    </div>";
    endif;
    //<!-- Social Links Ends -->
    return $string;

  }



  /**
   * render pagination links
   * @author cbhopal
   * @param $recordsArray array
   * @param $params array
   * @param $itemsPerPage integer
   * @param $range integer
   * @version 1.0
   * @return object $pagination
   */
  public static function renderPagination($recordsArray,$params,$itemsPerPage,$range = 3)
  {

        $page = intval($params['page'] > 0 ) ? $params['page'] : '1';
        $pagination = Zend_Paginator::factory($recordsArray);
        $pagination->setCurrentPageNumber($page);
        $pagination->setItemCountPerPage($itemsPerPage);
        $pagination->setPageRange(3);

        return $pagination;
  }

  /**
   * Get popular Category list from database for fronthome page
   * @author kkumar
   * @version 1.0
   * @return array $data
   */
  public static function getPopulerCategory($flag,$type='popular')
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
   * Get IP address of client system
   * @author Raman
   * @version 1.0
   * @return array $data
   */

  public static function getRealIpAddress()
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip=$_SERVER['REMOTE_ADDR'];
    }

    return $ip;

  }

  /**
   * View counter common function
   * @author Raman
   * @version 1.0
   * @return array $data
   */
  public static function viewCounter($type, $eventType, $id)
  {
    $clientIP = self::getRealIpAddress();
    $ip = ip2long($clientIP);

    $data = "false";
    switch (strtolower($type)) {

            case 'article':
                $data = self::checkIfThisArtEntryExists($eventType, $id, $ip);
                break;

            case 'shop':
                $data = self::checkIfThisShopEntryExists($eventType, $id, $ip);
                break;

            case 'offer':

                $data = self::checkIfThisOfferEntryExists($eventType, $id, $ip);
                break;
            default:
                break;

    }

    return $data;

  }
  /**
   * Check if the an article id and ip exist in articleViewCount Table
   * @author Raman
   * @version 1.0
   * $id article id
   * $ip ip address of local machine
   * @return array $data
   */
  public static function checkIfThisArtEntryExists($eventType, $id, $ip)
  {
    $res = "false";
    switch (strtolower($eventType)) {

        case 'onclick':

            $data = Doctrine_Query::create()
                ->select('count(*) as exists')
                ->from('ArticleViewCount')
                ->where('deleted=0')
                ->andWhere('onclick!=0')
                ->andWhere('articleid="'.$id.'"')
                ->andWhere('ip="'.$ip.'"')
            ->fetchArray();

            if ($data[0]['exists'] == 0) {

                $cnt  = new ArticleViewCount();
                $view = 1;
                $cnt->articleid = $id;
                $cnt->onclick = $view;
                $cnt->ip = $ip;
                $cnt->save();
                $res = "true";
            }
            break;

        case 'onload':

            $data = Doctrine_Query::create()
            ->select('count(*) as exists')
            ->from('ArticleViewCount')
            ->where('deleted=0' )
            ->andWhere('onload!=0')
            ->andWhere('articleid="'.$id.'"')
            ->andWhere('ip="'.$ip.'"')
            ->fetchArray();

            if ($data[0]['exists'] == 0) {

                $cnt  = new ArticleViewCount();
                $view = 1;
                $cnt->articleid = $id;
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

  /**
   * function to translate URL's according to language selected
   * @author Chetan
   * @return string $variable
   * @version 1.0
   */
  public static function __link($variable)
  {
    $trans = Zend_Registry::get('Zend_Translate');
    $variable = $trans->translate(_($variable));

    return $variable;
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
  {	?>
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
                '@[\\\]@'	// Strip out slashes
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


                    foreach ($additionalTopVouchercodes as $key => $value) {

                        $offers[] =   array('id'=> $value['shop']['id'],
                                                        'permalink' => $value['shop']['permalink'],
                                                        'offer' => $value
                                                      );
                    }
             }

           return $offers;
    }
}
