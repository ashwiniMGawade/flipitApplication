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
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        $pageAttributeId = PageAttribute::getPageAttributeIdByName($this->getRequest()->getControllerName());
        $pageDetails = Page::getPageDetails($pageAttributeId);
        $this->view->pageHeaderImage = Logo::getPageLogo($pageDetails->pageHeaderImageId);
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            FrontEnd_Helper_viewHelper::__link('link_redactie'),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $this->view->pageTitle = isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '';
        $allAuthorsDetails = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_about_pages_users_list',
            array(
                'function' => 'User::getAllUsersDetails',
                'parameters' => array($this->_helper->About->getWebsiteNameWithLocale())
            )
        );
        $this->view->authorsWithPagination = FrontEnd_Helper_viewHelper::renderPagination(
            $allAuthorsDetails,
            $this->_getAllParams(),
            20,
            7
        );
        
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'authors-page home-page';

    }

    public function profileAction()
    {
        $authorSlugName = $this->getRequest()->getParam('slug');
        $authorId = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_'. 'users'. str_replace('-', '_', $authorSlugName) .'_list',
            array('function' => 'User::getUserIdBySlugName', 'parameters' => array($authorSlugName)),
            0
        );
        $authorDetails = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_'. 'users'.$authorId .'_list',
            array('function' => 'User::getUserProfileDetails', 'parameters' => array($authorId)),
            0
        );

        if (empty($authorDetails)) {
            throw new Zend_Controller_Action_Exception('', 404);
        }

        $authorDetails = $authorDetails[0];
        $authorFavouriteShops = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_'. 'favouriteshop'.$authorId .'_list',
            array('function' => 'User::getUserFavouriteStores', 'parameters' => array($authorId)),
            0
        );
        $authorMostReadArticles = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache(
            'all_'. 'mostread'.$authorId .'_list',
            array('function' => 'MoneySaving::getMostReadArticles', 'parameters' => array(6, $authorId)),
            0
        );
        $authorFullName = $authorDetails['firstName'].' '. $authorDetails['lastName'];
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $customHeader = '';
        $this->viewHelperObject->getMetaTags(
            $this,
            $authorFullName,
            '',
            trim($authorDetails['mainText']),
            FrontEnd_Helper_viewHelper::__link('link_redactie') .'/'.$authorDetails['slug'],
            FACEBOOK_IMAGE,
            $customHeader
        );
        $this->view->authorDetails = $authorDetails;
        $this->view->authorFavouriteShops = $authorFavouriteShops;
        $this->view->authorMostReadArticles = $authorMostReadArticles;

        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp(
            'formSignupSidebarWidget',
            'SignUp '
        );
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'author-page home-page';
    }
}
