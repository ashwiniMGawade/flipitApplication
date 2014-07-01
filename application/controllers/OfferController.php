<?php
class OfferController extends Zend_Controller_Action
{
    public function init()
    {
        $module     = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function top20Action()
    {
        $pageName = 'top-20';
        $pageDetails = Page::getPageDetailsFromUrl(FrontEnd_Helper_viewHelper::getPagePermalink());
        $this->view->pageHeaderImage = Logo::getPageLogo($pageDetails->pageHeaderImageId);
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            FrontEnd_Helper_viewHelper::__link($pageName),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $offers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'top_20_offers_list',
            (array)array('function' => 'Offer::getTopOffers', 'parameters' => array(20))
        );
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'all_popularshop_list',
            (array)array('function' => 'Shop::getAllPopularStores', 'parameters' => array(10)),
            true
        );
        $this->view->popularStores = $popularStores;
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->top20PopularOffers = $offers;
        $signUpFormLarge = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignupForm', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormLarge, $signUpFormSidebarWidget);
        $this->view->form = $signUpFormLarge;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public function extendedofferAction()
    {
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $parameters = $this->_getAllParams();
        $extendedUrl = $parameters['permalink'];
        $currentDate = date('Y-m-d');
        $couponDetails = Offer::getCouponDetails($extendedUrl);
        $ShopList = $couponDetails[0]['shop']['id'].'_list';
        $allShopDetailKey = 'all_shopdetail'.$ShopList;
        $shopInformation = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allShopDetailKey,
            (array)array('function' => 'Shop::getStoreDetails', 'parameters' => array($couponDetails[0]['shop']['id']))
        );
        $shopImage =
            PUBLIC_PATH_CDN
            .$couponDetails[0]['shop']['logo']['path'].'thum_medium_store_'
            . $couponDetails[0]['shop']['logo']['name'];
        $allLatestUpdatesInStoreKey = 'all_latestupdatesInStore'.$ShopList;
        $latestShopUpdates = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allLatestUpdatesInStoreKey,
            (array)array(
                'function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode',
                'parameters' => array('latestupdates', 4, $couponDetails[0]['shop']['id'])
            )
        );
        if (count($couponDetails)==0) {
            $this->_redirect(HTTP_PATH_LOCALE.'error');
        }

        $currentDate = date('Y-m-d');
        $topOfferFromStore = Offer::getrelatedOffers($couponDetails[0]['shopId'], $currentDate);
        $frontendSidebarHelper = new FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->popularStoresList = $frontendSidebarHelper->PopularShopWidget();
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->topOfferFromStore = $topOfferFromStore;
        $this->view->couponDetails = $couponDetails;
        $this->view->currentStoreInformation = $shopInformation;
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);

        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $couponDetails[0]['title'],
            trim($couponDetails[0]['extendedTitle']),
            trim($couponDetails[0]['extendedMetaDescription']),
            FrontEnd_Helper_viewHelper::__link('link_deals') .'/'. $couponDetails[0]['extendedUrl'],
            FACEBOOK_IMAGE,
            $customHeader
        );
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
        $this->view->pageCssClass = 'flipit-expired-page home-page';
    }

    public function offerDetailAction()
    {
        $this->_helper->layout->disableLayout();
        $offerParameters = $this->_getAllParams();
        $this->view->params = $offerParameters;
        $offerObject = new Offer();

        if (isset($offerParameters['imagePath']) && !empty($offerParameters['imagePath'])) {
            $offerImagePath = $offerParameters['imagePath'];
            $this->view->offerImagePath = $offerImagePath;
        } else {
            $this->view->offerImagePath = '';
        }
        $offerId = $offerParameters['id'];
        $offerDetails = $offerObject->getOfferInfo($offerParameters['id']);
        $this->view->offerdetail = $offerDetails;
        $this->view->vote = $offerParameters['vote'];
        $this->view->votepercentage = 0;
        $shopImage = PUBLIC_PATH_CDN.$offerDetails[0]['shop']['logo']['path'].'thum_medium_store_'.
        $offerDetails[0]['shop']['logo']['name'];
        $this->viewHelperObject->getMetaTags(
            $this,
            $offerDetails[0]['title'],
            '',
            '',
            $offerDetails[0]['shop']['permaLink'],
            $shopImage,
            ''
        );
        if ($offerDetails[0]['couponCodeType']  == 'UN') {
            $getOfferUniqueCode = CouponCode::returnAvailableCoupon($offerId);
            if ($getOfferUniqueCode) {
                $this->view->couponCode = $getOfferUniqueCode['code'] ;
            }
        } else {
            $this->view->couponCode = $offerDetails[0]['couponCode']  ;
        }

    }

    public function indexAction()
    {
        $pageDetails = Page::getPageDetailsFromUrl(FrontEnd_Helper_viewHelper::getPagePermalink());
        $params = $this->_getAllParams();
        $cacheKeyForNewsOffer =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_newoffer_list');
        if ($cacheKeyForNewsOffer) {
            $offers = Offer::getCommonNewestOffers('newest', 40, $this->view->shopId);
            FrontEnd_Helper_viewHelper::setInCache('all_newoffer_list', $offers);
        } else {
            $offers = FrontEnd_Helper_viewHelper::getFromCacheByKey('all_newoffer_list');
        }
        $this->view->pageHeaderImage = Logo::getPageLogo($pageDetails->pageHeaderImageId);
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->getRequest()->getActionName();
        $this->view->top20PopularOffers = $offers;
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            FrontEnd_Helper_viewHelper::__link('link_nieuw'),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $this->view->shopId = '';
        $this->view->controllerName = $params['controller'];
        $this->view->offersType = 'newestOffer';
        $this->view->shopName = 'top20';
        $offersWithPagination = FrontEnd_Helper_viewHelper::renderPagination($offers, $this->_getAllParams(), 20, 3);
        $this->view->offersWithPagination = $offersWithPagination;
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
        $this->view->pageCssClass = 'home-page';
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }
}
