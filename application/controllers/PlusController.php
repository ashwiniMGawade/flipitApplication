<?php
/**
 * this class is used for Money Saving Guides (Bespaar Wijzers)
 * get value from database and display on home page
 *
 * @author Raman pending by Romy
 *
 */
class PlusController extends Zend_Controller_Action
{
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
        } else {

            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }
################ Refactored Starts #######################################
    public function indexAction()
    {
        $cannonicalPermalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $moneySavingPagePermalink = FrontEnd_Helper_viewHelper::__link('plus');
        $moneySavingPageDetails  =  FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_moneysavingpage".$moneySavingPagePermalink."_list", MoneySaving::getPageDetails($moneySavingPagePermalink));
        $mostReadArticles = FrontEnd_Helper_viewHelper::getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(3);
        
        $this->view->facebookDescription = trim(isset($moneySavingPageDetails[0]['metaDescription']) ? $moneySavingPageDetails[0]['metaDescription'] :'');
        $this->view->facebookLocale = FACEBOOK_LOCALE;
        $this->view->facebookTitle = isset($moneySavingPageDetails[0]['pageTitle']) ? $moneySavingPageDetails[0]['pageTitle'] :'';
        $this->view->facebookShareUrl = $moneySavingPagePermalink;
        $this->view->facebookImage = HTTP_PATH."public/images/plus_og.png";
        $this->view->twitterDescription = trim(isset($moneySavingPageDetails[0]['metaDescription']) ? $moneySavingPageDetails[0]['metaDescription'] :'');
        
        $this->view->pageTitle = isset($moneySavingPageDetails[0]['pageTitle']) ? $moneySavingPageDetails[0]['pageTitle'] :'';
        $this->view->permaLink = $moneySavingPagePermalink;
        $this->view->headTitle(trim(isset($moneySavingPageDetails[0]['metaTitle']) ? $moneySavingPageDetails[0]['metaTitle'] :''));
        $this->view->headMeta()->setName('description', trim(isset($moneySavingPageDetails[0]['metaDescription']) ? $moneySavingPageDetails[0]['metaDescription'] :''));
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($cannonicalPermalink);
        if(isset($moneySavingPageDetails[0]['customHeader'])) {
            $this->view->layout()->customHeader = "\n" . isset($moneySavingPageDetails[0]['customHeader']) ? $moneySavingPageDetails[0]['customHeader'] : '';
        }

        $this->view->mostReadArticles = $mostReadArticles;
        $this->view->categoryWiseArticles = $categoryWiseArticles;
        $this->view->recentlyAddedArticles = $recentlyAddedArticles;
       
        if (!empty($moneySavingPageDetails)) {
            $this->view->pageDetails = $moneySavingPageDetails;
        } else {
            $error404 = 'HTTP/1.1 404 Not Found';
            $this->getResponse()->setRawHeader($error404);
        }
    }

    public function guidedetailAction()
    {
        $parameters = $this->_getAllParams();
        $permalink = $parameters['permalink'];
        $currentArticleDetails = Articles::getArticleByPermalink($permalink);
        $currentArticleCategory = $currentArticleDetails[0]['relatedcategory'][0]['articlecategory']['name'];
        $categoryWiseArticles = MoneySaving::getCategoryWiseArticles();
        $articlesRelatedToCurrentCategory = $categoryWiseArticles[$currentArticleCategory]; 
        $allArticles = Articles::getAllArticles();
        $userInformationObject = new User();
        
        if (!empty($currentArticleDetails)) {
            $this->view->headTitle(trim($currentArticleDetails[0]['metatitle']));
            $this->view->headMeta()->setName('description', trim($currentArticleDetails[0]['metadescription']));
            $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink) ;
            $this->view->facebookDescription = trim($currentArticleDetails[0]['metadescription']);
            $this->view->facebookLocale = FACEBOOK_LOCALE;
            $this->view->facebookTitle = $currentArticleDetails[0]['title'];
            $this->view->facebookShareUrl = HTTP_PATH_LOCALE.$currentArticleDetails[0]['permalink'];
            $this->view->facebookImage = FACEBOOK_IMAGE;
            $this->view->twitterDescription = trim($currentArticleDetails[0]['metadescription']);
            $this->view->mostReadArticles = FrontEnd_Helper_viewHelper::
                getRequestedDataBySetGetCache("all_mostreadMsArticlePage_list", MoneySaving::getMostReadArticles(3));
            $this->view->currentArticle = $currentArticleDetails[0];
            $this->view->articlesRelatedToCurrentCategory = $articlesRelatedToCurrentCategory;
            $this->view->recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles(4);
            $this->view->topPopularOffers = Offer::getTopOffers(5);
            $this->view->userDetails =  $userInformationObject->getUseretails($currentArticleDetails[0]['authorid']);
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }

