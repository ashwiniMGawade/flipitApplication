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
    }
    
    public function indexAction()
    {
        $cannonicalPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $moneySavingPagePermalink = FrontEnd_Helper_viewHelper::__link('plus');
        $moneySavingPageDetails  =  FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_moneysavingpage".$moneySavingPagePermalink."_list", MoneySaving::getPageDetails($moneySavingPagePermalink));
        $mostReadArticles = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(3);
        
        $this->view->facebookDescription = trim(isset($moneySavingPageDetails[0]['metaDescription']) ? $moneySavingPageDetails[0]['metaDescription'] :'');
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->facebookTitle = isset($moneySavingPageDetails[0]['pageTitle']) ? $moneySavingPageDetails[0]['pageTitle'] :'';
        $this->view->facebookShareUrl = $moneySavingPagePermalink;
        $this->view->facebookImage = HTTP_PATH."public/images/plus_og.png";
        $this->view->twitterDescription = trim(isset($moneySavingPageDetails[0]['metaDescription']) ? $moneySavingPageDetails[0]['metaDescription'] :'');
        
        $this->view->pageTitle = isset($moneySavingPageDetails[0]['pageTitle']) ? $moneySavingPageDetails[0]['pageTitle'] :'';
        $this->view->permaLink = $moneySavingPagePermalink;
        $this->view->headTitle(trim(isset($moneySavingPageDetails[0]['metaTitle']) ? $moneySavingPageDetails[0]['metaTitle'] :''));
        $this->view->headMeta()->setName('description', trim(isset($moneySavingPageDetails[0]['metaDescription']) ? $moneySavingPageDetails[0]['metaDescription'] :''));
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($cannonicalPermalink);
        if(isset($moneySavingPageDetails[0]['customHeader'])) {
            $this->view->layout()->customHeader = "\n" . isset($moneySavingPageDetails[0]['customHeader']) ? $moneySavingPageDetails[0]['customHeader'] : '';
        }

        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->categoryWiseArticles = $categoryWiseArticles;
        $this->view->recentlyAddedArticles = $recentlyAddedArticles;
       
        if (!empty($moneySavingPageDetails)) {
            $this->view->pageDetails = $moneySavingPageDetails;
        } else {
            $error404 = 'HTTP/1.1 404 Not Found';
            $this->getResponse()->setRawHeader($error404);
        }
    }

    public function guidedetailAction()
    {
        $parameters = $this->_getAllParams();
        $permalink = $parameters['permalink'];
        $currentArticleDetails = Articles::getArticleByPermalink($permalink);
        $currentArticleCategory = $currentArticleDetails[0]['relatedcategory'][0]['articlecategory']['name'];
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $articlesRelatedToCurrentCategory = $categoryWiseArticles[$currentArticleCategory]; 
        $allArticles = Articles::getAllArticles();
        $userInformationObject = new User();
        
        if (!empty($currentArticleDetails)) {
            $this->view->headTitle(trim($currentArticleDetails[0]['metatitle']));
            $this->view->headMeta()->setName('description', trim($currentArticleDetails[0]['metadescription']));
            $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink) ;
            $this->view->facebookDescription = trim($currentArticleDetails[0]['metadescription']);
            $this->view->facebookLocale = FACEBOOK_LOCALE;
            $this->view->facebookTitle = $currentArticleDetails[0]['title'];
            $this->view->facebookShareUrl = HTTP_PATH_LOCALE.$currentArticleDetails[0]['permalink'];
            $this->view->facebookImage = FACEBOOK_IMAGE;
            $this->view->twitterDescription = trim($currentArticleDetails[0]['metadescription']);
            $this->view->mostReadArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));
            $this->view->currentArticle = $currentArticleDetails[0];
            $this->view->articlesRelatedToCurrentCategory = $articlesRelatedToCurrentCategory;
            $this->view->recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(4);
            $this->view->topPopularOffers = Offer::getTopOffers(5);
            $this->view->userDetails =  $userInformationObject->getUseretails($currentArticleDetails[0]['authorid']);
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }
}
