<?php
class CategoryController extends Zend_Controller_Action
{
    public function init()
    {
        $module     = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }
    public function showAction()
    {
        $categoryPermalink = $this->getRequest()->getParam('permalink');
        $positionOfSpecialCharactetr = strpos($categoryPermalink, "-");
        if ($positionOfSpecialCharactetr) {
            $stringWithoutSpecilaChracter = str_replace("-", "", $categoryPermalink);
            $cacheKey = $stringWithoutSpecilaChracter;
        } else {
            $cacheKey = $categoryPermalink;
        }

        $categoryDetails = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'category_'.$cacheKey.'_data',
                array(
                    'function' => 'Category::getCategoryDetails', 'parameters' => array($categoryPermalink)
                )
            );

        if (count($categoryDetails) > 0) {
            $categoryVoucherCodes = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'category_'.$cacheKey.'_voucherCodes',
                array(
                    'function' => 'Category::getCategoryVoucherCodes',
                    'parameters' => array($categoryDetails[0]['id'], 71)
                )
            );
            $offersWithPagination = FrontEnd_Helper_viewHelper::renderPagination(
                $categoryVoucherCodes,
                $this->_getAllParams(),
                20,
                9
            );
            $this->view->offersWithPagination = $offersWithPagination;
            $this->view->categoryDetails = $categoryDetails;
            $this->view->offersType = 'offerWithPagenation';
            $customHeader = '';
            $this->viewHelperObject->getMetaTags(
                $this,
                $categoryDetails[0]['name'],
                trim($categoryDetails[0]['metatitle']),
                trim($categoryDetails[0]['metaDescription']),
                FrontEnd_Helper_viewHelper::__link('link_categorieen') . '/' .$categoryDetails[0]['permaLink'],
                FACEBOOK_IMAGE,
                $customHeader
            );

        } else {
            throw new Zend_Controller_Action_Exception('', 404);
        }
        $signUpFormLarge = FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('largeSignupForm', 'SignUp');
        $signUpFormSidebarWidget =
           FrontEnd_Helper_SignUpPartialFunction::createFormForSignUp('formSignupSidebarWidget', 'SignUp ');
        FrontEnd_Helper_SignUpPartialFunction::validateZendForm($this, $signUpFormLarge, $signUpFormSidebarWidget);
        $this->view->form = $signUpFormLarge;
        $this->view->sidebarWidgetForm = $signUpFormSidebarWidget;
    }

    public function indexAction()
    {
        $categoryPermalink = FrontEnd_Helper_viewHelper::getPagePermalink();
        $categoryPermalink = explode('?', $categoryPermalink);
        $categoryPermalink = isset($categoryPermalink[0]) ? $categoryPermalink[0] : '';
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($categoryPermalink);
        $pageDetails = Page::getPageDetailsFromUrl($categoryPermalink);
        $this->viewHelperObject->getMetaTags(
            $this,
            isset($pageDetails->pageTitle) ? $pageDetails->pageTitle : '',
            isset($pageDetails->metaTitle) ? $pageDetails->metaTitle : '',
            isset($pageDetails->metaDescription) ? $pageDetails->metaDescription : '',
            FrontEnd_Helper_viewHelper::__link('link_categorieen'),
            FACEBOOK_IMAGE,
            isset($pageDetails->customHeader) ? $pageDetails->customHeader : ''
        );
        $allCategories = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'all_category_list',
                array(
                    'function' => 'Category::getCategoriesInformation', 'parameters' => array()
                )
            );
        $specialPagesList = FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'all_specialPages_list',
                array(
                    'function' => 'Page::getSpecialListPages', 'parameters' => array()
                )
            );
        $specialPages = $this->_helper->Category->getSpecialPageWithOffersCount($specialPagesList);
        $this->view->categoriesWithSpecialPagesList = array_merge($allCategories, $specialPages);
        $this->view->pageCssClass = 'all-categories-alt-page';
    }
}
