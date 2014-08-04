<?php
/**
 * Articlecategory
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Articlecategory extends BaseArticlecategory
{
    ####################### Refactored ##############################

    public static function deleteAllArticleCategoriesAndReferenceArticleCategories()
    {
        $deletedArticleCategory = self:: deleteArticleCategory();
        $deletedReferenceArticleCategory =  self:: deleteReferenceArticleCategory();
        return true;

    }
    public static function deleteArticleCategory()
    {
        $deleteArticleCategory = Doctrine_Query::create()->delete()
                                           ->from('Articlecategory')
                                           ->execute();
    }
    public static function deleteReferenceArticleCategory()
    {
        $deleteReferenceArticleCategory = Doctrine_Query::create()->delete()
                                            ->from('RefArticlecategoryRelatedcategory')
                                            ->execute();
    }
    ####################### Refactored ##############################

    public function addcategory($params)
    {
        $this->name = strtolower(BackEnd_Helper_viewHelper::stripSlashesFromString($params['categoryName']));
        $this->permalink = BackEnd_Helper_viewHelper::stripSlashesFromString($params['permaLink']);
        $this->metatitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['metaTitle']);
        $this->metadescription = BackEnd_Helper_viewHelper::stripSlashesFromString($params['metaDescription']);
        $this->description = BackEnd_Helper_viewHelper::stripSlashesFromString($params['description']);

        if ($_FILES['categoryIconNameHidden']['name']!=null) {
            $result = self::uploadImage('categoryIconNameHidden');
            if ($result['status'] == '200') {
                $categoryImageExtension = BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $this->ArtCatIcon->ext = $categoryImageExtension;
                $this->ArtCatIcon->path = $result['path'];
                $this->ArtCatIcon->name = BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
            } else{
                return false;
            }
        }

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_articlecategory_list');
        $categoryImageExtension = BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
        $this->ArtCatIcon->ext = $categoryImageExtension;
        try {
                $this->save();
                $categoryId = $this->id ;
                foreach ($params['selectedCategoryies'] as $relatedCategory) {
                    $refRelatedCategory = new RefArticlecategoryRelatedcategory();
                    $refRelatedCategory->articlecategoryid = $this->id;
                    $refRelatedCategory->relatedcategoryid = $relatedCategory;
                    $refRelatedCategory->save();
                }
        return $categoryId ;
        } catch(Exception $e) {
            return false;
        }
    }



    /**
     * upload image
     * @param $_FILES[index]  $file
     */
    public function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH))
            mkdir(UPLOAD_IMG_PATH);

        // generate upload path for images related to shop
        $uploadPath = UPLOAD_IMG_PATH . "articlecategory/";
        $adapter = new Zend_File_Transfer_Adapter_Http();

        // generate real path for upload path
        $rootPath = ROOT_PATH . $uploadPath;

        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath))
            mkdir($rootPath, 776, true);

        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $adapter->addValidator('Size', false, array('min' => 20, 'max' => '2MB'));
        // get file name
        $name = $adapter->getFileName($file, false);

        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;

        // generates complete path of image
        $cp = $rootPath . $newName;


        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;

        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName , 132, 95, $path);

        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName , 64, 32, $path);


        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_samll_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName , 60, 45, $path);

        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_medium_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName , 220, 165, $path);


        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_big_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName , 250, 0, $path);

        /**
         *	 generating thumnails for upload logo if file in shop logo
         */
        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;

            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName,
                    200, 150, $path);
        }

        // apply filter to rename file name and set target
        $adapter
        ->addFilter(
                new Zend_Filter_File_Rename(
                        array('target' => $cp, 'overwrite' => true)),
                null, $file);

        // recieve file for upload
        $adapter->receive($file);

        // check is file is valid then

        if ($adapter->isValid($file)) {

            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);

        } else {

            return array("status" => "-1",
                    "msg" => "Please upload the valid file");

        }

    }

    public function getCategoryList($params)
    {
        $srh =  $params["searchText"]=='undefined' ? '' : $params["searchText"];
        $flag = $params['flag'] ;

        $artCatList = $data = Doctrine_Query::create()->select('cat.id,cat.name,cat.permalink,cat.metatitle')
                                                      ->from("Articlecategory cat")
                                                      ->where('cat.deleted = ?' , $flag )
                                                      ->andWhere("cat.name LIKE ?", "$srh%");

        $result = 	DataTable_Helper::generateDataTableResponse($artCatList,
                $params,
                array("__identifier" => 'cat.id','cat.name','cat.permalink','cat.metatitle'),
                array(),
                array());
        return $result;
    }

    public static function getartCategories()
    {

        $artCatList = $data = Doctrine_Query::create()->select('cat.*')
        ->from("Articlecategory cat")
        ->where('cat.deleted = 0');

        $result = 	DataTable_Helper::generateDataTableResponse($artCatList,
                array("__identifier" => 'cat.id','cat.name','cat.permalink','cat.metatitle'),
                array(),
                array());
        return $result;
    }



    public static function searchKeyword($keyword, $flag)
    {
        $data = Doctrine_Query::create()->select('c.name as name')
                ->from("Articlecategory c")->where('c.deleted= ?' , $flag )
                ->andWhere("c.name LIKE ?", "$keyword%")->orderBy("c.name ASC")
                ->limit(5)->fetchArray();
        return $data;

    }

    public function editCategory($params,$type)
    {

        if($type == 'post'){

            $edit = Doctrine_Core::getTable('Articlecategory')->find($params['id']);
            $edit->name = BackEnd_Helper_viewHelper::stripSlashesFromString($params['categoryName']);
            $edit->permalink =  BackEnd_Helper_viewHelper::stripSlashesFromString ($params['permaLink']);
            $edit->metatitle =BackEnd_Helper_viewHelper::stripSlashesFromString ($params['metaTitle']);
            $edit->metadescription =BackEnd_Helper_viewHelper::stripSlashesFromString ($params['metaDescription']);
            $edit->description = BackEnd_Helper_viewHelper::stripSlashesFromString( $params['description']);

            if (isset($_FILES['categoryIconNameHidden']) && $_FILES['categoryIconNameHidden']['name'] != '') {

                $result = self::uploadImage('categoryIconNameHidden');

                if ($result['status'] == '200') {
                    $ext = BackEnd_Helper_viewHelper::getImageExtension(
                            $result['fileName']);

                    $edit->ArtCatIcon->ext = $ext;
                    $edit->ArtCatIcon->path = $result['path'];
                    $edit->ArtCatIcon->name = BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                } else{
                    return false;
                }
            }
            $getPage = Doctrine_Query::create()->select()->from('Articlecategory')->where('id = '.$edit->id )->fetchArray();
            if(!empty($getPage[0]['permalink'])){
                $updateRouteLink = Doctrine_Core::getTable('RoutePermalink')->findOneBy('permalink', $getPage[0]['permalink'] );
            }else{
                $updateRouteLink = new RoutePermalink();
            }
            //print_r($getPage[0]['permalink']); die;
            //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_articlecategory_list');

            $ext = BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
            $edit->ArtCatIcon->ext = $ext;

            try {
                $edit->save();

/* 				if(!empty($updateRouteLink)){
                    $updateRouteLink->permalink = 'plus/'.$params['permaLink'];
                    $updateRouteLink->type = 'ARTCAT';
                    $updateRouteLink->exactlink = 'plus/category/id/'.$edit->id;
                    $updateRouteLink->save();
                } */
                $delArticleCategory = Doctrine_Query::create()->delete('RefArticlecategoryRelatedcategory')
                                                              ->where('articlecategoryid = '.$params['id'])
                                                              ->execute();

                foreach ($params['selectedCategoryies'] as $relatedCategory) {
                    $data = Doctrine_Query::create()->select()
                    ->from('RefArticlecategoryRelatedcategory artcatrelated')
                    ->where('relatedcategoryid ='.$relatedCategory)
                    ->andWhere('articlecategoryid ='.$params['id'])
                    ->fetchArray();

                    if(sizeof($data) == 0){
                        $refRelatedCategory = new RefArticlecategoryRelatedcategory();
                        $refRelatedCategory->articlecategoryid = $params['id'];
                        $refRelatedCategory->relatedcategoryid = $relatedCategory;
                        $refRelatedCategory->save();
                    }
                }
                //Remove Cache for category

                $pageIds = self::findPageId($params['id']);
                $artArr = array();
                for($i=0;$i<count($pageIds);$i++){
                    $artArr[] = $pageIds[$i]['pageid'];
                }
                $page_ids = array_unique($artArr);
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mspagepopularCodeAtTheMoment_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');

                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('plus_pageHeader_image');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('article_'.$params['permaLink'].'_details ');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_offers');

                foreach($page_ids as $ids):

                    $key = "all_allMSArticle".$ids."_list";
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

                endforeach;



                return true;
            } catch (Exception $e) {
                return false;
            }


        }else{
            $data = Doctrine_Query::create()->select()
                                            ->from('Articlecategory artcat')
                                            ->leftJoin('artcat.ArtCatIcon icon')
                                            ->leftJoin('artcat.relatedcategory related')
                                            ->where('id='.$params['id'])
                                            ->fetchOne();
            return $data->toArray();
        }
    }

    public static function permanentDeleteArticleCategory($id)
    {
        if ($id) {

            $dela = Doctrine_Query::create()->delete()
                                            ->from('RefArticlecategoryRelatedcategory r')
                                            ->where('r.articlecategoryid=' . $id)
                                            ->execute();

            $del = Doctrine_Query::create()->delete()
                                           ->from('Articlecategory')
                                           ->where("id=" . $id)
                                           ->execute();

        } else {

            $id = null;
        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_articlecategory_list');

        $pageIds = self::findPageId($id);
        $artArr = array();
        for($i=0;$i<count($pageIds);$i++) {
            $artArr[] = $pageIds[$i]['pageid'];
        }
        $page_ids = array_unique($artArr);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mspagepopularCodeAtTheMoment_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('plus_pageHeader_image');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('article_'.$params['permaLink'].'_details ');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_offers');

        foreach($page_ids as $ids):

            $key = "all_allMSArticle".$ids."_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        endforeach;

        return $id;

    }

    public static function exportarticlecategorylist()
    {
        $categoryList = Doctrine_Query::create()->select('cat.id,cat.name,cat.permalink,cat.metatitle')
        ->from("Articlecategory cat")
        ->where("cat.deleted=0")
        ->orderBy("cat.id DESC")
        ->fetchArray();
        return $categoryList;

    }

   /*
    * Get cateory on id basis
    * Author: Raman
    * Version 1.0
    *
    */
    public static function getArticleCategory($catid)
    {
        $category = Doctrine_Query::create()->select('c.name, c.metatitle,c.permalink, c.metadescription,c.description, ai.path, ai.name')
        ->from("Articlecategory c")
        ->leftJoin("c.ArtCatIcon ai")
        ->where("c.deleted=0")
        ->andWhere("c.permaLink = '".$catid."'")
        ->fetchArray();
        return $category;

    }

    /**
     * Get page Id
     * @author Raman
     * @version 1.0
     */

    public static function findPageId($catId)
    {
        $pageIdList = Doctrine_Query::create()->select('ms.pageid')
        ->from("MoneySaving ms")
        ->where('ms.categoryid='.$catId.'')
        ->fetchArray();
        return $pageIdList;

    }


    /**
     * getAllUrls
     *
     * returns the all the urls related to the article category  like  permalink, realted category pages
     * @param integer $id articlae category id
     * @author Surinderpal Singh
     * @return array array of urls
     */
    public static function getAllUrls($id)
    {
        $data  = Doctrine_Query::create()->select("ac.permalink,a.permalink")
                            ->from('Articlecategory ac')
                            ->leftJoin("ac.articles a")
                            ->where("ac.id=? " , $id)
                            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

        $urlsArray = array();

        $cetgoriesPage = 'pluscat' .'/' ;

        # check for article permalink
        if(isset($data['permalink'])) {
            $urlsArray[] = $cetgoriesPage . $data['permalink'];
        }

        # check an article has one or more categories
        if(isset($data['articles']) && count($data['articles']) > 0) {
            # traverse through all catgories
            foreach($data['articles'] as $value) {
                # check if a category has permalink then add it into array
                if(isset($value['permalink']) && strlen($value['permalink']) > 0 ) {
                    $urlsArray[] = $cetgoriesPage . $value['permalink'] ;
                }
            }
        }
        return $urlsArray ;
    }
}
