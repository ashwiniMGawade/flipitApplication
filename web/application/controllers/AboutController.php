<?php
class AboutController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . '.phtml'
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->viewHelperObject = new \FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        $pageDetails = \KC\Repository\Page::getPageDetailsFromUrl(\FrontEnd_Helper_viewHelper::getPagePermalink());
        $pageDetails = (object) $pageDetails;
        $this->view->pageHeaderImage = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'page_header'.$pageDetails->id.'_image',
            array(
                'function' => '\KC\Repository\Logo::getPageLogo',
                'parameters' => array($pageDetails->pageHeaderImageId['id'])
            )
        );
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            \FrontEnd_Helper_viewHelper::__link('link_redactie'),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $allAuthorsDetails = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_users_list',
            array(
                'function' => '\KC\Repository\User::getAllUsersDetails',
                'parameters' => array($this->_helper->About->getWebsiteNameWithLocale())
            ),
            ''
        );
        $this->view->authorsWithPagination = \FrontEnd_Helper_viewHelper::renderPagination(
            $allAuthorsDetails,
            $this->_getAllParams(),
            20,
            9
        );
        
        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'authors-page';

    }

    public function profileAction()
    {

        $authorSlugName = $this->getRequest()->getParam('slug');
        $authorId = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'user_'. str_replace('-', '_', $authorSlugName) .'_data',
            array('function' => '\KC\Repository\User::getUserIdBySlugName', 'parameters' => array($authorSlugName)),
            0
        );
        $authorDetailsForView = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'user_'.$authorId .'_data',
            array(
                'function' => '\KC\Repository\User::getUserProfileDetails',
                'parameters' => array($authorId, $this->_helper->About->getWebsiteNameWithLocale())
            ),
            0
        );
        if (empty($authorDetailsForView)) {
            $this->_helper->redirector->setCode(301);
            $this->_redirect(HTTP_PATH_LOCALE);
        }
        $authorDetails = $authorDetailsForView[0];
        $authorFavouriteShops = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'user_'. 'favouriteShop'.$authorId .'_data',
            array('function' => '\KC\Repository\User::getUserFavouriteStores', 'parameters' => array($authorId)),
            0
        );
        $authorMostReadArticles = \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'user_'. 'mostRead'.$authorId .'_data',
            array('function' => '\KC\Repository\MoneySaving::getMostReadArticles', 'parameters' => array(4, $authorId)),
            0
        );
        $authorFullName = $authorDetails['firstName'].' '. $authorDetails['lastName'];
        $permalink = ltrim(\Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = \FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $authorFullName,
            '',
            trim($authorDetails['mainText']),
            \FrontEnd_Helper_viewHelper::__link('link_redactie') .'/'.$authorDetails['slug'],
            FACEBOOK_IMAGE,
            $customHeader
        );

        $cacheKey = \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($authorSlugName);
        $this->view->discussionComments =
            \FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
                'get_'.$cacheKey.'_disqusComments',
                array(
                    'function' => '\KC\Repository\DisqusComments::getPageUrlBasedDisqusComments',
                    'parameters' => array($authorSlugName)
                ),
                ''
            );
        $this->view->authorDetails = $authorDetailsForView;
        $this->view->authorFavouriteShops = $authorFavouriteShops;
        $this->view->authorMostReadArticles = $authorMostReadArticles;

        $signUpFormSidebarWidget = \FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        \FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'author-page';
    }

    public function loadCookieBarAction()
    {
        $this->_helper->layout()->disableLayout();
    }
}
