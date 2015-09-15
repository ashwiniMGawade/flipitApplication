<?php
use \Core\Domain\Factory\AdminFactory;

class Admin_LandingpagesController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['administration']['rights'] != '1') {
            $this->redirect('/admin');
        }
    }

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function getAction()
    {
        $landingPages = array(
            array(
                'id' => '1',
                'title' => 'Zalando Landing Page',
                'shopName' => 'Zalando',
                'permalink' => 'zalando-landing-page',
                'shopOffersCount' => 123,
                'shopCouponCount' => 21,
                'shopClickoutCount' => 67243,
                'status' => 1,
                'offlineSince' => ''
            ),
            array(
                'id' => '2',
                'title' => 'Hema Landing Page',
                'shopName' => 'Hema',
                'permalink' => 'hema-landing-page',
                'shopOffersCount' => 45,
                'shopCouponCount' => 251,
                'shopClickoutCount' => 57,
                'status' => 1,
                'offlineSince' => ''
            ),
            array(
                'id' => '3',
                'title' => 'Albelli Landing Page',
                'shopName' => 'Albelli',
                'permalink' => 'albelli-landing-page',
                'shopOffersCount' => 787,
                'shopCouponCount' => 3,
                'shopClickoutCount' => 6547,
                'status' => 0,
                'offlineSince' => new \DateTime('now')
            )
        );
        $response = \DataTable_Helper::createResponse(1, $landingPages, count($landingPages));
        echo Zend_Json::encode($response);
        exit;
    }

    public function createAction()
    {
        $conditions = array(
            'deleted' => 0,
            'status' => 1
        );

        $order = array(
            'name' => 'ASC'
        );
        $this->view->shops = AdminFactory::getShops()->execute($conditions, $order);
    }
}
