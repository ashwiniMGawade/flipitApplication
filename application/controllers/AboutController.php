<?php
class AboutController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action  = strtolower($this->getRequest()->getActionName());
        if (file_exists(APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
    }

    public function indexAction()
    {
        $pageAttributeId = PageAttribute::getPageAttributeIdByName($this->getRequest()->getControllerName());
        $pageDetails = Page::getPageFromPageAttribute($pageAttributeId);

        if ($pageDetails->customHeader) {
            $this->view->layout()->customHeader = "\n" . $pageDetails->customHeader;
        }

        $this->view->headMeta()->setName('description', trim($pageDetails->metaDescription));
        $this->view->pageTitle = $pageDetails->pageTitle;
        $this->view->headTitle($pageDetails->metaTitle);
        $this->view->facebookImage = FACEBOOK_IMAGE ;
        $this->view->facebookTitle = $pageDetails->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('redactie');
        $this->view->facebookDescription = trim($pageDetails->metaDescription);
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->twitterDescription = trim($pageDetails->metaDescription);
        $allAuthorsDetails = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_about_pages_users_list", User::getAllUsersDetails(self::getWebsiteNameWithLocale()));
        $this->view->authorsWithPagination = FrontEnd_Helper_viewHelper::renderPagination($allAuthorsDetails, $this->_getAllParams(), 20, 7);
        $this->view->pageDetails = $pageDetails;
 
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public static function getWebsiteNameWithLocale()
    {
        $splitWebsiteName = explode("//", HTTP_PATH_LOCALE);
        $webSiteNameWithoutRightSlash = rtrim($splitWebsiteName[1], '/');
        return strstr($webSiteNameWithoutRightSlash, "www") ? "http://".$webSiteNameWithoutRightSlash : "http://www.".$webSiteNameWithoutRightSlash;
    }

    public function profileAction()
    {
        $authorSlugName = $this->getRequest()->getParam('slug');
        $authorId = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "users". str_replace('-', '_', $authorSlugName) ."_list", User::getUserIdBySlugName($authorSlugName), 0);
        $authorDetails = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "users".$authorId ."_list", User::getUserProfileDetails($authorId), 0);

        if (empty($authorDetails)) {
            throw new Zend_Controller_Action_Exception('', 404);
        }

        $authorDetails = $authorDetails[0];
        $authorFavouriteShops = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "favouriteshop".$authorId ."_list", User::getUserFavouriteStores($authorId), 0);
        $authorMostReadArticles = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "mostread".$authorId ."_list", MoneySaving::getMostReadArticles(6, $authorId), 0);
        $authorFullName = $authorDetails['firstName']." ". $authorDetails['lastName'];
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');

        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink);
        $this->view->headTitle($authorFullName);
        $this->view->facebookTitle = $authorFullName;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link("redactie") ."/".$authorDetails['slug'];
        $this->view->facebookImage = FACEBOOK_IMAGE ;
        $this->view->facebookDescription = trim($authorDetails['mainText']);
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->twitterDescription = trim($authorDetails['mainText']);
        $this->view->authorDetails = $authorDetails;
        $this->view->authorFavouriteShops = $authorFavouriteShops;
        $this->view->authorMostReadArticles = $authorMostReadArticles;

        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }
}
