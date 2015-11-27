<?php

class Admin_TestController extends Application_Admin_BaseController
{

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author kraj
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
       \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


    }


    public function init()
    {
        /* Initialize action controller here */
        $this->_redirect('/admin/auth/index');
    }

    public function indexAction()
    {
        // action body



        # gte top 5 vouchercodes
        $topVouchercodes = \FrontEnd_Helper_viewHelper::gethomeSections("popular", 5);



        # if top korting are less than 15 then add newest code to fill up the list upto 15
        if(count($topVouchercodes) < 5 ) {
            # the limit of popular oces
            $additionalCodes = 5 - count($topVouchercodes) ;

            # GET TOP 5 POPULAR CODE
            $additionalTopVouchercodes = $offers = Offer::commongetnewestOffers('newest', $additionalCodes);


            foreach ($additionalTopVouchercodes as $key => $value) {

                $topVouchercodes[] =     array('id'=> $value['shop']['id'],
                                                'permalink' => $value['shop']['permalink'],
                                                'offer' => $value
                                              );
            }
        }





        //array_chunk($visitors, 1000);
        //print_r(array_chunk($input_array, 2, true));


    }





}
