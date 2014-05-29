<?php
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (file_exists(APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/' . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
        $this->view->banner = Signupmaxaccount::getHomepageImages();
        $this->viewHelperObject = new FrontEnd_Helper_viewHelper();
    }

    public function indexAction()
    {
        if (HTTP_HOST != 'www.flipit.com') {
            $this->view->canonical = '';
            $this->view->controllerName = $this->getRequest()->getControllerName();
            $this->view->action = $this->getRequest()->getActionName();
            $pageAttributeId = Page::getPageAttributeByPermalink($this->getRequest()->getActionName());
            $pageDetails = Page::getPageFromPageAttribute($pageAttributeId);
            if (!empty($pageDetails)) {
                $this->view->pageTitle = ucfirst($pageDetails->pageTitle);
                $customHeader = isset($pageDetails->customHeader) ? $pageDetails->customHeader : '';
                $this->viewHelperObject->getFacebookMetaTags($this, $pageDetails->metaTitle, ucfirst(trim($pageDetails->metaTitle)), trim($pageDetails->metaDescription), FrontEnd_Helper_viewHelper::__link($this->getRequest()->getActionName()), FACEBOOK_IMAGE, $customHeader);
            } else {
                throw new Zend_Controller_Action_Exception('', 404);
            }
            $this->view->topOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_popularvaouchercode_list", Offer::getTopOffers(10));
            $this->view->newOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_homenewoffer_list", Offer::getNewestOffers('newest', 10));
            $topCategories = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_popularcategory_list", Category::getPopularCategories(10));
            $this->view->topCategories = $topCategories;
            $topCategoriesOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_hometocategoryoffers_list", Category::getCategoryVoucherCodes(self::getTopCategoriesIds($topCategories), 0, 'home'));
            $this->view->topCategoriesOffers = self::getCategoriesOffers($topCategoriesOffers);
            $specialListPages = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_speciallist_list", SpecialList::getSpecialPages());
            $this->view->specialListPages = $specialListPages;
            $this->view->specialPagesOffers = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_speciallist_count", self::getSpecialListPagesOffers($specialListPages));
            $this->view->moneySavingGuides = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_homemanisaving_list", Articles::getMoneySavingArticles());
            $this->view->topStores = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_popularshopForHomePage_list", FrontEnd_Helper_viewHelper::getStoreForFrontEnd("popular", 24));
            $this->view->seeninContents = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_homeseenin_list", SeenIn::getSeenInContent(10));
            $this->view->aboutTabs = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_about_page", About::getAboutContent(1));
            $this->view->pageCssClass = 'home-page';
        }
    }

    public static function getTopCategoriesIds($topCategories)
    {
        $categoriesIds = '';
        foreach ($topCategories as $topCategory) {
            $categoriesIds[] = $topCategory['categoryId'];
        }

        return $categoriesIds;
    }

    public static function getCategoriesOffers($topCategoriesOffers)
    {
        $topCategoriesOffersWithCategoriesPermalinkIndex = '';
        foreach ($topCategoriesOffers as $topCategoriesOffer) {
            $topCategoriesOffersWithCategoriesPermalinkIndex[$topCategoriesOffer['categoryPermalink']][] = $topCategoriesOffer['Offer'];
        }

        return $topCategoriesOffersWithCategoriesPermalinkIndex;
    }

    public function getSpecialListPagesOffers($specialListPages)
    {
        $specialOfferslist = '';
        foreach ($specialListPages as $specialListPage) {
            foreach ($specialListPage['page'] as $page) {
                $specialOfferslist[$page['permaLink']] = Offer::getSpecialPageOffers($page);
            }
        }

        return $specialOfferslist;
    }
}
