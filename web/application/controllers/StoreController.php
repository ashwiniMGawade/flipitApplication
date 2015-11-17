<?php
class StoreController extends Zend_Controller_Action
{
    public $viewHelperObject = '';
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (file_exists(APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }

        $shopId = $this->getRequest()->getParam('id');
        $shopdetail = \KC\Repository\Shop::getshopStatus($shopId);
        if ($shopdetail) {
            $request = $this->getRequest();
            $request->setControllerName('error');
            $request->setActionName('error');
        }
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();

    }

    public function addshopinfevoriteAction()
    {
        $shopId = $this->getRequest()->getParam("shopid");
        $userId = $this->getRequest()->getParam("uId");
        $shopInformation = KC\Repository\Shop::shopAddInFavourite($userId, $shopId);
        echo \Zend_Json::encode($shopInformation);
        exit();
    }

    public function storedetailAction()
    {
        $url = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $explodeUrl = explode('?', $url);
        $shopPermalink = $explodeUrl[0];

        if (LOCALE != '') {
            $explodedPermalink = explode("/", $shopPermalink);
            $shopPermalink = $explodedPermalink[1];
        }

        $this->view->shareCodeStatus = '';
        if (isset($explodeUrl[1])) {
            $this->view->shareCodeStatus = $explodeUrl[1];
        }

        $this->view->storePageUrl = $shopPermalink;
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($shopPermalink);
        $shopRecordsLimit = 10;
        $shopParams = $this->_getAllParams();
        if (isset($shopParams['popup']) && $shopParams['popup'] != '') {
            $offerVisiblity = KC\Repository\Offer::getOfferVisiblity($shopParams['popup']);
            $shopInfo = KC\Repository\Shop::getShopInformation($this->getRequest()->getParam('id'));
            if (!Auth_VisitorAdapter::hasIdentity() && $offerVisiblity == 1) {
                if (!empty($shopInfo) && isset($shopInfo[0]['permaLink'])) {
                    $this->_redirect(HTTP_PATH_LOCALE. $shopInfo[0]['permaLink']);
                }
            }

            $referer = $this->getRequest();
            $referer = $referer->getHeader('referer');

            if ($referer == '') {
                //$this->_redirect(HTTP_PATH_LOCALE. $shopInfo[0]['permaLink']);
                //exit();
            }
        }

        $currentShopId = $shopParams['id'];
        $shopId = $this->getRequest()->getParam('id');


        if ($shopId) {
            $shopList = $shopId.'_list';
            $allShopDetailKey = 'shopDetails_'.$shopList;
            $shopInformation = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allShopDetailKey,
                array('function' => 'KC\Repository\Shop::getStoreDetailsForStorePage', 'parameters' => array($shopId)
                ),
                ''
            );
            $allOffersInStoreKey = '6_topOffers'.$shopList;
            $offers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allOffersInStoreKey,
                array(
                    'function' => 'KC\Repository\Offer::getAllOfferOnShop',
                    'parameters' => array($shopId)
                ),
                ''
            );

            $offers = $this->_reorder_offers_as_per_created_date($offers);

