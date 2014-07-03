<?php
class PlusController extends Zend_Controller_Action
{
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        $articlesOverviewPagePermalink  = 'plus';
        $pageDetails = Page::getPageDetailsFromUrl(FrontEnd_Helper_viewHelper::getPagePermalink());

        $mostReadArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", array('function' =>
                'MoneySaving::getMostReadArticles', 'parameters' => array(3)));

        $categoryWiseArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_categoryWiseArticles_list", array('function' =>
                'MoneySaving::getCategoryWiseArticles', 'parameters' => array()));

        foreach ($categoryWiseArticles['blog'] as $key => $categoryWiseArticle) {
            $categoryWiseArticles['blog'][$key]['authorDetails'] =
                User::getUserDetails($categoryWiseArticle['authorid']);
        }

        foreach ($categoryWiseArticles['savingtip'] as $key => $categoryWiseArticle) {
            $categoryWiseArticles['savingtip'][$key]['authorDetails'] =
                User::getUserDetails($categoryWiseArticle['authorid']);
        }

        $recentlyAddedArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("all_recentlyAddedArticles_list", array('function' =>
                'MoneySaving::getRecentlyAddedArticles', 'parameters' => array(2)));

        foreach ($recentlyAddedArticles as $key => $recentlyAddedArticle) {
            $recentlyAddedArticles[$key]['authorDetails'] =
                User::getUserDetails($recentlyAddedArticle['authorid']);
        }

        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'all_popularshop_list',
            (array)array('function' => 'Shop::getAllPopularStores', 'parameters' => array(10)),
            true
        );
        
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $this->view->permaLink = $articlesOverviewPagePermalink;
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($articlesOverviewPagePermalink);
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            $articlesOverviewPagePermalink,
            HTTP_PATH."public/images/plus_og.png",
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $this->view->pageHeaderImage = Logo::getPageLogo($pageDetails->pageHeaderImageId);
        $this->view->popularStores = $popularStores;
        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->categoryWiseArticles = $categoryWiseArticles;
        $this->view->recentlyAddedArticles = $recentlyAddedArticles;
        $this->view->pageCssClass = 'article-page home-page';
    }


    public function guidedetailAction()
    {
        $articleDetails = Articles::getArticleByPermalink($this->getRequest()->getParam('permalink'));
        $currentArticleCategory = $articleDetails[0]['relatedcategory'][0]['articlecategory']['name'];
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles(4);
        $articlesRelatedToCurrentCategory =
            !empty($categoryWiseArticles[$currentArticleCategory])
            ? $categoryWiseArticles[$currentArticleCategory]
            : '';
        $incrementArticleViewCountValue  = FrontEnd_Helper_viewHelper::
            viewCounter('article', 'onload', $articleDetails[0]['id']);
        if (!empty($articleDetails)) {
            $this->view->canonical =
                FrontEnd_Helper_viewHelper::generateCononical($this->getRequest()->getParam('permalink'));
            $this->view->mostReadArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", array(
                    'function' => 'MoneySaving::getMostReadArticles', 'parameters' => array(3)));
            $this->view->articleDetails = $articleDetails[0];
            $this->view->articlesRelatedToCurrentCategory = $articlesRelatedToCurrentCategory;
            $this->view->recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(4);
            $this->view->topPopularOffers = Offer::getTopOffers(5);
            $this->view->userDetails = User::getUserDetails($articleDetails[0]['authorid']);
            $this->viewHelperObject->getMetaTags(
                $this,
                $articleDetails[0]['title'],
                trim($articleDetails[0]['metatitle']),
                trim($articleDetails[0]['metadescription']),
                $articleDetails[0]['permalink'],
                FACEBOOK_IMAGE,
                ''
            );
            $this->view->pageCssClass = 'in-savings-page author-page  home-page';
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }
}
