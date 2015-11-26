<?php

class Admin_PopulararticlesController extends Application_Admin_BaseController
{
    public function preDispatch()
    {
        $dbConnection = \BackEnd_Helper_viewHelper::addConnection(); // connection
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($dbConnection);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }
    
    public function indexAction()
    {
        $getArticlesList = \KC\Repository\Articles::getArticlesList();
        $popularArticles = \KC\Repository\PopularArticles::getPopularArticles();
        $changedArticlesDataForSorting = \KC\Repository\PopularArticles::changeArticlesDataForSorting($popularArticles);
        \KC\Repository\PopularArticles::saveArticles($getArticlesList, $changedArticlesDataForSorting);
        $this->view->articles = \KC\Repository\PopularArticles::getPopularArticles();
    }

    public function savepopulararticlespositionAction()
    {
        //print_r($this->getRequest()->getParam('articleIds'));die;
        \KC\Repository\PopularArticles::savePopularArticlePosition($this->getRequest()->getParam('articleIds'));
        $popularArticles = \KC\Repository\PopularArticles::getPopularArticles();
        echo Zend_Json::encode($popularArticles);
        exit();
    }
}
