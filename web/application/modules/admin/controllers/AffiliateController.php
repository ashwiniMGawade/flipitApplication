<?php

class Admin_AffiliateController extends Application_Admin_BaseController
{
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
        if (!\Auth_StaffAdapter::hasIdentity ()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect ( '/admin/auth/index' );
        }
        \BackEnd_Helper_viewHelper::closeConnection ( $conn2 );
        $this->view->controllerName = $this->getRequest ()->getParam ( 'controller' );
        $this->view->action = $this->getRequest ()->getParam ( 'action' );

    }




    /**
     * Affiliate Helper file for switch the connection
     * @see Zend_Controller_Action::init()
     * @author pkaur4
     * @version 1.0
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * show success and error messages
     * @author blal
     * @version 1.0
     */
    public function indexAction()
    { 
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $flash->getMessages ();
        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';

    }

    /**
     * add new network in database
     * @author blal
     * @version 1.0
     */
    public function addaffiliateAction()
    {

        if ($this->getRequest ()->isPost ()) {

        $params = $this->getRequest ()->getParams ();
        $network = \KC\Repository\AffliateNetwork::addNewnetwork($params);
        if ($network != null) {
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            $message = $this->view->translate ( 'Network has been created successfully' );
            $flash->addMessage ( array ('success' => $message ) );
            $this->_redirect ( HTTP_PATH . 'admin/affiliate' );
        }
       }
    }


     /**
     * get all networks from database
     * @return array $data
     * @author blal
     * @version 1.0
     */
    public function networklistAction()
    {
        $params = $this->_getAllParams();
        // cal to function in network model class
        $networkList =  \KC\Repository\AffliateNetwork::getNetworkList($params);
        echo Zend_Json::encode ( $networkList );
        die ;
    }

    /**
     * Get top five networks from database by search text in autocomplete
     * @param string $text
     * @author blal
     * @version 1.0
     */
    public function searchtopfivenetworkAction()
    {
        $srh = $this->getRequest ()->getParam ( 'keyword' );
        $data = \KC\Repository\AffliateNetwork::searchTopFiveNetwork( $srh);
        $ar = array ();
        if (sizeof ( $data ) > 0) {
            foreach ( $data as $d ) {

             $ar [] = $d ['name'];

            }
        } else {

            $msg = $this->view->translate ( 'No Record Found' );
            $ar [] = $msg;
        }
        echo Zend_Json::encode ( $ar );
        die ();

    }

    /**
     * change the status of networks(online/offline)
     * @author blal
     * @version 1.0
     */

    public function affiliatestatusAction()
    {
    $params = $this->_getAllParams ();
    $status = \KC\Repository\AffliateNetwork::changeStatus($params);
    die;
   }

   /**
    * edit network by id and update network
    * @author blal
    * @version 1.0
    */
   public function editaffiliateAction()
   {
    $u = \Auth_StaffAdapter::getIdentity();
    $this->view->role = $u->users->id;
    $id = $this->getRequest ()->getParam ( 'id' );

     $params = $this->_getAllParams();
     //function call to show network list in dropdown in edit case
     $network =  \KC\Repository\AffliateNetwork::networklistDropdown($params);
     $this->view->getNetworkdropdown = $network;


     if ($id > 0) {

        // get network to edit
        $network = \KC\Repository\AffliateNetwork::getNetworkForEdit($id);
        $this->view->editNetwork = $network;
    }

    if ($this->getRequest ()->isPost ()) {

        $params = $this->getRequest ()->getParams ();

        // cal to update network function
        $network = \KC\Repository\AffliateNetwork::updateNetwork($params );

        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $this->view->translate ( 'Network details have been updated successfully' );
        $flash->addMessage ( array ('success' => $message ) );
        $this->_redirect ( HTTP_PATH . 'admin/affiliate' );
    }
  }

  /**
   * delete network from list
   * @author blal
   * @version 1.0
   */
  public function deletenetworkAction()
  {
    $params = $this->_getAllParams ();

    \KC\Repository\AffliateNetwork::deleteNetwork( $params );
    $flash = $this->_helper->getHelper ( 'FlashMessenger' );
    $message = $this->view->translate ( 'Network has been deleted successfully' );
    $flash->addMessage ( array ('success' => $message ) );
    die ();
  }


  /**
   * replace existing network with network from dropdown
   * @author blal
   * @version 1.0
   */
   public function replacenetworkAction()
   {
       $params = $this->_getAllParams ();
       $network = \KC\Repository\AffliateNetwork::replaceNetwork($params);
       die;
   }
}
