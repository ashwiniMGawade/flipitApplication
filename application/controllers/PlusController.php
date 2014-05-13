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
        $recentlyAddedArticles = MoneySaving::getRecentlyAddedArticles();
        
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

    public function guidedetailAction()
    {
        $permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
        $parameters = $this->_getAllParams();
        $permalink = $parameters['permalink'];
        $currentArticleView = Articles::getArticleByPermalink($permalink);
        $getAllArticles = Articles::getAllArticles();
        $ArNew =  array();
        $userInformationObject = new User();
        for ($i= 0; $i<count($getAllArticles); $i++) {

            $autherImage  = $userInformationObject->getProfileImage($getAllArticles[$i]['authorid']);
            $image = HTTP_PATH.'public/' .$autherImage['profileimage']['path'] .'thum_medium_'. $autherImage['profileimage']['name'];


            $ArNew[$i]['id']  = $getAllArticles[$i]['id'];
            $ArNew[$i]['title']  = $getAllArticles[$i]['title'];
            $ArNew[$i]['permalink']  = $getAllArticles[$i]['permalink'];
            $ArNew[$i]['chapters']  = $getAllArticles[$i]['chapters'];


            if(! $getAllArticles[$i]['thumbnail']) {
                $thumbnailSmall = $getAllArticles[$i]['ArtIcon'];
            } else {

                $thumbnailSmall = $getAllArticles[$i]['thumbnail'];
            }
            $ArNew[$i]['articleImageName']  = $thumbnailSmall['name'];
            $ArNew[$i]['articleImagePath']  = $thumbnailSmall['path'];
            $ArNew[$i]['authorId']  = $getAllArticles[$i]['authorid'];
            $ArNew[$i]['authorImage']  = $image;

        }

        Zend_Session::start();
        $guideDetail = new Zend_Session_Namespace('nextPrevious');
        for($key=0; $key < count($ArNew); $key++) {
            if ($parameters['permalink'] == $ArNew[$key]['permalink']) {
                    $despN = '';
                    $despP = '';
                    if(array_key_exists($key+1,$ArNew)){
                            $imgNext = PUBLIC_PATH_CDN.$ArNew[$key+1]['articleImagePath'] .'thum_article_medium_'.  $ArNew[$key+1]['articleImageName'];

                        $this->view->nextImage =  @$imgNext;
                        $this->view->nextLink = $ArNew[$key+1]['permalink'];
                        $this->view->nextTitle = $ArNew[$key+1]['title'];

                        if(isset($ArNew[$key+1]['chapters'][0])) {
                            if($ArNew[$key+1]['chapters'][0]['content']!=null  ||$ArNew[$key+1]['chapters'][0]['content']!=''){

                                $despN = $ArNew[$key+1]['chapters'][0]['content'];
                            }
                        }
                    }else{
                        $firstElement = reset($ArNew);
                            $imgNext = PUBLIC_PATH_CDN.$firstElement['articleImagePath'] .'thum_article_medium_'.  $firstElement['articleImageName'];

                        $this->view->nextImage =  @$imgNext;
                        $this->view->nextLink = $firstElement['permalink'];
                        $this->view->nextTitle = $firstElement['title'];
                        if(isset($firstElement['chapters'][0])) {
                            if($firstElement['chapters'][0]['content']!=null  || $firstElement['chapters'][0]['content']!=''){

                                $despN = $firstElement['chapters'][0]['content'];
                            }
                        }
                    }
                    $this->view->nextDesc = $despN;

                    if(array_key_exists($key-1,$ArNew)){
                            $imgPrev = PUBLIC_PATH_CDN. $ArNew[$key-1]['articleImagePath'] .'thum_article_medium_'.  $ArNew[$key-1]['articleImageName'];

                        $this->view->prevImage = @$imgPrev;
                        $this->view->prevLink = $ArNew[$key-1]['permalink'];
                        $this->view->prevTitle = $ArNew[$key-1]['title'];

                        if(isset($ArNew[$key-1]['chapters'][0])) {
                            if($ArNew[$key-1]['chapters'][0]['content']!=null  ||$ArNew[$key-1]['chapters'][0]['content']!=''){

                                $despP = $ArNew[$key-1]['chapters'][0]['content'];
                            }
                        }
                     } else{
                         $lastElement = end($ArNew);
                             $imgPrev = PUBLIC_PATH_CDN.$lastElement['articleImagePath'] .'thum_article_medium_'.  $lastElement['articleImageName'];


                         $this->view->prevImage = @$imgPrev;
                         $this->view->prevLink = $lastElement['permalink'];
                         $this->view->prevTitle = $lastElement['title'];

                         if(isset($lastElement['chapters'][0])) {
                             if($lastElement['chapters'][0]['content']!=null  ||$lastElement['chapters'][0]['content']!=''){

                                 $despP = $lastElement['chapters'][0]['content'];
                             }
                         }
                    }
                    $this->view->prevDesc = $despP;
                }
            }

        if (!empty($currentArticleView)) {
            $this->view->currentArticle = $currentArticleView[0];
            $this->view->headTitle(trim($currentArticleView[0]['metatitle']));
            $this->view->headMeta()->setName('description', trim($currentArticleView[0]['metadescription']));

            $mostReadArticleKey ="all_mostreadMsArticlePage_list";
            $ArticlesExistOrNot =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($mostReadArticleKey);

            if ($ArticlesExistOrNot) {
                $mostReadArticles = MoneySaving::getMostReadArticles($permalink, 3);
                FrontEnd_Helper_viewHelper::setInCache($mostReadArticleKey, $mostReadArticles);
            } else {
                $mostReadArticles = FrontEnd_Helper_viewHelper::getFromCacheByKey($mostReadArticleKey);
            }
            $this->view->mostReadArticles = $mostReadArticles;
            $this->view->canonical = FrontEnd_Helper_viewHelper::generateCononical($permalink) ;
            $this->view->facebookDescription = trim($currentArticleView[0]['metadescription']);
            $this->view->facebookLocale = FACEBOOK_LOCALE;
            $this->view->facebookTitle = $currentArticleView[0]['title'];
            $this->view->facebookShareUrl = HTTP_PATH_LOCALE.$currentArticleView[0]['permalink'];
            $this->view->facebookImage = FACEBOOK_IMAGE;
            $this->view->twitterDescription = trim($currentArticleView[0]['metadescription']);

            $this->view->userDetails =  $userInformationObject->getProfileImage($currentArticleView[0]['authorid']);
        } else {
              throw new Zend_Controller_Action_Exception('', 404);
        }
    }


}
