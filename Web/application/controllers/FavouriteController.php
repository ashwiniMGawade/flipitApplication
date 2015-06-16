<?php
require_once 'Zend/Controller/Action.php';
class FavouriteController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->viewHelperObject = new \FrontEnd_Helper_viewHelper();
    }

    public function yourbrandsAction()
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->successMessage = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->errorMessage = isset($message[0]['error']) ?
        $message[0]['error'] : '';
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');

        if (\Auth_VisitorAdapter::hasIdentity()) {
            $searchBrandForm = new \Application_Form_SearchBrand();
            $this->view->form = $searchBrandForm;

            if ($this->getRequest()->isPost()) {
                if ($searchBrandForm->isValid($this->getRequest()->getPost())) {
                    $stores = \KC\Repository\Shop::getStoresForSearchByKeyword(
                        $searchBrandForm->getValue('searchBrand'),
                        25,
                        'favourite'
                    );
                    $stores = array_slice($stores, 0, 1);
                } else {
                    $searchBrandForm->highlightErrorElements();
                    $stores = $this->_helper->Favourite->getPopularStores();
                }
            } else {
                $stores = $this->_helper->Favourite->getPopularStores();
            }
            
            $favouriteShops = $this->_helper->Favourite->getFavoritesStores();
            $this->view->popularShops = $this->_helper->Favourite->filterAlreadyFavouriteShops($stores, $favouriteShops);
            $this->view->favouriteShops = $favouriteShops;
            $userDetails = \FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'visitor_'.\Auth_VisitorAdapter::getIdentity()->id.'_details',
                array(
                    'function' => '\KC\Repository\Visitor::getUserFirstName',
                    'parameters' => array(Auth_VisitorAdapter::getIdentity()->id)
                )
            );

            $this->view->userDetails = isset($userDetails[0]) ? $userDetails[0] : '';
            $this->view->pageCssClass = 'profile-page';
        } else {
            $this->_redirect('/');
        }
    }

    public function youroffersAction()
    {
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $favoriteShopsOffers = \FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'visitor_'.\Auth_VisitorAdapter::getIdentity()->id.'_favouriteShopOffers',
                array(
                    'function' => '\KC\Repository\Visitor::getFavoriteShopsOffers',
                    'parameters' => array()
                )
            );
            
            $offers = $this->_helper->Favourite->getOffers($favoriteShopsOffers);
            $userDetails = \FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'visitor_'.\Auth_VisitorAdapter::getIdentity()->id.'_details',
                array(
                    'function' => '\KC\Repository\Visitor::getUserFirstName',
                    'parameters' => array(\Auth_VisitorAdapter::getIdentity()->id)
                )
            );
            $this->view->favouriteShopsOffers = $offers;
            $this->view->userDetails = $userDetails[0];
            $this->view->pageCssClass = 'profile-page';
        } else {
            $this->_redirect('/');
        }
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
    }
    
    public function sharesocialcodeAction()
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->successMessage = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->errorMessage = isset($message[0]['error']) ? $message[0]['error'] : '';
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $this->view->pageCssClass = 'social-page';

        $this->view->offers = '';
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $userDetails = \FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'visitor_'.\Auth_VisitorAdapter::getIdentity()->id.'_details',
                array(
                    'function' => '\KC\Repository\Visitor::getUserFirstName',
                    'parameters' => array(\Auth_VisitorAdapter::getIdentity()->id)
                )
            );
            $this->view->offers = \KC\Repository\Offer::getNewestOffers(
                'UserGeneratedOffers',
                '',
                '',
                \Auth_VisitorAdapter::getIdentity()->id
            );
        }
        $this->view->userDetails = isset($userDetails[0]) ? $userDetails[0] : '';
        $socialcodeForm = new \Application_Form_SocialCodeSettingForm();
        $this->view->zendForm = $socialcodeForm;
        if ($this->getRequest()->isPost()) {
            if ($socialcodeForm->isValid($this->getRequest()->getPost())) {
                $socialcode = $socialcodeForm->getValues();
                $parameters =  array(
                    'nickname' => $socialcode['nickname'],
                    'shopId'=> base64_encode($socialcode['store']),
                    'title'=> $socialcode['title'],
                    'offerUrl'=> $socialcode['offerUrl'],
                    'code'=> $socialcode['code'],
                    'offerDetails'=>$socialcode['offerDetails'],
                    'expireDate'=>$socialcode['expireDate']
                );
                \KC\Repository\UserGeneratedOffer::addOffer($parameters);
                $socialcodeForm->reset();
                $flashMessage->addMessage(
                    array(
                        'success' => \FrontEnd_Helper_viewHelper::__translate('Thanks for sharing your coupon with the Flipit Community! Our team will check the details and publish the code')
                    )
                );
                $redirectUrl = HTTP_PATH_LOCALE
                    .\FrontEnd_Helper_viewHelper::__link('link_sharesocialcode');
                $this->_redirect($redirectUrl);
            } else {
                $socialcodeForm->highlightErrorElements();
            }
        }
        $this->_redirect('/');
    }
}
