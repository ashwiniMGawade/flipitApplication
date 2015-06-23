<?php

/**
 * Rss feeds  Generation
 *
 * @author Surinderpal Singh
 *
 */
class Admin_RefreshvarnishController extends Zend_Controller_Action
{
    public function init()
    {
        //ini_set ("display_errors", "1");
        //error_reporting(E_ALL);

        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author Kraj
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection (); // connection
        $params = $this->_getAllParams ();
        if (! \Auth_StaffAdapter::hasIdentity ()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect ( '/admin/auth/index' );
        }
        \BackEnd_Helper_viewHelper::closeConnection ( $conn2 );
        $this->view->controllerName = $this->getRequest ()->getParam ( 'controller' );
        $this->view->action = $this->getRequest ()->getParam ( 'action' );

    }


    public function indexAction()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);


        #translating sitemaps names
        $sitemaps = \FrontEnd_Helper_viewHelper::__link('link_sitemap');
        $plus = \FrontEnd_Helper_viewHelper::__link('link_plus');
        $main = \FrontEnd_Helper_viewHelper::__link('link_main');
        $shops = \FrontEnd_Helper_viewHelper::__link('link_shops');

        $sitemap_shops = $sitemaps.'_'.$shops.'.xml';
        $sitemap_plus = $sitemaps.'_'.$plus.'.xml';
        $sitemap_main = $sitemaps.'_'.$main.'.xml';


        $varnishObj = new \KC\Repository\Varnish();

        $shopFile =  realpath(ROOT_PATH.'sitemaps/'.$sitemap_shops) ;

        if(file_exists( $shopFile)) {

            // Parse the 3 xml's and add the url's to the varnish table
            $shop_urls = simplexml_load_file($shopFile);

            if (!empty($shop_urls) ) {
                foreach ($shop_urls as $url) {
                    $varnishObj->addUrl( $url->loc );
                }
            }
        }

        $mainFile = realpath(ROOT_PATH.'sitemaps/'.$sitemap_main) ;
        if(file_exists( $mainFile)) {
            $main_urls = simplexml_load_file($mainFile);
            if (!empty($main_urls) ) {
                foreach ($main_urls as $url) {
                    $varnishObj->addUrl( $url->loc );
                }
            }
        }


        $bespaarFile = realpath(ROOT_PATH.'sitemaps/'.$sitemap_plus) ;
        if(file_exists( $bespaarFile)) {
            $bespaar_urls = simplexml_load_file($bespaarFile);
            if (!empty($bespaar_urls)) {
                foreach ($bespaar_urls as $url) {
                    $varnishObj->addUrl( $url->loc );
                }
            }
        }

        //add the flash mesage that the newsletter has been sent
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message =  'Varnish has been successfully refreshed'  ;
        $flash->addMessage(array('success' => $message));
        $this->_helper->redirector( 'index', 'index', null ) ;


    }

}
