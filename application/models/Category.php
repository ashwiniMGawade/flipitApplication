<?php

/**
 * Category
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Category extends BaseCategory
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################
    /**
     * Function getCategoryVoucherCodes.
     *
     * get vouchercodes per category.
     *
     * @return array $categoryOffers
     * @version 1.0
     */
    public static function getCategoryVoucherCodes($categoryId, $numberOfOffers = 0, $pageName = '')
    {
        $categoryOffers= array();
        $currentDateAndTime = date('Y-m-d H:i:s');
        $categoryOffersList = Doctrine_Query::create()
        ->select(
            "roc.offerId as oid,roc.categoryId as cid,c.permalink as categoryPermalink, o.*,s.refUrl, s.actualUrl, s.name,s.permalink as permalink,l.path,l.name,fv.shopId,fv.visitorId,fv.Id,terms.content"
        )
        ->from("refOfferCategory roc")
        ->leftJoin("roc.Category c")
        ->leftJoin("roc.Offer o")
        ->leftJoin("o.shop s")
        ->leftJoin('o.termandcondition terms')
        ->leftJoin("s.logo l")
        ->leftJoin('s.favoriteshops fv');
        if ($pageName=='home') {
            $categoryId = implode(',', $categoryId);
            $categoryOffersList->where("roc.categoryId IN ($categoryId)");
        } else {
            $categoryOffersList->Where("roc.categoryId =".$categoryId);
        }
        $categoryOffersList->andWhere("c.deleted = 0")
        ->andWhere("c.status= 1")
        ->andWhere('o.discounttype="CD"')
        ->andWhere("(couponCodeType = 'UN' AND (SELECT count(id)  FROM CouponCode cc WHERE cc.offerid = o.id and status=1)  > 0) or couponCodeType = 'GN'")
        ->andWhere("s.deleted = 0")
        ->andWhere("s.status = 1")
        ->andWhere("o.deleted = 0")
        ->andWhere("o.userGenerated = 0")
        ->andWhere('o.enddate > "'.$currentDateAndTime.'"')
        ->andWhere('o.startdate < "'.$currentDateAndTime.'"')
        ->andWhere('o.discounttype="CD"')
        ->andWhere('o.Visability!="MEM"')
        ->orderBy('o.exclusiveCode DESC')
        ->addOrderBy('o.startDate DESC')
        ->limit($numberOfOffers);
        $categoryOffersList = $categoryOffersList->fetchArray();
        return $categoriesoffers = $pageName=='home' ? $categoryOffersList : self::changeDataAccordingToOfferHtml($categoryOffersList);

    }

    public static function changeDataAccordingToOfferHtml($categoryOffersList)
    {
        foreach ($categoryOffersList as $offer) {
            $categoryOffers[] = $offer['Offer'];
        }
        return $categoryOffers;
    }
    /**
     * Function getPopularCategories.
     *
     * Get popular Category list from database for front-end.
     *
     * @version 1.0
     * @return array $allCategories
     */
    public static function getPopularCategories($categoriesLimit = 0)
    {
        $currentDateAndTime = date('Y-m-d H:i:s');
        $popularCategories = Doctrine_Query::create()
        ->select('p.id, o.name,o.categoryiconid,i.type,i.path,i.name,p.type,p.position,p.categoryId,o.permaLink')
        ->from('PopularCategory p')
        ->addSelect("(SELECT  count(*) FROM refOfferCategory roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  WHERE  off.deleted = 0 and s.deleted = 0 and roc.categoryId = p.categoryId and off.enddate >'".$currentDateAndTime."' and off.discounttype='CD' and off.Visability!='MEM') as countOff")
        ->addSelect("(SELECT  count(*) FROM refOfferCategory roc1 LEFT JOIN roc1.Offer off1 LEFT JOIN off1.shop s1  WHERE  off1.deleted = 0 and s1.deleted = 0 and roc1.categoryId = p.categoryId and off1.enddate >'".$currentDateAndTime."'  and off1.Visability!='MEM') as totalOffers")
        ->leftJoin('p.category o')
        ->leftJoin('o.categoryicon i')
        ->where('o.deleted=0')
        ->andWhere('o.status= 1')
        ->orderBy("countOff DESC")
        ->limit($categoriesLimit)
        ->fetchArray();
        return $popularCategories;
    }

    public static function getCategoryDetails($permalink)
    {
        $categoryDetails = Doctrine_Query::create()
        ->select("c.*,i.name,i.path, categoryfeaturedimage.name, categoryfeaturedimage.path, categoryheaderimage.name, categoryheaderimage.path")
        ->from('Category c')
        ->LeftJoin("c.categoryicon i")
        ->LeftJoin("c.categoryfeaturedimage categoryfeaturedimage")
        ->LeftJoin("c.categoryheaderimage categoryheaderimage")
        ->where("permalink = ?", $permalink)
        ->andWhere('c.deleted=0')
        ->andWhere('c.status= 1')
        ->fetchArray();
        return $categoryDetails;

    }
    /**
     * Save new category.
     *
     * @param array $categoryParameter
     * @return mixed
     * @version 1.0
     */
    public static function saveCategories($categoryParameter)
    {
        $category = new Category();
        self::getCategoryParameters($categoryParameter, $category);
        $category->status = '1';
        $categoryIconId = self::
            setCategoryImage($_FILES['categoryIconNameHidden']['name'], 'categoryIconNameHidden', $category, 'thumb');
        $categoryFeaturedImageId = self::
            setCategoryImage($_FILES['categoryFeaturedImage']['name'], 'categoryFeaturedImage', $category, 'featured');
        $categoryHeaderImageId = self::
            setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
        $category->categoryIconId = $categoryIconId;
        $category->categoryFeaturedImageId = $categoryFeaturedImageId;
        $category->categoryHeaderImageId = $categoryHeaderImageId;
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcategory_list');

        try {
            $category->save();
            self::updateFeaturedCategory($category->id);
            self::categoryRoutePermalinkSave($categoryParameter, $category);
            return array($category->toArray(), $category->toArray());
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * Update by id category.
     *
     * @param array $categoryParameter
     * @version 1.0
     */
    public static function updateCategory($categoryParameter)
    {
        $category = Doctrine_Core::getTable('Category')->find( $categoryParameter['id']);
        self::getCategoryParameters($categoryParameter, $category);
       
        if($_FILES['categoryIconNameHidden']['name'] != ''
            && $_FILES['categoryFeaturedImage']['name'] != '' 
            && $_FILES['categoryHeaderImage']['name'] != '' ){
            $categoryIconId = self::
                setCategoryImage($_FILES['categoryIconNameHidden']['name'], 'categoryIconNameHidden', $category, 'thumb');
            $category->categoryIconId = $categoryIconId;
            $categoryFeaturedImageId = self::
                setCategoryImage($_FILES['categoryFeaturedImage']['name'], 'categoryFeaturedImage', $category, 'featured');
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
        }else if($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != ''){
            $categoryIconId = self::
                setCategoryImage($_FILES['categoryIconNameHidden']['name'], 'categoryIconNameHidden', $category, 'thumb');
            $category->categoryIconId = $categoryIconId;
            $categoryFeaturedImageId = self::
                setCategoryImage($_FILES['categoryFeaturedImage']['name'], 'categoryFeaturedImage', $category, 'featured');
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;    
        }else if($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryHeaderImage']['name'] != ''){
            $categoryIconId = self::
                setCategoryImage($_FILES['categoryIconNameHidden']['name'], 'categoryIconNameHidden', $category, 'thumb');
            $category->categoryIconId = $categoryIconId;
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
        }else if($_FILES['categoryHeaderImage']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '' ){
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
            $categoryFeaturedImageId = self::
                setCategoryImage($_FILES['categoryFeaturedImage']['name'], 'categoryFeaturedImage', $category, 'featured');
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
        }elseif($_FILES['categoryIconNameHidden']['name'] != '' &&  $_FILES['categoryFeaturedImage']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '' ) {
            $categoryIconId = self::
                setCategoryImage($_FILES['categoryIconNameHidden']['name'], 'categoryIconNameHidden', $category, 'thumb');
            $category->categoryIconId = $categoryIconId;
        }elseif($_FILES['categoryFeaturedImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' && 
            $_FILES['categoryHeaderImage']['name'] == '') {
            $categoryFeaturedImageId = self::
                setCategoryImage($_FILES['categoryFeaturedImage']['name'], 'categoryFeaturedImage', $category, 'featured');
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
        }elseif($_FILES['categoryHeaderImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryFeaturedImage']['name'] == '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
        }
        $categoryInfo = self::getCategoryById($categoryParameter['id']);

        if (!empty($categoryInfo[0]['permaLink'])) {
            $getRouteLink = self::getCategoryRoutePermalink($categoryInfo);
        } else {
            $updateRouteLink = new RoutePermalink();
        }

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcategory_list');
        try {
            $category->save();
            self::updateFeaturedCategory($categoryParameter['id']);
            if(!empty($getRouteLink)){
                self::updateCategoryRoutePermalink($categoryParameter, $categoryInfo);
            }
            return true;
        }catch (Exception $e) {
            return false;
        }

    }

    public static function getCategoryParameters($categoryParameter, $category)
    {
        $category->name = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["categoryName"]);
        $category->permaLink = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["permaLink"]);
        $category->metatitle = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["metaTitle"]);
        $category->metaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["metaDescription"]);
        $category->description = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["description"]);
        $category->featured_category = $categoryParameter["featuredCategory"];
        return true;
    }

    public static function setCategoryImage($categoryIconFileName, $categoryIconName, $category, $imageType)
    {
        if (isset($categoryIconFileName) && $categoryIconFileName != '') {
            $uploadedImage = self::uploadImage($categoryIconName);
            if ($uploadedImage['status'] == '200') {
                $category = new CategoryIcon();
                $category->ext = BackEnd_Helper_viewHelper::getImageExtension($uploadedImage['fileName']);
                $category->path = $uploadedImage['path'];
                $category->name = BackEnd_Helper_viewHelper::stripSlashesFromString($uploadedImage['fileName']);
                $category->save();
                return $category->id;
            } else {
                return false;
            }
        }
    }

    public static function categoryRoutePermalinkSave($categoryInfo, $category)
    {
        $categoryRoute = new RoutePermalink();
        $categoryRoute->permalink = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryInfo['permaLink']);
        $categoryRoute->type = 'CAT';
        $categoryRoute->exactlink = 'category/show/id/'.$category->id;
        $categoryRoute->save();
        return true;
    }

    public static function getCategoryRoutePermalink($categoryInfo)
    {
        return Doctrine_Query::create()->select()->from('RoutePermalink')->where("permalink = '".$categoryInfo[0]['permaLink']."'")->andWhere('type = "CAT"')->fetchArray();
    }

    public static function getCategoryById($categoryId)
    {
        return Doctrine_Query::create()->select()->from('Category')->where('id = '.$categoryId)->fetchArray();
    }

    public static function updateCategoryRoutePermalink($category, $categoryInfo)
    {
        $categoryPermalink = 'category/show/id/'.$category['id'];
        $updateRouteLink = Doctrine_Query::create()->update('RoutePermalink')
            ->set('permalink', "'".
            BackEnd_Helper_viewHelper::stripSlashesFromString($category["permaLink"]) ."'")
            ->set('type', "'CAT'")
            ->set('exactlink', "'".$categoryPermalink."'");
        $updateRouteLink->where('type = "CAT"')->andWhere("permalink = '".$categoryInfo[0]['permaLink']."'")->execute();
        return true;
    }

    public static function updateFeaturedCategory($categoryId)
    {
        Doctrine_Query::create()->update('Category c')
            ->set('c.featured_category', 0)
            ->where('c.id !='. $categoryId)
            ->execute();
        return true;
    }

    public static function getCategoriesDetail()
    {
        $categoriesDetail = Doctrine_Query::create()
            ->select('c.name,c.id,i.path,i.name,c.permaLink,c.featured_category, categoryfeaturedimage.*')
            ->from("Category c")
            ->leftJoin("c.categoryicon i")
            ->LeftJoin("c.categoryfeaturedimage categoryfeaturedimage")
            ->where("c.deleted=0" )
            ->andWhere("c.status= 1")
            ->orderBy("c.featured_category DESC")
            ->fetchArray();
        return $categoriesDetail;
    }

    public static function getCategoryInformation($categoryId)
    {
        $categoryDetails = Doctrine_Query::create()
        ->select("c.*,i.name,i.path,categoryfeaturedimage.name,categoryfeaturedimage.path, categoryheaderimage.name,categoryheaderimage.path")
        ->from('Category c')
        ->LeftJoin("c.categoryicon i")
        ->LeftJoin("c.categoryfeaturedimage categoryfeaturedimage")
        ->LeftJoin("c.categoryheaderimage categoryheaderimage")
        ->where("id = ?", $categoryId)
        ->andWhere('c.deleted=0')
        ->fetchArray();
        return $categoryDetails;

    }
    #####################################################
    ############# ENd REFACORED CODE ####################
    #####################################################
    /**
     * upload image for category icon
     * @param array $params
     * @param string $image
     * @return string $orgName
     * @author blal
     * @version 1.0
     */

    public static function uploadImage($file)
    {
       // generate upload path for images related to category
        $uploadPath = UPLOAD_IMG_PATH."category/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        // generate real path for upload path
        $rootPath = ROOT_PATH.$uploadPath;
        //echo $rootPath; die;
        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath))
            mkdir($rootPath ,776, true);


        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, 'jpg,png');
        $adapter->addValidator('Size', false, array('max' => '2MB'));

        // get file name
        $name = $adapter->getFileName($file, false);

        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;

        // generates complete path of image
        $cp = $rootPath . $newName;


        /**
         *	 generating thumnails for image
         */

        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 135, 95, $path);

        $path = ROOT_PATH. $uploadPath . "thum_medium_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 50, 50, $path);

        $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 95, 95, $path);

        $path = ROOT_PATH . $uploadPath . "thum_small_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 24, 24, $path);

        //echo "<pre>"; print_r($file); die;
        //apply filter to rename file name and set target
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
                    "path" => $uploadPath);

        } else {

            return array("status" => "-1"
            );

        }

    }


    /**
     * get categories to show in list
     * @param array $params
     * @return array $categoryList
     * @author blal updated by kraj
     * @version 1.0
     */
    public static function getCategoryList($params = "")
    {
        $conn2 = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        BackEnd_Helper_viewHelper::closeConnection($conn2);
        $srh = @$params["SearchText"] != 'undefined' ? @$params["SearchText"] : '';
        //$delVal = isset($params['status']) ?  array($params['status']) : array(null,0, 1);
        $categoryList = Doctrine_Query::create()
                ->select('c.id as id,c.name as name ,c.status as status')
                ->from("Category c")
                ->Where("deleted = 0" )
                ->andWhere("c.name LIKE ?", "$srh%")
                ->orderBy("c.name ASC");

        $list = DataTable_Helper::generateDataTableResponse($categoryList,
                        $params, array("__identifier" => 'c.id', 'c.id','c.name'),
                        array(), array());
        return $list;

    }

    /**
     * get to five category
     * @param string $keyword
     * @return array $data
     * @author blal
     * @version 1.0
     */
    public static function searchToFiveCategory($keyword)
    {
      $status="null";
        $data = Doctrine_Query::create()
                ->select('c.name as name')
                ->from("Category c")->where('c.deleted=0')
                ->andWhere("c.name LIKE ?", "$keyword%")
                ->andWhere("c.deleted=0")
                ->orderBy("c.name ASC")
                ->limit(5)->fetchArray();
        return $data;
    }
    /**
     *change status of category
     * @param array $params
     * @author blal
     * @version 1.0
     */
    public static function changeStatus($params)
    {
        $status = $params['status'] == 'offline' ? '0' : '1';
        $q = Doctrine_Query::create()
                ->update('Category c')
                ->set('c.status', $status)
                ->where('c.id=?', $params['id'])
                ->execute();

        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcategory_list');

    }
    /**
     * deleted category by id
     * @param integer $params
     * @author blal
     * @version 1.0
     */
    public static function deleteCategory($params)
    {
        $q = Doctrine_Query::create()->update('Category c')
                                    ->set('c.deleted', 1)
                                    ->where('c.id=?', $params['id'])
                                    ->execute();
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcategory_list');
    }

    /**
     * get list of category for export
     * @author kraj
     * @return array $categoryList
     * @version 1.0
     */
    public static function exportcategoryList()
    {
       $categoryList = Doctrine_Query::create()
                ->select('c.*')
                ->from("Category  c")
                ->where("c.deleted=0")
                ->orderBy("c.id DESC")
                ->fetchArray();
        return $categoryList;

    }

