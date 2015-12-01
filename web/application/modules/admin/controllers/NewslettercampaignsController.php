<?php
use \Core\Domain\Factory\AdminFactory;
use \Core\Domain\Factory\SystemFactory;
use \Core\Service\Errors;

class Admin_NewslettercampaignsController extends Application_Admin_BaseController
{

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author mkaur
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
        $this->view->controllerName = $this->getRequest()
            ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if (
            $sessionNamespace->settings['rights']['content']['rights'] != '1'
            && $sessionNamespace->settings['rights']['content']['rights'] != '2'
        ) {
            $this->_redirect('/admin/auth/index');
        }
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }

    public function createAction()
    {

    }

    public function settingsAction()
    {
        $campaignHeaderSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_HEADER'));
        $this->view->campaign_header = !empty($campaignHeaderSetting) ? $campaignHeaderSetting->value : '';

        $campaignFooterSetting = SystemFactory::getSetting()->execute(array('name'=>'NEWSLETTER_CAMPAIGN_FOOTER'));
        $this->view->campaign_footer = !empty($campaignFooterSetting) ? $campaignFooterSetting->value : '';

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $isValid = true;

            $params['campaign-header'] = trim($params['campaign-header']);
            $params['campaign-footer'] = trim($params['campaign-footer']);
            if (true === empty($params['campaign-header'])) {
                $this->setFlashMessage('error', "campaign header is required");
                $isValid = false;
            }
            if (true === empty($params['campaign-footer'])) {
                $this->setFlashMessage('error', "campaign footer is required");
                $isValid = false;
            }

            $result = AdminFactory::updateSetting()->execute($campaignHeaderSetting, array('value'=>$params['campaign-header']));
            if ($result instanceof Errors) {
                $this->setFlashMessage('error', $result->getErrorsAll());
                $isValid = false;
            }

            $result = AdminFactory::updateSetting()->execute($campaignFooterSetting, array('value'=>$params['campaign-footer']));
            if ($result instanceof Errors) {
                $this->setFlashMessage('error', $result->getErrorsAll());
                $isValid = false;
            }            if (true == $isValid) {
                $this->setFlashMessage('success', 'Campaign settings has been updated successfully');
                $this->redirect(HTTP_PATH . 'admin/newslettercampaigns/settings');
            }
        }

    }

}
