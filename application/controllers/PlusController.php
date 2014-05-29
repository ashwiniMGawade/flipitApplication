<?php
class PlusController extends Zend_Controller_Action
{
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else {
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }
    public function indexAction()
    {
        $cannonicalPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $moneySavingPagePermalink = FrontEnd_Helper_viewHelper::__link('plus');
        $moneySavingPageDetails  =  FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_moneysavingpage".$moneySavingPagePermalink."_list", MoneySaving::getPageDetails($moneySavingPagePermalink));
        $mostReadArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(3);

        $this->view->pageTitle = isset($moneySavingPageDetails[0]['pageTitle']) ? $moneySavingPageDetails[0]['pageTitle'] :'';
        $this->view->permaLink = $moneySavingPagePermalink;
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($cannonicalPermalink);
        $customHeader = isset($moneySavingPageDetails[0]['customHeader']) ? $moneySavingPageDetails[0]['customHeader'] : '';
        $this->viewHelperObject->getFacebookMetaTags($this, isset($moneySavingPageDetails[0]['pageTitle']) ? 
        $moneySavingPageDetails[0]['pageTitle'] :'', trim(isset($moneySavingPageDetails[0]['metaTitle']) ? 
        $moneySavingPageDetails[0]['metaTitle'] :''), trim(isset($moneySavingPageDetails[0]['metaDescription']) ? 
        $moneySavingPageDetails[0]['metaDescription'] :''), $moneySavingPagePermalink, HTTP_PATH."public/images/plus_og.png", $customHeader);

        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->categoryWiseArticles = $categoryWiseArticles;
        $this->view->recentlyAddedArticles = $recentlyAddedArticles;
       
        if (!empty($moneySavingPageDetails)) {
            $this->view->pageDetails = $moneySavingPageDetails;
        } else {
            $error404 = 'HTTP/1.1 404 Not Found';
            $this->getResponse()->setRawHeader($error404);
        }
        $this->view->pageCssClass = 'saving-page';
    }
  

    public function guidedetailAction()
    {
        $parameters = $this->_getAllParams();
        $permalink = $parameters['permalink'];
        $articleDetails = Articles::getArticleByPermalink($permalink);
        $currentArticleCategory = $articleDetails[0]['relatedcategory'][0]['articlecategory']['name'];
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $articlesRelatedToCurrentCategory = $categoryWiseArticles[$currentArticleCategory]; 
                
        if (!empty($articleDetails)) {
            $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink) ;
            $this->view->mostReadArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));    
            $this->view->articleDetails = $articleDetails[0];
            $this->view->articlesRelatedToCurrentCategory = $articlesRelatedToCurrentCategory;
            $this->view->recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(4);
            $this->view->topPopularOffers = Offer::getTopOffers(5);
            $userInformationObject = new User();
            $this->view->userDetails =  $userInformationObject->getUserDetails($articleDetails[0]['authorid']);
            $customHeader = '';
            $this->viewHelperObject->getFacebookMetaTags($this, $articleDetails[0]['title'], trim($articleDetails[0]['metatitle']), trim($articleDetails[0]['metadescription']), $articleDetails[0]['permalink'], FACEBOOK_IMAGE, $customHeader);
            $this->view->pageCssClass = 'in-savings-page';
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }
}
