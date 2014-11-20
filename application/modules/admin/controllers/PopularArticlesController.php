<?php

class Admin_PopularArticlesController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $conn2 = BackEnd_Helper_viewHelper::addConnection(); // connection
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
    
    public function indexAction()
    {
        $getArticlesList = Articles::getArticlesList();
        $popularArticles = PopularArticles::getPopularArticles();
        $changedArticlesDataForSorting = PopularArticles::changeArticlesDataForSorting($popularArticles);
        $articlesList = PopularArticles::getPopularArticlesAndNewestArticles(
            $getArticlesList,
            $changedArticlesDataForSorting
        );
        $this->view->articles = $articlesList;
    }

    public function savepopulararticlespositionAction()
    {
        PopularArticles::savePopularArticlePosition($this->getRequest()->getParam('articleIds'));
        $popularArticles = PopularArticles::getPopularArticles();
        echo Zend_Json::encode($popularArticles);
        exit();
    }
}
