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
            $this->view->popularShops = FavoriteShop::filterAlreadyFavouriteShops(Shop::getPopularStores(25));
            $this->view->favouriteShops = Visitor::getFavoriteShops(Auth_VisitorAdapter::getIdentity()->id);
            $this->view->pageCssClass = 'brands-page';
        } else {
            $this->_redirect('/');
        }
    }

    public function youroffersAction()
    {
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        if (Auth_VisitorAdapter::hasIdentity()) {
            $favoriteShopsOffers = Visitor::getFavoriteShopsOffers();
            $offers = $this->_helper->Favourite->getOffers($favoriteShopsOffers);
            $userDetails = Visitor::getUserDetails(Auth_VisitorAdapter::getIdentity()->id);
            $this->view->favouriteShopsOffers = $offers;
            $this->view->userDetails = $userDetails[0];
            $this->view->pageCssClass = 'youroffers-page';
        } else {
            $this->_redirect('/');
        }
    }
}
