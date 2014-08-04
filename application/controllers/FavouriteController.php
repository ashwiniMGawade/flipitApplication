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
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function yourbrandsAction()
    {
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        if (Auth_VisitorAdapter::hasIdentity()) {
            $this->view->popularShops = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'alreadyFavourite_'.Auth_VisitorAdapter::getIdentity()->id.'_shops',
                array(
                    'function' => 'FavoriteShop::filterAlreadyFavouriteShops',
                    'parameters' => array(Shop::getPopularStores(25))
                )
            );
            $this->view->favouriteShops = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'all_'.Auth_VisitorAdapter::getIdentity()->id.'_favouriteShops',
                array(
                    'function' => 'Visitor::getFavoriteShops',
                    'parameters' => array(Auth_VisitorAdapter::getIdentity()->id)
                )
            );
            $this->view->pageCssClass = 'brands-page';
        } else {
            $this->_redirect('/');
        }
    }

    public function youroffersAction()
    {
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        if (Auth_VisitorAdapter::hasIdentity()) {
            $favoriteShopsOffers = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'visitor_'.Auth_VisitorAdapter::getIdentity()->id.'_favouriteShopOffers',
                array(
                    'function' => 'Visitor::getFavoriteShopsOffers',
                    'parameters' => array()
                )
            );
            $offers = $this->_helper->Favourite->getOffers($favoriteShopsOffers);
            $favouriteShopsOffersWithPagination = FrontEnd_Helper_viewHelper::renderPagination(
                $offers,
                $this->_getAllParams(),
                30,
                4
            );
            $userDetails = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'visitor_'.Auth_VisitorAdapter::getIdentity()->id.'_details',
                array(
                    'function' => 'Visitor::getUserDetails',
                    'parameters' => array(Auth_VisitorAdapter::getIdentity()->id)
                )
            );
            $this->view->favouriteShopsOffers = $favouriteShopsOffersWithPagination;
            $this->view->userDetails = $userDetails[0];
            $this->view->pageCssClass = 'youroffers-page';
        } else {
            $this->_redirect('/');
        }
    }
}