/******************functions to be used on frontend*******************/
     /**
      * get categories show in frontend user profile
      * @author sunny patial
      * @return array $categoryIcons
      * @version 1.0
      */
     public static function getCategoryUser()
     {
         $categoryIcons = Doctrine_Query::create()
                         ->select('c.name,c.id')
                         ->from("Category c")
                         ->Where("deleted = 0" )
                         ->andWhere("status= 1")
                         ->orderBy("c.name ASC")
                         ->fetchArray();
         return $categoryIcons;
     }

     /**
      * Get special Category list from database
      * @author blal
      * @version 1.0
      * @return array $data
      */
     public static function getSpecialCategory()
     {
         $data = Doctrine_Query::create()
                 ->select('p.*,o.id,o.title,roc.*,c.*,ci.*')
                 ->from('SpecialList p')
                 ->leftJoin('p.offer o')
                 ->leftJoin('o.refOfferCategory roc')
                 ->leftJoin('roc.Category c')
                 ->leftJoin('c.categoryicon ci')
                 ->where('o.deleted=0' )
                 ->andWhere('c.deleted=0' )
                 ->andWhere('p.deleted=0')
                 ->limit(9)
                 ->fetchArray();
         return $data;
     }
     /**
      *
      */
      public static function getAuthorId()
      {
          $data = Doctrine_Query::create()
                  ->select('count(*) as total,o.authorName,o.authorId')
                  ->from('offer o')
                  ->groupBy('authorId')
                  ->where('o.deleted=0' )
                  ->orderBy('total DESC')
                  ->fetchArray();
      if($data){
      return $data[0];
      }
    }
      public static function getPouparCategory()
      {
          $data = Doctrine_Query::create()
                  ->select('p.*,c.*')
                  ->from('PopularCategory p')
                  ->leftJoin('p.category c')
                  ->orderBy('p.id DESC')->limit(3)
                  ->fetchArray();
          return $data;
      }
      /**
       * for widget
       */
      public static function generateMostReadArticle()
      {
        $mostReadArticle = Doctrine_Query::create()
                        ->select('av.id, av.articleid, (sum(av.onclick)) as pop, a.title,a.content,a.permalink, a.authorname, a.authorid, a.publishdate, ai.path, ai.name')
                        ->from('ArticleViewCount av')
                        ->leftJoin('av.articles a')
                        ->innerJoin('a.articleImage ai')
                        ->where('a.deleted=0' )
                        ->orderBy('pop DESC')
                        ->limit(4)
                        ->fetchArray();

      return $mostReadArticle;

    }


    /**
     * getAllUrls
     *
     * returns the realted store page url
     * @param integer $id  cvategory id
     * @author Surinderpal Singh
     * @return array array of urls
     */
    public static function getAllUrls($id)
    {
        $data  = Doctrine_Query::create()->select("s.permaLink,c.id,c.permaLink, ac.permalink")
                ->from('Category c')
                ->leftJoin("c.articlecategory ac")
                ->leftJoin("c.shop s")
                ->where("c.id=? " , $id)
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

        $urlsArray = array();


        $cetgoriesPage = FrontEnd_Helper_viewHelper::__link( 'categorieen') .'/' ;

        $articlesCetgoriesPage = FrontEnd_Helper_viewHelper::__link( 'pluscat') .'/' ;

        # check if a category has permalink then add it into array
        if(isset($data['permaLink']) && strlen($data['permaLink']) > 0 ) {
            $urlsArray[] = $cetgoriesPage . $data['permaLink'] ;
        }


        /* # check a category has one or more related
        if(isset($data['shop']) && count($data['shop']) > 0 ) {
            # traverse through all shops
            foreach($data['shop'] as $value) {
                # check if a category has permalink then add it into array
                if(isset($value['permaLink']) && strlen($value['permaLink']) > 0 ) {
                    $urlsArray[] = $value['permaLink'] ;
                }
            }
        }  */


        # check a category has one or more related article category
        if(isset($data['articlecategory']) && count($data['articlecategory']) > 0 ) {
            # traverse through all shops
            foreach($data['articlecategory'] as $value) {
                # check if a category has permalink then add it into array
                if(isset($value['permalink']) && strlen($value['permalink']) > 0 ) {
                    $urlsArray[] = $articlesCetgoriesPage . $value['permalink'] ;
                }
            }
        }
        return $urlsArray ;
    }

}
