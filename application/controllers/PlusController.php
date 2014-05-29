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
        $articleOverviewPagePermalink = FrontEnd_Helper_viewHelper::__link('plus');
        $articleOverviewPageDetails  =  FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_moneysavingpage".$articleOverviewPagePermalink."_list", MoneySaving::getPageDetails($articleOverviewPagePermalink));
        $mostReadArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(3);

        $this->view->pageTitle = isset($articleOverviewPageDetails[0]['pageTitle']) ? $articleOverviewPageDetails[0]['pageTitle'] :'';
        $this->view->permaLink = $articleOverviewPagePermalink;
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($articleOverviewPagePermalink);
        $customHeader = isset($articleOverviewPageDetails[0]['customHeader']) ? $articleOverviewPageDetails[0]['customHeader'] : '';
        $this->viewHelperObject->getFacebookMetaTags($this, isset($articleOverviewPageDetails[0]['pageTitle']) ? 
        $articleOverviewPageDetails[0]['pageTitle'] :'', trim(isset($articleOverviewPageDetails[0]['metaTitle']) ? 
        $articleOverviewPageDetails[0]['metaTitle'] :''), trim(isset($articleOverviewPageDetails[0]['metaDescription']) ? 
        $articleOverviewPageDetails[0]['metaDescription'] :''), $articleOverviewPagePermalink, HTTP_PATH."public/images/plus_og.png", $customHeader);

        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->categoryWiseArticles = $categoryWiseArticles;
        $this->view->recentlyAddedArticles = $recentlyAddedArticles;
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