            $allLatestUpdatesInStoreKey = '4_shopLatestUpdates_'.$shopList;
            $latestShopUpdates = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allLatestUpdatesInStoreKey,
                array(
                    'function' => '\FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array('latestupdates', 4, $shopId)
                ),
                ''
            );
            $moneySavingGuideArticle = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_moneySavingArticles_'.$shopList,
                array(
                    'function' => 'KC\Repository\MoneySaving::generateShopMoneySavingGuideArticle',
                    'parameters' => array('moneysaving', 3, $shopId)
                ),
                ''
            );

            $sixShopReasons = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_sixReasons_'.$shopList,
                array(
                    'function' => 'KC\Repository\ShopReasons::getShopReasons',
                    'parameters' => array($shopId)
                ),
                ''
            );

            if (!count($shopInformation) > 0) {
                $localeUrl = HTTP_PATH_LOCALE;
                $this->_helper->redirector->setCode(301);
                $this->_redirect($localeUrl);
            }

           /* if ($shopInformation[0]['showChains']) {
                if ($shopInformation[0]['chainItemId'] != '') {
                    $frontEndViewHelper = new \FrontEnd_Helper_SidebarWidgetFunctions();
                    $shopChains = $frontEndViewHelper->sidebarChainWidget(
                        $shopInformation[0]['id'],
                        $shopInformation[0]['name'],
                        $shopInformation[0]['chainItemId']
                    );
                    if ($shopChains['hasShops'] && isset($shopChains['string'])) {
                        $this->view->shopChain = $shopChains['string'];
                    }
                }

            }*/

            $shopImage = PUBLIC_PATH_CDN.ltrim($shopInformation[0]['logo']['path'], "/")
                .'thum_medium_store_'.$shopInformation[0]['logo']['name'];
            $this->view->shopBranding = KC\Repository\Shop::getShopBranding($shopId);
        } else {
            $urlToRedirect = HTTP_PATH_LOCALE. 'store/index';
            $this->_redirect($urlToRedirect);
        }

        $topThreeExpiredOfferKey = 'shop_topthreeexpiredoffers'.$shopList;
        $topThreeExpiredOffers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$topThreeExpiredOfferKey,
            array(
                'function' => 'KC\Repository\Offer::getAllOfferOnShop',
                'parameters' => array($shopId, 10, false, true, false, true)
            ),
            ''
        );

        $expiredOffersForBottom = array();
        $offersInformation = $offers;
        if (!empty($topThreeExpiredOffers)) {
            $expiredOffersForBottom = $topThreeExpiredOffers;
            $topThreeExpiredOffers = array_slice($topThreeExpiredOffers, 0, 3);
            $offersInformation = $shopInformation[0]['affliateProgram'] != 0
                ? $this->_helper->Store->mergeExpiredOffersWithLiveOffers($offers, $topThreeExpiredOffers)
                : $offers;
        }

        $this->view->currentStoreInformation = $shopInformation;
        $this->view->moneySavingGuideArticle = $moneySavingGuideArticle;
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->offers = $offersInformation;
        $this->view->sixShopReasons = $this->_helper->Store->changeIndexOfSixReasons($sixShopReasons);

        if ($this->view->currentStoreInformation[0]['affliateProgram']==0 && count($offers) <=0) {
            $offers = $this->_helper->Store->topStorePopularOffers($shopId, $offers);
            $this->view->topPopularOffers = $offers;
        }

        $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($shopInformation[0]['permaLink']);
        if ($this->view->currentStoreInformation[0]['discussions'] == 1) {
            $this->view->discussionComments =
                \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                    'get_'.$cacheKey.'_disqusComments',
                    array(
                        'function' => 'KC\Repository\DisqusComments::getPageUrlBasedDisqusComments',
                        'parameters' => array($shopPermalink)
                    ),
                    ''
                );
        }

        $this->view->expiredOffers = $expiredOffersForBottom;
        $similarShopsAndSimilarCategoriesOffersKey = 'shop_similarShopsAndSimilarCategoriesOffers'.$shopList;
        $similarShopsAndSimilarCategoriesOffers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$similarShopsAndSimilarCategoriesOffersKey,
            array(
               'function' => 'Application_Service_Factory::similarOffers',
               'parameters' => array($shopId, $shopInformation[0]['affliateProgram'])
            ),
            ''
        );
        $this->view->similarShopsAndSimilarCategoriesOffers = $similarShopsAndSimilarCategoriesOffers;
        $offersAddedInShopKey = "offersAdded_".$shopId."_shop";
        $this->view->offersAddedInShop = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$offersAddedInShopKey,
            array(
                'function' => 'KC\Repository\Offer::getNumberOfOffersCreatedByShopId',
                'parameters' => array($shopId)
            ),
            ''
        );

        $futureCodeKey = "futurecode_".$shopId."_shop";
        $offerInfo = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$futureCodeKey,
            array(
                'function' => 'KC\Repository\Offer::getFutureOffersDatesByShopId',
                'parameters' => array($shopId)
            ),
            ''
        );

        $this->view->futureCodeCount = $this->_helper->Store->getNumberOfDaysTillOfferGetsLive($offerInfo);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->storeImage = $shopImage;
        $this->view->shareUrl = HTTP_PATH_LOCALE . $shopInformation[0]['permaLink'];
        $this->view->shopEditor = \FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'user_'.$shopInformation[0]['contentManagerId'].'_details',
                array(
                'function' =>
                'KC\Repository\User::getUserDetails', 'parameters' => array($shopInformation[0]['contentManagerId'])
                ),
                ''
            );
        $customHeader = isset($shopInformation[0]['customHeader']) ? $shopInformation[0]['customHeader'] : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $shopInformation[0]['overriteTitle'],
            '',
            trim($shopInformation[0]['metaDescription']),
            $shopPermalink,
            $shopImage,
            $customHeader
        );

        if ($shopInformation[0]['showSimliarShops']) {
            $this->view->similarShops = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_'.$shopId.'_similar_shops',
                array(
                    'function' => 'KC\Repository\Shop::getSimilarShops',
                    'parameters' => array($shopId, 10)
                )
            );
        }
        $signUpFormForStorePage = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            $signUpFormForStorePage,
            $signUpFormSidebarWidget
        );
        $this->view->form = $signUpFormForStorePage;
        $socialCodeForm = new Application_Form_SocialCode();
        $socialCodeForm->getElement('shops')->setValue($shopInformation[0]['name']);
        $this->view->zendForm = $socialCodeForm;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'author-page page-store';
        $moneyShop = $shopInformation[0]['affliateProgram'] != 0 ? 'money-shop' : 'no-money';
        $shopId = !empty($shopInformation[0]['id']) ? $shopInformation[0]['id'] : '';
        $this->view->widgetPosition = \KC\Repository\WidgetLocation::getWidgetPositionForFrontEnd(
            'shop',
            'global',
            $shopId,
            $moneyShop,
            $this->view->offers
        );
    }

    private function _reorder_offers_as_per_created_date($offers)
    {
        $reorderOffers = array();
        $saleOffers = array();
        for($i = 0; $i < count($offers) ; $i++) {
            if('CD' == $offers[$i]['discountType']) {
                $reorderOffers[] = $offers[$i];
            } else {
                $saleOffers[] = $offers[$i];
            }
        }
        //REARRANGE SALE OFFERS
        for($i = 0; $i < count($saleOffers) ; $i++) {
            for($j = 0; $j <= $i ; $j++) {
                if ($saleOffers[$j]['startDate']->date < $saleOffers[$i]['startDate']->date) {
                    $temp = $saleOffers[$i];
                    $saleOffers[$i] = $saleOffers[$j];
                    $saleOffers[$j] = $temp;
                }
            }
        }
        $reorderOffers = array_merge($reorderOffers, $saleOffers);
        return $reorderOffers;
    }

    public function indexAction()
    {
        $permalink = FrontEnd_Helper_viewHelper::getPagePermalink();
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $startingCharacter = $this->_helper->Store->getActualPermalink($permalink, 'firstCharacter');
        $endingCharacter = $this->_helper->Store->getActualPermalink($permalink, 'lastCharacter');
        $startingAndEndingCharacter = $startingCharacter. "-". $endingCharacter;
        $permalink = $this->_helper->Store->getActualPermalink($permalink, 'permalink');
        $pageDetails = KC\Repository\Page::getPageDetailsFromUrl($permalink);
        $pageDetails = (object) $pageDetails;
        $this->view->pageHeaderImage = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'page_header'.$pageDetails->id.'_image',
            array(
                'function' => 'KC\Repository\Logo::getPageLogo',
                'parameters' => array($pageDetails->pageHeaderImageId['id'])
            )
        );
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->view->controllerName = $this->getRequest()->getParam('controller');

        $allShopsCacheKey = 'all_shops'. FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter(
            $startingAndEndingCharacter
        ).'_list';

        $allStoresList = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allShopsCacheKey,
            array('function' => '\KC\Repository\Shop::getAllStoresForFrontEnd', 'parameters' => array($startingCharacter, $endingCharacter)),
            true
        );

        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            '5_popularShops_list',
            array('function' => '\KC\Repository\Shop::getAllPopularStores', 'parameters' => array(6)),
            true
        );

        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            isset($pageDetails->permaLink) ? $pageDetails->permaLink : '',
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );

        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            '',
            $signUpFormSidebarWidget
        );
        $this->view->selectedAlphabet = $startingAndEndingCharacter;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->storesInformation = $allStoresList;
        $socialCodeForm = new Application_Form_SocialCode();
        $this->view->zendForm = $socialCodeForm;
        $this->view->popularStores = $popularStores;
        $this->view->pageCssClass = 'all-stores-page';
    }

    public function howtoguideAction()
    {
        $howToGuidePermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($howToGuidePermalink);
        $shopId = $this->getRequest()->getParam('shopid');
        if (!isset($shopId)) {
            $shopId = KC\Repository\Shop::getShopIdByPermalink($this->getRequest()->getParam('permalink'));
        }
        $howToGuides = $this->_helper->Store->getHowToGuide($shopId);
        if (empty($howToGuides)||!($howToGuides[0][0]['howToUse'])) {
            throw new \Zend_Controller_Action_Exception('', 404);
        }
        $howToGuides = $howToGuides[0];
        $shopList = $howToGuides[0]['id'].'_list';
        $shopInformation = $this->_helper->Store->getShopInformation($howToGuides[0]['id'], $shopList);
        //$this->view->shopChain = $this->_helper->Store->getShopChain($shopInformation);
        $latestShopUpdates = $this->_helper->Store->getShopLatestUpdates($howToGuides[0]['id'], $shopList);
        $offers = $this->_helper->Store->getSixTopOffers($howToGuides[0]['id'], $shopList);

        $this->view->offers = $offers;
        $this->view->currentStoreInformation = $shopInformation;
        $frontEndViewHelper = new \FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->howToGuides = $howToGuides;
        $shopName = isset($shopInformation[0]['name']) ? $shopInformation[0]['name'] : '';
        $howToGuidesTitle = isset($howToGuides[0]['howtoTitle']) ? $howToGuides[0]['howtoTitle'] : '';
        $customHeader = '';
        $howToGuideMetaDescription = isset($howToGuides[0]['howtoMetaDescription'])
            ? $howToGuides[0]['howtoMetaDescription']
            : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            str_replace('[shop]', $shopName, $howToGuidesTitle),
            '',
            trim($howToGuideMetaDescription),
            $howToGuidePermalink,
            FACEBOOK_IMAGE,
            $customHeader
        );
        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp'
        );
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            $signUpFormForStorePage,
            $signUpFormSidebarWidget
        );
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $socialCodeForm = new Application_Form_SocialCode();
        $this->view->zendForm = $socialCodeForm;
        $this->view->pageCssClass = 'page-store';
    }

    public function addtofavouriteAction()
    {
        $this->view->layout()->robotKeywords = 'noindex, nofollow';
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $visitorShopIdSessionNameSpace = new \Zend_Session_Namespace('favouriteShopId');
        $visitorShopIdSessionNameSpace->favouriteShopId = $this->getRequest()->getParam('shopId');
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $visitorId = \Auth_VisitorAdapter::getIdentity()->id;
            $favouriteShopIdFromSession = new \Zend_Session_Namespace('favouriteShopId');
            if (isset($favouriteShopIdFromSession->favouriteShopId)) {
                $shopId = $favouriteShopIdFromSession->favouriteShopId;
                KC\Repository\Shop::shopAddInFavourite($visitorId, base64_decode($shopId));
                \Zend_Session::namespaceUnset('favouriteShopId');
                $this->_redirect(HTTP_PATH_LOCALE. $this->getRequest()->getParam('permalink'));
                exit();
            }
        } else {
            $this->_redirect(HTTP_PATH_LOCALE. \FrontEnd_Helper_viewHelper::__link('link_login'));
            exit();
        }
        throw new \Zend_Controller_Action_Exception('', 404);
    }
    // Returns the right favorite heart status by fetching the partial.
    public function addfavoriteviewAction()
    {
        $this->view->shopid = $this->getRequest()->getParam('shopId');
        $this->view->shopname = $this->getRequest()->getParam('shopName');
        $this->_helper->layout()->disableLayout();
    }

    public function followbuttonAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->shopId = $this->getRequest()->getParam('shopid');
        $this->view->permalink = $this->getRequest()->getParam('permalink');
    }

    public function createdoffersAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->shopId = $this->getRequest()->getParam('shopid');
    }
}
