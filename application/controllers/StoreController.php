<?php
class StoreController extends Zend_Controller_Action
{
    public $viewHelperObject = '';
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }

        $shopId = $this->getRequest()->getParam('id');
        $shopdetail = Shop::getshopStatus($shopId);
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
        $shopInformation = Shop::shopAddInFavourite($userId, $shopId);
        echo Zend_Json::encode($shopInformation);
        exit();
    }

    public function storedetailAction()
    {
        $shopPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($shopPermalink);
        $shopRecordsLimit = 10;
        $shopParams = $this->_getAllParams();
        $currentShopId = $shopParams['id'];
        $shopId = $this->getRequest()->getParam('id');

        if ($shopId) {
            $ShopList = $shopId.'_list';
            $allShopDetailKey = 'shopDetails_'.$ShopList;
            $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allShopDetailKey,
                array('function' => 'Shop::getStoreDetails', 'parameters' => array($shopId))
            );
            $allOffersInStoreKey = '6_topOffers_'.$ShopList;
            $offers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allOffersInStoreKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                    'parameters' => array("all", 10, $shopId, 0)
                )
            );
            $allExpiredOfferKey = 'shop_expiredOffers_'.$ShopList;
            $expiredOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allExpiredOfferKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array("expired", 8, $shopId)
                )
            );
            $allLatestUpdatesInStoreKey = '4_shopLatestUpdates_'.$ShopList;
            $latestShopUpdates = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allLatestUpdatesInStoreKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array('latestupdates', 4, $shopId)
                )
            );
            $moneySavingGuideArticle = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_moneySavingArticles_'.$ShopList,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::generateShopMoneySavingGuideArticle',
                    'parameters' => array('moneysaving', 3, $shopId)
                )
            );

            if (!count($shopInformation) >0) {
                $localeUrl = HTTP_PATH_LOCALE;
                $this->_helper->redirector->setCode(301);
                $this->_redirect($localeUrl);
            }

            if ($shopInformation[0]['showChains']) {
                $frontEndViewHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
                $shopChains = $frontEndViewHelper->sidebarChainWidget(
                    $shopInformation[0]['id'],
                    $shopInformation[0]['name'],
                    $shopInformation[0]['chainItemId']
                );

                if (isset($shopChains['headLink'])) {
                    $this->view->layout()->customHeader = "\n" . $shopChains['headLink'];
                }
                if ($shopChains['hasShops'] && isset($shopChains['string'])) {
                    $this->view->shopChain = $shopChains['string'];
                }
            }

            $shopImage = PUBLIC_PATH_CDN.ltrim($shopInformation[0]['logo']['path'], "/")
                .'thum_medium_store_'.$shopInformation[0]['logo']['name'];
            $this->view->shopBranding = Shop::getShopBranding($shopId);
        } else {
            $urlToRedirect = HTTP_PATH_LOCALE. 'store/index';
            $this->_redirect($urlToRedirect);
        }

        $this->view->currentStoreInformation = $shopInformation;
        $this->view->moneySavingGuideArticle = $moneySavingGuideArticle;
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->offers = $offers;

        if ($this->view->currentStoreInformation[0]['affliateProgram']==0 && count($this->view->offers) <=0) {
            $offers = $this->_helper->Store->topStorePopularOffers($shopId, $offers);
            $this->view->topPopularOffers = $offers;
        }
        $this->view->expiredOffers = $expiredOffers;
        if ($shopInformation[0]['affliateProgram'] == 0) {
            $numberOfSimilarOffers = 10;
        } else {
            $numberOfSimilarOffers = 4;
        }

        $this->view->similarShopsAndSimilarCategoriesOffers = FrontEnd_Helper_viewHelper::getShopCouponCode(
            'similarStoresAndSimilarCategoriesOffers',
            $numberOfSimilarOffers,
            $shopId
        );

        $this->view->countPopularOffers = count(
            FrontEnd_Helper_viewHelper::commonfrontendGetCode('popular', $shopRecordsLimit, $currentShopId)
        );
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->storeImage = $shopImage;
        $this->view->shareUrl = HTTP_PATH_LOCALE . $shopInformation[0]['permaLink'];
        $this->view->shopEditor = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'user_'.$shopInformation[0]['contentManagerId'].'_details',
                array(
                'function' =>
                'User::getUserDetails', 'parameters' => array($shopInformation[0]['contentManagerId'])
                )
            );

        $customHeader = isset($shopInformation[0]['customHeader']) ? $shopInformation[0]['customHeader'] : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $shopInformation[0]['overriteTitle'],
            '',
            trim($shopInformation[0]['metaDescription']),
            $shopInformation[0]['permaLink'],
            $shopImage,
            $customHeader
        );

        if ($shopInformation[0]['showSimliarShops']) {
            $this->view->similarShops = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_'.$shopId.'_similarShops',
                array(
                    'function' => 'Shop::getSimilarShops',
                    'parameters' => array($shopId, 11)
                )
            );
        }

        $frontendSidebarHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->popularStoresList = $frontendSidebarHelper->PopularShopWidget();

        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            $signUpFormForStorePage,
            $signUpFormSidebarWidget
        );
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'author-page';
    }

    public function indexAction()
    {
        $permalink = FrontEnd_Helper_viewHelper::getPagePermalink();
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $pageDetails = Page::getPageDetailsFromUrl(FrontEnd_Helper_viewHelper::getPagePermalink());
        $this->view->pageHeaderImage = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'page_header'.$pageDetails->id.'_image',
            array(
                'function' => 'Logo::getPageLogo',
                'parameters' => array($pageDetails->pageHeaderImageId)
            )
        );
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $allStoresList = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_shops_list',
            array('function' => 'Shop::getallStoresForFrontEnd', 'parameters' => array('all', null)),
            true
        );
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            '10_popularShops_list',
            array('function' => 'Shop::getAllPopularStores', 'parameters' => array(10)),
            true
        );
        $storeSearchByAlphabet = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_searchPanel_list',
            array('function' => 'FrontEnd_Helper_viewHelper::alphabetList', 'parameters' => array()),
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

        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            '',
            $signUpFormSidebarWidget
        );
        
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->storesInformation = $allStoresList;
        $this->view->storeSearchByAlphabet = $storeSearchByAlphabet;
        $this->view->popularStores = $popularStores;
        $this->view->pageCssClass = 'all-stores-page';
    }

    public function howtoguideAction()
    {
        $howToGuidePermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($howToGuidePermalink);
        $parameters = $this->_getAllParams();
        $howToGuides = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'store_'.$parameters['permalink'].'howToGuide',
            array('function' => 'Shop::getshopDetails', 'parameters' => array($parameters['permalink']))
        );

        if (empty($howToGuides)) {
            throw new Zend_Controller_Action_Exception('', 404);
        }

        $ShopList = $howToGuides[0]['id'].'_list';
        $allShopDetailKey = 'shopDetails_'.$ShopList;
        $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allShopDetailKey,
            array('function' => 'Shop::getStoreDetails', 'parameters' => array($howToGuides[0]['id']))
        );

        if ($shopInformation[0]['showChains']) {
            $frontEndViewHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
            $shopChains = $frontEndViewHelper->sidebarChainWidget(
                $shopInformation[0]['id'],
                $shopInformation[0]['name'],
                $shopInformation[0]['chainItemId']
            );
            if ($shopChains['hasShops'] && isset($shopChains['string'])) {
                $this->view->shopChain = $shopChains['string'];
            }
        }

        $allLatestUpdatesInStoreKey = 'ShoplatestUpdates_'.$ShopList;
        $latestShopUpdates = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allLatestUpdatesInStoreKey,
            array('function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode', 'parameters' => array(
                'latestupdates',
                4,
                $howToGuides[0]['id'])
            )
        );
        $allOffersInStoreKey = '6_topOffers_'.$ShopList;
        $offers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allOffersInStoreKey,
            array('function' => 'FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                'parameters' => array('topSixOffers', 6, $howToGuides[0]['id'], 0)
            )
        );
        $offers = array_chunk($offers, 3);
        $this->view->offers = $offers;
        $this->view->currentStoreInformation = $shopInformation;
        $frontEndViewHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->popularStoresList = $frontEndViewHelper->PopularShopWidget();
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->howToGuides=$howToGuides;

        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $howToGuides[0]['howtoTitle'],
            '',
            trim($howToGuides[0]['howtoMetaDescription']),
            $howToGuides[0]['permaLink'],
            FACEBOOK_IMAGE,
            $customHeader
        );

        $signUpFormForStorePage = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'largeSignupForm',
            'SignUp'
        );
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp'
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm(
            $this,
            $signUpFormForStorePage,
            $signUpFormSidebarWidget
        );
        $this->view->form = $signUpFormForStorePage;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public function addtofavouriteAction()
    {
        $this->view->layout()->robotKeywords = 'noindex, nofollow';
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $visitorShopIdSessionNameSpace = new Zend_Session_Namespace('favouriteShopId');
        $visitorShopIdSessionNameSpace->favouriteShopId = $this->getRequest()->getParam('shopId');
        if (Auth_VisitorAdapter::hasIdentity()) {
            $visitorId = Auth_VisitorAdapter::getIdentity()->id;
            $favouriteShopIdFromSession = new Zend_Session_Namespace('favouriteShopId');
            if (isset($favouriteShopIdFromSession->favouriteShopId)) {
                $shopId = $favouriteShopIdFromSession->favouriteShopId;
                Shop::shopAddInFavourite($visitorId, base64_decode($shopId));
                Zend_Session::namespaceUnset('favouriteShopId');
                $this->_redirect(HTTP_PATH_LOCALE. $this->getRequest()->getParam('permalink'));
                exit();
            }
        } else {
            $this->_redirect(HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_login'));
            exit();
        }
        throw new Zend_Controller_Action_Exception('', 404);
    }
    // Returns the right favorite heart status by fetching the partial.
    public function addfavoriteviewAction()
    {
        $this->view->shopid = $this->getRequest()->getParam('shopId');
        $this->view->shopname = $this->getRequest()->getParam('shopName');
        $this->_helper->layout()->disableLayout();
    }
}