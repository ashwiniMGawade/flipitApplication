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
        $articlesOverviewPagePermalink  = FrontEnd_Helper_viewHelper::getPagePermalink();
        $pageDetails = Page::getPageDetailsFromUrl($articlesOverviewPagePermalink);

        $mostReadArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"all_mostreadMsArticlePage_list",
                array('function' =>
                'MoneySaving::getMostReadArticles',
                'parameters' => array(3))
            );

        $categoryWiseArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"all_categoriesArticles_list",
                array('function' =>
                'MoneySaving::getCategoryWiseArticles', 'parameters' => array())
            );

        $moneySavingPartialFunctions = new FrontEnd_Helper_MoneySavingGuidesPartialFunctions();
        $allArticlesWithAuthorDetails = $moneySavingPartialFunctions->addAuthorDetailsInArticles($categoryWiseArticles);

        $recentlyAddedArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"2_recentlyAddedArticles_list",
                array('function' =>
                'MoneySaving::getRecentlyAddedArticles', 'parameters' => array(2))
            );

        $popularStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            (string)'7_popularShops_list',
            array('function' => 'Shop::getAllPopularStores', 'parameters' => array(7)),
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
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $this->view->pageHeaderImage = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'page_header'.$pageDetails->id.'_image',
            array(
                'function' => 'Logo::getPageLogo',
                'parameters' => array($pageDetails->pageHeaderImageId)
            )
        );
        $this->view->popularStores = $popularStores;
        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->allArticles =  $allArticlesWithAuthorDetails;
        $this->view->recentlyAddedArticles = $recentlyAddedArticles;
        $this->view->pageCssClass = 'article-page';
    }

    public function guidedetailAction()
    {
        $permalink = $this->getRequest()->getParam('permalink');
        $articleDetails = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"article_".$permalink."_details ",
                array('function' =>
                'Articles::getArticleByPermalink', 'parameters' => array($permalink))
            );
        $currentArticleCategory = $articleDetails[0]['relatedcategory'][0]['articlecategory']['name'];
        $categoryWiseArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"4_categoriesArticles_list",
                array('function' =>
                'MoneySaving::getCategoryWiseArticles', 'parameters' => array(4))
            );
        $articlesRelatedToCurrentCategory =
            !empty($categoryWiseArticles[$currentArticleCategory])
            ? $categoryWiseArticles[$currentArticleCategory]
            : '';
        $incrementArticleViewCountValue  = FrontEnd_Helper_viewHelper::
            viewCounter('article', 'onload', $articleDetails[0]['id']);

        if (!empty($articleDetails)) {
            $this->view->canonical =
                FrontEnd_Helper_viewHelper::generateCononical(
                    $this->getRequest()->getControllerName() .'/'. $this->getRequest()->getParam('permalink')
                );
            $this->view->mostReadArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", array(
                    'function' => 'MoneySaving::getMostReadArticles', 'parameters' => array(3)));
            $this->view->articleDetails = $articleDetails[0];
            $this->view->articlesRelatedToCurrentCategory = $articlesRelatedToCurrentCategory;
            $this->view->recentlyAddedArticles =  FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("2_recentlyAddedArticles_list", array('function' =>
                'MoneySaving::getRecentlyAddedArticles', 'parameters' => array(2)));
            $this->view->topPopularOffers = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("5_topOffers_offers", array('function' =>
                'Offer::getTopOffers', 'parameters' => array(5)));
            $this->view->userDetails = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache('user_'.$articleDetails[0]['authorid'].'_details', array('function' =>
                'User::getUserDetails', 'parameters' => array($articleDetails[0]['authorid'])));
            $articleThumbNailImage = FACEBOOK_IMAGE;
            if (!empty($articleDetails[0]['thumbnail'])) {
                $articleThumbNailImage = PUBLIC_PATH_CDN
                    . $articleDetails[0]['thumbnail']['path']
                    .$articleDetails[0]['thumbnail']['name'];
            }
            $this->viewHelperObject->getMetaTags(
                $this,
                $articleDetails[0]['title'],
                trim($articleDetails[0]['metatitle']),
                trim($articleDetails[0]['metadescription']),
                $this->getRequest()->getControllerName() .'/'. $articleDetails[0]['permalink'],
                $articleThumbNailImage,
                ''
            );
            $this->view->pageCssClass = 'in-savings-page author-page';
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }
}