################ Refactored Ends #######################################
    public function categoryAction()
    {
        
        // get top categories (for now i just fetch the available category list)
        $perma = $this->getRequest ()->getParam ('permalink');
        $permalinkForCanonical = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        //sdie($perma);
        $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalinkForCanonical) ;
        $this->pageDetail = Articlecategory::getArticleCategory($perma);
        //print_r($this->pageDetail); die;
        $id = $this->pageDetail[0]['id'];
        $this->view->permaLink = @$this->pageDetail[0]['permalink'];
        $this->view->headTitle(@trim($this->pageDetail[0]['metatitle']));
        $this->view->headMeta()->setName('description', @trim($this->pageDetail[0]['metadescription']));
        $cache = Zend_Registry::get('cache');

        $limit = 10;

        $key ="all_". "category".$id ."_list";
        // no cache available, lets query..
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($key);
        //key not exist in cache
        if($flag){

            //get Page data from database and store in cache
            $category = Articlecategory::getArticleCategory($perma);
            FrontEnd_Helper_viewHelper::setInCache($key, $category);
            //echo  'FROM DATABASE';

        } else {
            //get from cache
            $category = FrontEnd_Helper_viewHelper::getFromCacheByKey($key);
            //echo 'The result is comming from cache!!';
        }
        if(empty($category)){

            throw new Zend_Controller_Action_Exception('', 404);
        }

        $mostreaddkey ="all_". "mostreadarticle".$id ."_list";
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($mostreaddkey);
        if($flag){

            //get Page data from database and store in cache
            $mostreadArtOfCategory = MoneySaving::generateMostReadArticleOfcategory($id);
            FrontEnd_Helper_viewHelper::setInCache($mostreaddkey, $mostreadArtOfCategory);
            //echo  'FROM DATABASE';

        } else {
            //get from cache
            $mostreadArtOfCategory = FrontEnd_Helper_viewHelper::getFromCacheByKey($mostreaddkey);
            //echo 'The result is comming from cache!!';
        }

        $relatedcategorykey ="all_". "relatedcategory".$id ."_list";
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($relatedcategorykey);
        if($flag){

            //get Page data from database and store in cache
            $relatedCategory = MoneySaving::generateRelatedCategory($id);
            FrontEnd_Helper_viewHelper::setInCache($relatedcategorykey, $relatedCategory);
            //echo  'FROM DATABASE';

        } else {
            //get from cache
            $relatedCategory = FrontEnd_Helper_viewHelper::getFromCacheByKey($relatedcategorykey);
            //echo 'The result is comming from cache!!';
        }

        $articlesofcategorykey ="all_". "articlesofcategory".$id ."_list";
        $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($articlesofcategorykey);
        if($flag){

            //get Page data from database and store in cache
            $allMoneySavingArticles = MoneySaving::generateAllMoneySavingArticlesOfcategory($id);
            FrontEnd_Helper_viewHelper::setInCache($articlesofcategorykey, $allMoneySavingArticles);
            //echo  'FROM DATABASE';

        } else {
            //get from cache
            $allMoneySavingArticles = FrontEnd_Helper_viewHelper::getFromCacheByKey($articlesofcategorykey);
            //echo 'The result is comming from cache!!';
        }

        //for facebook parameters
        $this->view->fbtitle = @$this->pageDetail[0]['name'];
        $this->view->fbshareUrl = HTTP_PATH_LOCALE.$this->pageDetail[0]['permalink'];;

        $this->view->fbImg = HTTP_PATH."public/images/plus_og.png";

        $this->view->category = $category;
        $this->view->mostReadArtOfcategory = $mostreadArtOfCategory;
        $this->view->allMoneySavingArticles = $allMoneySavingArticles;
        $this->view->relatedCategory = $relatedCategory;
        $this->view->pageDetail = $this->pageDetail;
    }

    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        //$cache->remove('top_categories_list');
        //$cache->remove('top_categories_output');
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

        $cnt  = intval($u->registerUser($this->_getParam('email')));
        if($cnt > 0) {
            echo Zend_Json::encode(false);

        } else {

            echo Zend_Json::encode(true);
        }

        die();
    }




}
