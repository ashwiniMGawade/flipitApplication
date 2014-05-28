<?php
class CategoryController extends Zend_Controller_Action
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################
    /**
     * Function show.
     *
     * get offer related to category.
     */
    public function showAction()
    {
        $categoryPermalink = $this->getRequest()->getParam('permalink');
        $categoryDetail = Category::getCategoryDetails($categoryPermalink);
        if (count($categoryDetail) > 0) {
            $categoryVoucherCodes = Category::getCategoryVoucherCodes($categoryDetail[0]['id'], 71);
            $offersWithPagination = FrontEnd_Helper_viewHelper::renderPagination($categoryVoucherCodes, $this->_getAllParams(), 27, 3);
            $this->view->offersWithPagination = $offersWithPagination;
            $this->view->categoryDetail = $categoryDetail;
            $this->view->offersType = 'offerWithPagenation';
            $this->view->headTitle(trim($categoryDetail[0]['metatitle']));
            $this->view->headMeta()->setName('description', trim($categoryDetail[0]['metaDescription']));

            if (LOCALE == '') {
                $facebookImage = 'logo_og.png';
                $facebookLocale = LOCALE;
            } else {
                $facebookImage = 'flipit.png';
                $facebookLocale = LOCALE;
            }

            $this->view->facebookTitle = $categoryDetail[0]['name'];
            $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen') . '/' .$categoryDetail[0]['permaLink'];
            $this->view->facebookImage = HTTP_PATH."public/images/" .$facebookImage ;
            $this->view->facebookDescription = trim($categoryDetail[0]['metaDescription']);
            $this->view->facebookLocale = $facebookLocale;
            $this->view->twitterDescription = trim($categoryDetail[0]['metaDescription']);
        } else {
            throw new Zend_Controller_Action_Exception('', 404);
        }
        $signUpFormLarge = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignupForm', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormLarge, $signUpFormSidebarWidget);
        $this->view->form = $signUpFormLarge;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public function indexAction()
    {
        $categoryPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($categoryPermalink) ;
        $this->pageDetail = Page::getPageFromPageAttribute(9);
        $this->view->headTitle($this->pageDetail->metaTitle);
        $this->view->headMeta()->setName('description', trim($this->pageDetail->metaDescription));

        if ($this->pageDetail->customHeader) {
            $this->view->layout()->customHeader = "\n" . $this->pageDetail->customHeader;
        }

        $allCategories = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_category_list', Category::getCategoriesDetail());
        $specialPagesList = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache('all_categoryspeciallist_list', Page::getSpecialListPages());
        $this->view->categoriesWithSpecialPagesList = array_merge($allCategories, $specialPagesList);
        $this->view->facebookTitle = $this->pageDetail->pageTitle;
        $this->view->facebookShareUrl = HTTP_PATH_LOCALE . FrontEnd_Helper_viewHelper::__link('categorieen');
        $this->view->facebookImage = FACEBOOK_IMAGE;
        $this->view->facebookDescription = trim($this->pageDetail->metaDescription);
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->twitterDescription = trim($this->pageDetail->metaDescription);
        $largeSignUpForm = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignUpForm', 'SignUp');
        $signUpFormSidebarWidget = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $largeSignUpForm, $signUpFormSidebarWidget);
        $this->view->form = $largeSignUpForm;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
        $this->view->pageCssClass = 'all-categories-alt-page';
    }
    #####################################################
    ############# END REFACORED CODE ####################
    #####################################################
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
}
