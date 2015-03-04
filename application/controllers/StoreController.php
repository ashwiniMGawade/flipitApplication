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
            $referer = $_SERVER['HTTP_REFERER'];
            if ($referer == '') {
                $this->_redirect(HTTP_PATH_LOCALE. $shopInfo[0]['permaLink']);
                exit();
            }
        }

        $currentShopId = $shopParams['id'];
        $shopId = $this->getRequest()->getParam('id');

        if ($shopId) {
            $ShopList = $shopId.'_list';
            $allShopDetailKey = 'shopDetails_'.$ShopList;
            $shopInformation = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allShopDetailKey,
                array('function' => 'KC\Repository\Shop::getStoreDetails', 'parameters' => array($shopId)
                ),
                ''
            );
            $allOffersInStoreKey = '6_topOffers'.$ShopList;
            $offers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allOffersInStoreKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                    'parameters' => array("all", 10, $shopId, 0)
                ),
                ''
            );
            $allExpiredOfferKey = 'shop_expiredOffers'.$ShopList;
            $expiredOffers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allExpiredOfferKey,
                array(
                    'function' => 'FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array("expired", 8, $shopId)
                ),
                ''
            );
            $allLatestUpdatesInStoreKey = '4_shopLatestUpdates_'.$ShopList;
            $latestShopUpdates = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)$allLatestUpdatesInStoreKey,
                array(
                    'function' => '\FrontEnd_Helper_viewHelper::getShopCouponCode',
                    'parameters' => array('latestupdates', 4, $shopId)
                ),
                ''
            );
            $moneySavingGuideArticle = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_moneySavingArticles_'.$ShopList,
                array(
                    'function' => 'KC\Repository\MoneySaving::generateShopMoneySavingGuideArticle',
                    'parameters' => array('moneysaving', 3, $shopId)
                ),
                ''
            );

            $sixShopReasons = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_sixReasons_'.$ShopList,
                array(
                    'function' => 'KC\Repository\ShopReasons::getShopReasons',
                    'parameters' => array($shopId)
                ),
                ''
            );

            if (!count($shopInformation) >0) {
                $localeUrl = HTTP_PATH_LOCALE;
                $this->_helper->redirector->setCode(301);
                $this->_redirect($localeUrl);
            }

            if ($shopInformation[0]['showChains']) {
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

            $shopImage = PUBLIC_PATH_CDN.ltrim($shopInformation[0]['logo']['path'], "/")
                .'thum_medium_store_'.$shopInformation[0]['logo']['name'];
            $this->view->shopBranding = KC\Repository\Shop::getShopBranding($shopId);
        } else {
            $urlToRedirect = HTTP_PATH_LOCALE. 'store/index';
            $this->_redirect($urlToRedirect);
        }
        
        $this->view->currentStoreInformation = $shopInformation;
        $this->view->moneySavingGuideArticle = $moneySavingGuideArticle;
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->offers = $offers;
        $this->view->sixShopReasons = $this->_helper->Store->changeIndexOfSixReasons($sixShopReasons);

        if ($this->view->currentStoreInformation[0]['affliateProgram']==0 && count($this->view->offers) <=0) {
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

        $this->view->expiredOffers = $expiredOffers;
        if ($shopInformation[0]['affliateProgram'] == 0) {
            $numberOfSimilarOffers = 10;
        } else {
            $numberOfSimilarOffers = 3;
        }

        $similarShopsAndSimilarCategoriesOffers = \FrontEnd_Helper_viewHelper::getShopCouponCode(
            'similarStoresAndSimilarCategoriesOffers',
            $numberOfSimilarOffers,
            $shopId
        );

        $this->view->similarShopsAndSimilarCategoriesOffers = '';
        if (!empty($similarShopsAndSimilarCategoriesOffers)) {
            $this->view->similarShopsAndSimilarCategoriesOffers = $this->_helper->Store->removeDuplicateShopsOffers(
                $similarShopsAndSimilarCategoriesOffers
            );
        }

        $this->view->countPopularOffers = count(
            \FrontEnd_Helper_viewHelper::commonfrontendGetCode('popular', $shopRecordsLimit, $currentShopId)
        );
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

        $this->view->ballonEditorText = KC\Repository\EditorBallonText::getEditorText($shopId);
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
            $this->view->similarShops = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                (string)'shop_similar_shops',
                array(
                    'function' => 'KC\Repository\Shop::getSimilarShops',
                    'parameters' => array($shopId, 11)
                )
            );
        }

        $frontendSidebarHelper = new \FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->popularStoresList = $frontendSidebarHelper->PopularShopWidget();

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
        $this->view->pageCssClass = 'author-page';
    }

    public function indexAction()
    {
        $permalink = FrontEnd_Helper_viewHelper::getPagePermalink();
        $startingCharacter = $this->_helper->Store->getActualPermalink($permalink, 'firstCharacter');
        $endingCharacter = $this->_helper->Store->getActualPermalink($permalink, 'lastCharacter');
        $startingAndEndingCharacter = $startingCharacter. "-". $endingCharacter;
        $permalink = $this->_helper->Store->getActualPermalink($permalink, 'permalink');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
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
            '10_popularShops_list',
            array('function' => '\KC\Repository\Shop::getAllPopularStores', 'parameters' => array(10)),
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
        $this->view->popularStores = $popularStores;
        $this->view->pageCssClass = 'all-stores-page';
    }

    public function howtoguideAction()
    {
        $howToGuidePermalink = ltrim(\Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = \FrontEnd_Helper_viewHelper::generateCononical($howToGuidePermalink);
        $parameters = $this->_getAllParams();
        $cacheKey = \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($parameters['permalink']);
        $howToGuides = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'store_'.$cacheKey.'_howToGuide',
            array('function' => 'KC\Repository\Shop::getshopDetails', 'parameters' => array($parameters['permalink']))
        );

        if (empty($howToGuides)) {
            throw new \Zend_Controller_Action_Exception('', 404);
        }

        $ShopList = $howToGuides[0][0]['id'].'_list';
        $allShopDetailKey = 'shopDetails_'.$ShopList;
        $shopInformation = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allShopDetailKey,
            array('function' => 'KC\Repository\Shop::getStoreDetails', 'parameters' => array($howToGuides[0][0]['id']))
        );

        if ($shopInformation[0]['showChains']) {
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

        $allLatestUpdatesInStoreKey = 'ShoplatestUpdates_'.$ShopList;
        $latestShopUpdates = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allLatestUpdatesInStoreKey,
            array('function' => '\FrontEnd_Helper_viewHelper::getShopCouponCode', 'parameters' => array(
                'latestupdates',
                4,
                $howToGuides[0][0]['id'])
            )
        );
        $allOffersInStoreKey = '6_topOffersHowto'.$ShopList;
        $offers = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            $allOffersInStoreKey,
            array('function' => '\FrontEnd_Helper_viewHelper::commonfrontendGetCode',
                'parameters' => array('topSixOffers', 6, $howToGuides[0][0]['id'], 0)
            )
        );
        $offers = array_chunk($offers, 3);
        $this->view->offers = $offers;
        $this->view->currentStoreInformation = $shopInformation;
        $frontEndViewHelper = new \FrontEnd_Helper_SidebarWidgetFunctions();
        $this->view->popularStoresList = $frontEndViewHelper->PopularShopWidget();
        $this->view->latestShopUpdates = $latestShopUpdates;
        $this->view->howToGuides=$howToGuides;
        $shopName = isset($shopInformation[0]['name']) ? $shopInformation[0]['name'] : '';
        $howToGuides = isset($howToGuides[0][0]['howtoTitle']) ? $howToGuides[0][0]['howtoTitle'] : '';
        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            str_replace('[shop]', $shopName, $howToGuides),
            '',
            trim(isset($howToGuides[0][0]['howtoMetaDescription']) ? $howToGuides[0][0]['howtoMetaDescription'] : ''),
            isset($howToGuides[0][0]['permaLink']) ? $howToGuides[0][0]['permaLink'] : '',
            FACEBOOK_IMAGE,
            $customHeader
        );

        $signUpFormForStorePage = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
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

    public function socialcodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $shopPermalink = $this->getRequest()->getParam('shopPermalink');
        $shopId = base64_encode(Shop::getShopIdByPermalink($shopPermalink));
        $socialcodeForm = new \Application_Form_SocialCode();
        $socialcodeForm->getElement('shopPermalink')->setValue($shopPermalink);
        $socialcodeForm->getElement('shopId')->setValue($shopId);
        $this->view->zendForm = $socialcodeForm;
        if ($this->getRequest()->isPost()) {
            if ($socialcodeForm->isValid($this->getRequest()->getPost())) {
                $socialcode = $socialcodeForm->getValues();
                KC\Repository\UserGeneratedOffer::addOffer($socialcode);
                $this->_redirect(HTTP_PATH_LOCALE. $socialcode['shopPermalink'].'?name=thanksMessage');
                exit();
            } else {
                $socialcodeForm->highlightErrorElements();
                $socialcode = $socialcodeForm->getValues();
                $this->_redirect(HTTP_PATH_LOCALE. $socialcode['shopPermalink']);
            }
        }
        $this->_redirect('/');
    }

    public function socialcodethanksAction()
    {
        $this->_helper->layout()->disableLayout();
    }
}
