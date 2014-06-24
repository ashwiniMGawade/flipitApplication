<?php
class Admin_EmailcampainController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $campainModel = new EmailCampain();
        $campains     = $campainModel->fetchAll();
    }

    public function newAction()
    {
        $campain                = array();
        $campain['id']          = false;
        $campain['sender']      = 'nieuws@locale-name.ex';
        $campain['subject']     = 'Subject';

        $this->view->campain    = $campain;
    }

    public function saveAction(){

        $campainModel = new EmailCampain();

        if($this->_request->isPost()){
            $postData = $this->_request->getPost();
            $campainModel->saveForm($postData);
        }
        exit;
    }
}
