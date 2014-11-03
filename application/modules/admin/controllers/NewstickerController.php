<?php

class Admin_NewstickerController extends Zend_Controller_Action
{
    /**
     * check authentication before load the page
     *
     * @see Zend_Controller_Action::preDispatch()
     * @author kraj
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database

        $params = $this->_getAllParams();

        if (!Auth_StaffAdapter::hasIdentity()) {

            $referer = new Zend_Session_Namespace('referer');

            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($conn2);

        $this->view->controllerName = $this->getRequest()->getParam('controller');

        $this->view->action = $this->getRequest()->getParam('action');

    }
    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
        // get flashes
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );

        $message = $flash->getMessages ();

        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';

        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';

    }
    /**
     * add new newsticker
     * @author blal
     * @version 1.0
     */
    public function createnewstickerAction()
    {
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );

        $message = $flash->getMessages ();

        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';

        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';



        $shopObj = new \KC\Repository\Shop();

        $shopNames = $shopObj::getOfferShopList();

        $this->view->shopList = $shopNames;

        if ($this->getRequest ()->isPost ()){

            $params = $this->_getAllParams();

            $newsticker = \KC\Repository\OfferNews::saveNewsticker($params);

            self::updateVarnish($newsticker);

            $flash = $this->_helper->getHelper ( 'FlashMessenger' );




            if(filter_var($params['saveAndAddnew'], FILTER_VALIDATE_BOOLEAN)) {
                $message = $this->view->translate('News Ticker has been created successfully and add new newsticker again');
            } else {
                $message = $this->view->translate ( 'News Ticker has been created successfully' );
            }

            $flash->addMessage (array ('success' => $message ));


            if(filter_var($params['saveAndAddnew'], FILTER_VALIDATE_BOOLEAN)) {
                $this->_redirect ( HTTP_PATH . 'admin/newsticker/createnewsticker' );

            } else {
                $this->_redirect ( HTTP_PATH . 'admin/newsticker' );
            }


        }

    }

    /**
     * get all newsticker from database
     * @author blal
     * @version 1.0
     */
    public function newstickerlistAction()
    {
        $params = $this->_getAllParams ();

        $newstickerList = \KC\Repository\OfferNews::getnewstickerList($params);

        echo Zend_Json::encode ( $newstickerList );

        die ();

    }

    /**
     * delete newsticker by id
     *
     * @version 1.0
     * @author blal
     */
    public function deletenewstickerAction()
    {
        $id = $this->getRequest()->getParam('id');

        self::updateVarnish($id);

        $deletePermanent = \KC\Repository\OfferNews::deletenewsticker( $id );

        $flash = $this->_helper->getHelper('FlashMessenger');

        $message = $this->view->translate ( 'Record has been deleted successfully' );

        $flash->addMessage ( array ('success' => $message ) );

        die ();
    }

    /**
     * edit newsticker by id
     *
     * @author blal
     * @version 1.0
     */
    public function editnewstickerAction()
    {
        $shopObj = new \KC\Repository\Shop();

        $shopNames = $shopObj::getOfferShopList();

        $this->view->shopList = $shopNames;

       //$this->view->role = Zend_Auth::getInstance ()->getIdentity ()->roleId;
        $id = $this->getRequest ()->getParam ( 'id' );
        if ($id > 0) {
            // get edit newsticker
            $newsticker = \KC\Repository\OfferNews::getNewsticker ($id);
            $this->view->editNews = $newsticker;

        }

        if ($this->getRequest ()->isPost ()) {
            $params = $this->getRequest ()->getParams ();
            // cal to update newsticker function
            $newsticker = \KC\Repository\OfferNews::updateNewsticker( $params );


            self::updateVarnish($id);

            $flash = $this->_helper->getHelper ( 'FlashMessenger' );

            $message = $this->view->translate ( 'News Ticker has been updated successfully' );

            $flash->addMessage (array ('success' => $message ));

            $this->_redirect ( HTTP_PATH . 'admin/newsticker' );
        }
    }

    /**
     *  updateVarnish
     *
     *  update varnish table when a newsticker  is cretaed, edited, updated and deleted
     *  @param integer $id widget id
     */
    public function updateVarnish($id)
    {
                // Add urls to refresh in Varnish
            $varnishObj = new \KC\Repository\Varnish();

            # get all the urls related to this newsticker
            $varnishUrls = \KC\Repository\OfferNews::getAllUrls($id);

            # check $varnishUrls has atleast one
            if(isset($varnishUrls) && count($varnishUrls) > 0) {
                foreach($varnishUrls as $value) {
                    $varnishObj->addUrl( HTTP_PATH_FRONTEND . $value);
                }
            }
    }
}
