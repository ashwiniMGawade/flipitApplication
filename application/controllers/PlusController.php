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
                'parameters' => array(3)
                ),
                ''
            );

        $categoryWiseArticles = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"all_categoriesArticles_list",
                array('function' =>
                'MoneySaving::getCategoryWiseArticles', 'parameters' => array()
                ),
                ''
            );

        $moneySavingPartialFunctions = new FrontEnd_Helper_MoneySavingGuidesPartialFunctions();
        $allArticlesWithAuthorDetails = $moneySavingPartialFunctions->addAuthorDetailsInArticles($categoryWiseArticles);
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
            ),
            ''
        );
        $this->view->popularStores = $popularStores;
        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->allArticles =  $allArticlesWithAuthorDetails;
        $this->view->pageCssClass = 'article-page';
    }

    public function guidedetailAction()
    {
        $permalink = $this->getRequest()->getParam('permalink');
        $articleObject = new FrontEnd_Helper_MoneySavingGuidesPartialFunctions();
        $positionOfSpecialCharactetr = strpos($permalink, "-");
        if ($positionOfSpecialCharactetr) {
            $stringWithoutSpecilaChracter = str_replace("-", "", $permalink);
            $cacheKey = $stringWithoutSpecilaChracter;

        } else {
            $cacheKey = $permalink;
        }

        $articleDetails = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                (string)"article_".$cacheKey."_details",
                array('function' =>
                'Articles::getArticleByPermalink', 'parameters' => array($permalink))
            );

        if (!empty($articleDetails)) {
            $currentArticleCategory = !empty($articleDetails[0]['relatedcategory'][0]['articlecategory'])
                                      ? $articleDetails[0]['relatedcategory'][0]['articlecategory']['name'] : '';
            $categoryWiseArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache(
                    (string)"4_categoriesArticles_list",
                    array('function' =>
                    'MoneySaving::getCategoryWiseArticles', 'parameters' => array(5))
                );

            $articlesRelatedToCurrentCategory =
                !empty($categoryWiseArticles[$currentArticleCategory])
                ? $articleObject->excludeSelectedArticle(
                    $categoryWiseArticles[$currentArticleCategory],
                    $articleDetails[0]['id']
                )
                : '';
            $incrementArticleViewCountValue  = FrontEnd_Helper_viewHelper::
                viewCounter('article', 'onload', $articleDetails[0]['id']);
                $this->view->canonical =
                    FrontEnd_Helper_viewHelper::generateCononical(
                        $this->getRequest()->getControllerName() .'/'. $permalink
                    );
            $this->view->mostReadArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", array(
                    'function' => 'MoneySaving::getMostReadArticles', 'parameters' => array(3)));
            $this->view->articleDetails = $articleDetails[0];
            $this->view->articlesRelatedToCurrentCategory = $articlesRelatedToCurrentCategory;
            $this->view->recentlyAddedArticles =  FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("2_recentlyAddedArticles_list", array('function' =>
                'MoneySaving::getRecentlyAddedArticles', 'parameters' => array($articleDetails[0]['id'], 3)));
            $this->view->topPopularOffers = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache("5_topOffers_list", array('function' =>
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
            $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($permalink);
            $this->view->discussionComments =
                FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                    'get_'.$cacheKey.'_disqusComments',
                    array(
                        'function' => 'DisqusComments::getPageUrlBasedDisqusComments',
                        'parameters' => array($permalink)
                    ),
                    ''
                );
            $this->view->pageCssClass = 'in-savings-page author-page';
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }
}
