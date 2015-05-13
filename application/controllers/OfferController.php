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
        $this->viewHelperObject = new \FrontEnd_Helper_viewHelper();
    }

    public function top20Action()
    {
        $pageName = 'top-20';
        $pagePermalink = \FrontEnd_Helper_viewHelper::getPagePermalink();

        $pagePermalink = explode('?', $pagePermalink);
        $pagePermalink = isset($pagePermalink[0]) ? $pagePermalink[0] : '';
        $pageDetails = (object)\KC\Repository\Page::getPageDetailsFromUrl($pagePermalink);

        $this->view->canonical = \FrontEnd_Helper_viewHelper::generateCononical($pagePermalink);

        $this->view->pageHeaderImage = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'page_header'.$pageDetails->id.'_image',
            array(
                'function' => '\KC\Repository\Logo::getPageLogo',
                'parameters' => array($pageDetails->pageHeaderImageId['id'])
            )
        );
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            \FrontEnd_Helper_viewHelper::__link($pageName),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $offers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'20_topOffers_list',
            (array)array('function' => '\KC\Repository\Offer::getTopOffers', 'parameters' => array(20)
            ),
            ''
        );
        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'10_popularShops_list',
            (array)array('function' => '\KC\Repository\Shop::getAllPopularStores', 'parameters' => array(12)
            ),
            ''
        );
        $this->view->popularStores = $popularStores;
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->top20PopularOffers = $offers;
        $signUpFormLarge = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignupForm', 'SignUp');
        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        $editorWidgetInformation = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'top20_editor_data',
                array(
                'function' =>
                'KC\Repository\EditorWidget::getEditorWigetData', 'parameters' => array('top20')
                ),
                ''
            );
        $editorId = !empty($editorWidgetInformation[0]['editorId'])
            ? $editorWidgetInformation[0]['editorId'] : '';
        if (!empty($editorId)) {
            $this->view->editorInformation = \FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache(
                    'user_'.$editorId.'_details',
                    array(
                    'function' =>
                    'KC\Repository\User::getUserDetails', 'parameters' => array($editorId)
                    ),
                    ''
                );
        } else {
            $this->view->editorInformation = '';
        }
        $this->view->editorWidgetInformation = $editorWidgetInformation;
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormLarge, $signUpFormSidebarWidget);
        $this->view->form = $signUpFormLarge;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $socialCodeForm = new Application_Form_SocialCode();
        $this->view->zendForm = $socialCodeForm;
        $this->view->pageCssClass = 'page-store';
        $this->view->widgetPosition = \KC\Repository\WidgetLocation::getWidgetPositionForFrontEnd(
            'page',
            'global',
            $pageDetails->id,
            '',
            $offers
        );
    }

    public function extendedofferAction()
    {
        $permalink = ltrim(\Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $parameters = $this->_getAllParams();
        $extendedUrl = $parameters['permalink'];
        $couponDetails = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'extended_'.\FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($extendedUrl).'_couponDetails',
            array('function' => '\KC\Repository\Offer::getCouponDetails', 'parameters' => array($extendedUrl))
        );
        $shopList = $couponDetails[0]['shopOffers']['id'].'_list';
        $allShopDetailKey = 'offerDetails_'.$shopList;
        $shopInformation = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allShopDetailKey,
            (array)array(
                'function' => '\KC\Repository\Shop::getStoreDetails',
                'parameters' => array($couponDetails[0]['shopOffers']['id'])
            )
        );

        $shopImage =
            PUBLIC_PATH_CDN
            .$couponDetails[0]['shopOffers']['logo']['path'].'thum_medium_store_'
            . $couponDetails[0]['shopOffers']['logo']['name'];
        $allLatestUpdatesInStoreKey = 'shop_latestUpdates'.$shopList;
        $latestShopUpdates = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$allLatestUpdatesInStoreKey,
            (array)array(
                'function' => '\FrontEnd_Helper_viewHelper::getShopCouponCode',
                'parameters' => array('latestupdates', 4, $couponDetails[0]['shopOffers']['id'])
            )
        );

        if (count($couponDetails)== 0) {
            throw new \Zend_Controller_Action_Exception('', 404);
        }

        $topOfferFromStore = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'extendedTopOffer_of_'.$couponDetails[0]['shopOffers']['id'],
            array('function' => '\KC\Repository\Offer::getrelatedOffers',
                'parameters' => array($couponDetails[0]['shopOffers']['id']))
        );

        $frontendSidebarHelper = new \FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->popularStoresList = $frontendSidebarHelper->PopularShopWidget();
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->topOfferFromStore = $topOfferFromStore;
        $this->view->couponDetails = $couponDetails;
        $this->view->currentStoreInformation = $shopInformation;
        $this->view->canonical = \FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $couponDetails[0]['title'],
            trim($couponDetails[0]['extendedTitle']),
            trim($couponDetails[0]['extendedMetaDescription']),
            \FrontEnd_Helper_viewHelper::__link('link_deals') .'/'. $couponDetails[0]['extendedUrl'],
            FACEBOOK_IMAGE,
            $customHeader
        );
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
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'flipit-expired-page page-store';
    }

    public function offerDetailAction()
    {
        $this->_helper->layout->disableLayout();
        $offerParameters = $this->_getAllParams();
        $this->view->params = $offerParameters;

        if (isset($offerParameters['imagePath']) && !empty($offerParameters['imagePath'])) {
            $offerImagePath = $offerParameters['imagePath'];
            $this->view->offerImagePath = $offerImagePath;
        } else {
            $this->view->offerImagePath = '';
        }
        $offerDetails = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'offer_'.$offerParameters['id'].'_details',
            array('function' => '\KC\Repository\Offer::getOfferInfo', 'parameters' => array($offerParameters['id']))
        );
        $this->view->offerdetail = $offerDetails;
        $this->view->vote = $offerParameters['vote'];
        $this->view->votepercentage = 0;
        $shopImage = PUBLIC_PATH_CDN.$offerDetails[0]['shopImagePath'].'thum_medium_store_'.
        $offerDetails[0]['shopImageName'];
        $this->viewHelperObject->getMetaTags(
            $this,
            $offerDetails[0][0]['title'],
            '',
            '',
            $offerDetails[0][0]['shopOffers']['permaLink'],
            $shopImage,
            ''
        );
        if ($offerDetails[0][0]['couponCodeType']  == 'UN') {
            $getOfferUniqueCode = \KC\Repository\CouponCode::returnAvailableCoupon($offerDetails[0][0]['id']);
            if ($getOfferUniqueCode) {
                $this->view->couponCode = $getOfferUniqueCode['code'];
            }
        } else {
            $this->view->couponCode = $offerDetails[0][0]['couponCode'];
        }

    }

    public function indexAction()
    {
        $pageDetails = KC\Repository\Page::getPageDetailsFromUrl(\FrontEnd_Helper_viewHelper::getPagePermalink());
        $params = $this->_getAllParams();
        $cacheKeyForNewOffers =  \FrontEnd_Helper_viewHelper::checkCacheStatusByKey('all_newOffer_list');
        if ($cacheKeyForNewOffers) {
            $offers = KC\Repository\Offer::getCommonNewestOffers('newest', 40, $this->view->shopId);
            \FrontEnd_Helper_viewHelper::setInCache('all_newOffer_list', $offers);
        } else {
            $offers = \FrontEnd_Helper_viewHelper::getFromCacheByKey('all_newOffer_list');
        }
        $pageDetails = (object) $pageDetails;
        if (!empty($pageDetails->pageHeaderImageId)) {
            $this->view->pageHeaderImage = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                'page_header'.$pageDetails->id.'_image',
                array(
                    'function' => '\KC\Repository\Logo::getPageLogo',
                    'parameters' => array($pageDetails->pageHeaderImageId['id'])
                ),
                ''
            );
        }
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->getRequest()->getActionName();
        $this->view->top20PopularOffers = $offers;
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            \FrontEnd_Helper_viewHelper::__link('link_nieuw'),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $this->view->shopId = '';
        $this->view->controllerName = $params['controller'];
        $this->view->offersType = 'newestOffer';
        $this->view->shopName = 'top20';
        $offersWithPagination = \FrontEnd_Helper_viewHelper::renderPagination($offers, $this->_getAllParams(), 20, 9);
        $this->view->offersWithPagination = $offersWithPagination;
        $editorWidgetInformation = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'newPage_editor_data',
                array(
                    'function' =>
                    'KC\Repository\EditorWidget::getEditorWigetData', 'parameters' => array('newPage')
                ),
                ''
            );
        $editorId = !empty($editorWidgetInformation[0]['editorId'])
            ? $editorWidgetInformation[0]['editorId'] : '';
        if (!empty($editorId)) {
            $this->view->editorInformation = \FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache(
                    'user_'.$editorId.'_details',
                    array(
                    'function' =>
                    'KC\Repository\User::getUserDetails', 'parameters' => array($editorId)
                    ),
                    ''
                );
        } else {
            $this->view->editorInformation = '';
        }
        $this->view->editorWidgetInformation = $editorWidgetInformation;
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
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'page-store';
        $this->view->widgetPosition = \KC\Repository\WidgetLocation::getWidgetPositionForFrontEnd(
            'page',
            'global',
            $pageDetails->id,
            '',
            $offers
        );
    }

    public static function getOfferUniqueCode($offerParameters)
    {
        $getOfferUniqueCode = \KC\Repository\CouponCode::returnAvailableCoupon($offerParameters['id']);
        return $getOfferUniqueCode;
    }

    public function offerCodeAction()
    {
        $this->_helper->layout->disableLayout();
        $getOfferUniqueCode = self::getOfferUniqueCode($this->_getAllParams());
        echo $getOfferUniqueCode['code'];
        exit();
    }

    public function offerUniqueCodeUpdateAction()
    {
        $this->_helper->layout->disableLayout();
        $offerParameters = $this->_getAllParams();
        $getOfferUniqueCode = self::getOfferUniqueCode($offerParameters);
        \KC\Repository\CouponCode::updateCodeStatus($offerParameters['id'], $getOfferUniqueCode['code']);
        exit();
    }

    public function offerViewCountAction()
    {
        $this->_helper->layout()->disableLayout();
        $offerId = $this->getRequest()->getParam('offerId');
        $cahceKey = 'viewCount_'.$offerId.'_text';
        $this->view->offerViewCount = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)$cahceKey,
            (array)array(
                'function' => '\KC\Repository\Offer::getViewCountByOfferId',
                'parameters' => array($offerId)
            ),
            ''
        );
    }
}
