<?php
/**
 * this class is used for about page
 * @author Raman
 */
class AboutController extends Zend_Controller_Action
{
    ##########################################################
    ########### REFACTORED CODE ##############################
    ##########################################################
    public function indexAction()
    {
        $pageAttributeId = PageAttribute::getPageAttributeIdByName($this->getRequest()->getControllerName());
        $pageDetail = Page::getPageFromPageAttribute($pageAttributeId);

        if ($pageDetail->customHeader) {
            $this->view->layout()->customHeader = "\n" . $pageDetail->customHeader;
        }

        $this->view->headMeta()->setName('description', trim($pageDetail->metaDescription));
        $this->view->pageTitle = $pageDetail->pageTitle;
        $this->view->headTitle($pageDetail->metaTitle);
        $this->view->facebookImage = FACEBOOK_IMAGE ;
        $this->view->facebookTitle = $pageDetail->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('redactie');
        $this->view->facebookDescription = trim($pageDetail->metaDescription);
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->twitterDescription = trim($pageDetail->metaDescription);
        $allAuthorsDetail = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_about_pages_users_list", User::getAllUsersDetail(self::getWebsiteName()));
        $this->view->authorsWithPagination = FrontEnd_Helper_viewHelper::renderPagination($allAuthorsDetail, $this->_getAllParams(), 20, 7);
        $this->view->pageDetail = $pageDetail;
 
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, '', $signUpFormSidebarWidget);
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public static function getWebsiteName()
    {
        $splitWebsiteName = explode("//", HTTP_PATH_LOCALE);
        $webSiteNameWithoutRightSlash = rtrim($splitWebsiteName[1], '/');
        return strstr($webSiteNameWithoutRightSlash, "www") ? "http://".$webSiteNameWithoutRightSlash : "http://www.".$webSiteNameWithoutRightSlash;
    }
    ##########################################################
    ########### REFACTORED CODE ##############################
    ##########################################################
    public function profileAction()
    {
        $authorSlugName = $this->getRequest()->getParam('slug');
        $authorId = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "users". str_replace('-', '_', $authorSlugName) ."_list", User::getUserIdBySlugName($authorSlugName), 0);
        $authorDetails = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "users".$authorId ."_list", User::getUserProfileDetails($authorId), 0);

        if (empty($authorDetails)) {
            throw new Zend_Controller_Action_Exception('', 404);
        }

        $authorDetails = $authorDetails[0];
        $authorFavouriteShops = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_". "favouriteshop".$authorId ."_list", User::getUserFavouritesStores($authorId), 0);
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

    /**
     * override views based on modules if exists
     * @see Zend_Controller_Action::init()
     * @author Bhart
     */
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        # check module specific view exists or not
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){

            # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else{

            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }


    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }

    /**
     * Find if an email id exists allready for newsletter
     * @author Raman
     * @version 1.0
     */
    public function checkuserAction()
    {
        $u =  new Newslettersub();
        $cnt  = intval($u->checkDuplicateUser($this->_getParam('email')));

        if($cnt > 0) {
            echo Zend_Json::encode(false);

        } else {

            echo Zend_Json::encode(true);
        }

        die();
    }

    /**
     * Find if an email id exists allready for newsletter
     * @author Raman
     * @version 1.0
     */
    public function registerAction()
    {
        $u =  new Newslettersub();
        //echo $this->_getParam('email');
        //die("Rajajk");
        $cnt  = intval($u->registerUser($this->_getParam('email')));
        if($cnt > 0) {
            echo Zend_Json::encode(false);

        } else {

            echo Zend_Json::encode(true);
        }

        die();
    }
}
