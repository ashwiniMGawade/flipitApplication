<?php

class Admin_MediaController extends Zend_Controller_Action
{
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
        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['content']['rights'] != '1') {
            $this->_redirect('/admin/auth/index');
        }
    }

    public function init()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    public function indexAction()
    {
    // action body
    }

    public function getmediaAction()
    {
        $params = $this->_getAllParams();
        $mediaList = \KC\Repository\Media::getmediaList($params);
        $request = \DataTable_Helper::createSearchRequest($params, array());
        $builder  = new NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($mediaList)
            ->add('number', 'm.id')
            ->add('text', 'm.name')
            ->add('text', 'm.authorName')
            ->add('text', 'm.alternatetext')
            ->add('text', 'm.created_at')
            ->add('text', 'm.fileurl');
        $result = $builder->getTable()->getResponseArray();
        echo Zend_Json::encode($result);
        die();
    }

    public function permanentdeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flash = $this->_helper->getHelper('FlashMessenger');

        if (\KC\Repository\Media::permanentDeleteMedia($id)) {
            $this->_helper->flashMessenger->addMessage(array('success'=>'Media has been deleted successfully.'));
            $this->_helper->redirector(null, 'media', null);
        } else {
            $this->_helper->flashMessenger->addMessage(array('error'=>'The media is not deleted.'));
            $this->_helper->redirector(null, 'media', null);
        }

        die();
    }

    public function addmediaAction()
    {
        if ($this->_request->isPost()) {
            $params = $this->_getAllParams();

            if (\KC\Repository\Media::updateMediaRecord($params)) {
                $this->_helper->flashMessenger->addMessage(array('success'=>'Media has been updated successfully!'));
                $this->_helper->redirector(null, 'media', null);
            } else {
                $this->_helper->flashMessenger->addMessage(array('error'=>'The media is not created!'));
                $this->_helper->redirector(null, 'media', null);
            }
        }
    }

    public function getmediadataAction()
    {
        $params = $this->getRequest()->getParam('id');
        $mediaList = \KC\Repository\Media::getMediadata($params);
        echo Zend_Json::encode(@$mediaList[0]);
        die();
    }

    public function editmediaAction()
    {
        $parmas = $this->_getAllParams();
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $id = $this->getRequest()->getParam('id');

        if (intval($id) > 0) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $data = $queryBuilder->select('m')
                ->from('KC\Entity\Media', 'm')
                ->where("m.id = " . $id)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $this->view->data = $data ;
            $this->view->id = $id;
        }

        if (@$parmas['act']=='delete') {
            $media= new \KC\Repository\Media();
            $flash = $this->_helper->getHelper('FlashMessenger');
            if ($media->permanentDeleteMedia($id)) {
                $message = $this->view->translate('Media has been deleted successfully');
                $flash->addMessage(array('success' => $message));
                $this->_helper->redirector(null, 'media', null);
            } else {
                $message = $this->view->translate('Problem in your data.');
                $flash->addMessage(array('error' => $message));
                $this->_helper->redirector(null, 'media', null);
            }
        }


        if ($this->_request->isPost()) {
            $parmas = $this->_getAllParams();
            $media = new \KC\Repository\Media();
            $flash = $this->_helper->getHelper('FlashMessenger');
            if ($media->editMediaRecord($parmas)) {
                $message = $this->view->translate('Media has been updated successfully');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH.'admin/media#'.$parmas['qString']);
            } else {
                $message = $this->view->translate('Problem in your data.');
                $flash->addMessage(array('error' => $message));
                $this->_redirect(HTTP_PATH.'admin/media#'.$parmas['qString']);
            }
        }
    }

    public function saveimageAction()
    {
        $upload_handler = new \KC\Repository\Media();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $upload_handler->getfile();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $upload_handler->deleteMedia();
                } else {
                    $upload_handler->post();
                }
                break;
            case 'DELETE':
                $upload_handler->deleteMedia();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
        die();
    }
}
