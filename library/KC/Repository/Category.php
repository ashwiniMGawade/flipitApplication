<?php
namespace KC\Repository;

class Category extends \KC\Entity\Category
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################

    public static function getCategoryVoucherCodes($categoryId, $numberOfOffers = 0, $pageName = '')
    {
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $categoryOffersList = Doctrine_Query::create()
        ->select(
            "roc.offerId as oid,roc.categoryId as cid,c.permalink as categoryPermalink,c.name as categoryName,
            o.*,s.id as shopId, s.refUrl, s.actualUrl, s.name,s.permalink as permalink,l.path,l.name,
            fv.shopId,fv.visitorId,fv.Id,terms.content"
        )
        ->from("refOfferCategory roc")
        ->leftJoin("roc.Category c")
        ->leftJoin("roc.Offer o")
        ->leftJoin("o.shop s")
        ->leftJoin('o.termandcondition terms')
        ->leftJoin("s.logo l")
        ->leftJoin('s.favoriteshops fv')
        ->where("roc.categoryId =".$categoryId)
        ->andWhere("c.deleted = 0")
        ->andWhere("c.status= 1")
        ->andWhere('o.discounttype="CD"')
        ->andWhere(
            "(couponCodeType = 'UN' AND (
            SELECT count(id)  FROM CouponCode cc WHERE cc.offerid = o.id and status=1)  > 0
            ) or couponCodeType = 'GN'"
        )
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
        ->limit($numberOfOffers)
        ->fetchArray();
        return self::changeDataAccordingToOfferHtml($categoryOffersList);
    }

    public static function changeDataAccordingToOfferHtml($categoryOffersList)
    {
        $categoryOffers = array();
        foreach ($categoryOffersList as $offer) {
            $categoryOffers[] = $offer['Offer'];
        }
        return $categoryOffers;
    }
    
    public static function getPopularCategories($categoriesLimit = 0, $pageName = '')
    {
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $popularCategories = Doctrine_Query::create()
        ->select('p.id, o.name,o.categoryiconid,i.type,i.path,i.name,p.type,p.position,p.categoryId,o.permaLink')
        ->from('PopularCategory p')
        ->addSelect(
            "(
                SELECT  count(*) FROM refOfferCategory roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  WHERE  
                off.deleted = 0 and s.deleted = 0 and roc.categoryId = p.categoryId and off.enddate >'"
            .$currentDateAndTime."' and off.discounttype='CD' and off.Visability!='MEM') as countOff"
        )
        ->addSelect(
            "(SELECT count(off1.id) FROM refShopCategory roc1 LEFT JOIN roc1.shops s1 LEFT JOIN s1.offer off1  
                WHERE  s1.deleted = 0 and 
                s1.status = 1 and off1.deleted = 0 and roc1.categoryId = p.categoryId  
                and off1.enddate >'".$currentDateAndTime."' and off1.startdate < '".$currentDateAndTime."') 
                as totalOffers"
        )
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
        ->select(
            "c.*,i.name,i.path, categoryfeaturedimage.name, categoryfeaturedimage.path, categoryheaderimage.name,
            categoryheaderimage.path"
        )
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
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params["permaLink"]);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');

        try {
            $category->save();
            self::updateFeaturedCategory($category->id);
            self::categoryRoutePermalinkSave($categoryParameter, $category);
            return array($category->toArray(), $category->toArray());
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateCategory($categoryParameter)
    {
        $category = Doctrine_Core::getTable('Category')->find($categoryParameter['id']);
        self::getCategoryParameters($categoryParameter, $category);
       
        if ($_FILES['categoryIconNameHidden']['name'] != ''
            && $_FILES['categoryFeaturedImage']['name'] != ''
            && $_FILES['categoryHeaderImage']['name'] != '' ) {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryIconId = $categoryIconId;
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '') {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryIconId = $categoryIconId;
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryHeaderImage']['name'] != '') {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryIconId = $categoryIconId;
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
        } else if ($_FILES['categoryHeaderImage']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' &&  $_FILES['categoryFeaturedImage']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '' ) {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryIconId = $categoryIconId;
        } else if ($_FILES['categoryFeaturedImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '') {
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImageId = $categoryFeaturedImageId;
        } else if ($_FILES['categoryHeaderImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryFeaturedImage']['name'] == '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImageId = $categoryHeaderImageId;
        }
        $categoryInfo = self::getCategoryById($categoryParameter['id']);

        if (!empty($categoryInfo[0]['permaLink'])) {
            $getRouteLink = self::getCategoryRoutePermalink($categoryInfo);
        }

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params["permaLink"]);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');

        try {
            $category->save();
            self::updateFeaturedCategory($categoryParameter['id']);
            if (!empty($getRouteLink)) {
                self::updateCategoryRoutePermalink($categoryParameter, $categoryInfo);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    public static function getCategoryParameters($categoryParameter, $category)
    {
        $category->name = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["categoryName"]);
        $category->permaLink = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["permaLink"]);
        $category->metatitle = BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["metaTitle"]);
        $category->metaDescription = BackEnd_Helper_viewHelper::
            stripSlashesFromString($categoryParameter["metaDescription"]);
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
        return Doctrine_Query::create()
            ->select()
            ->from('RoutePermalink')
            ->where("permalink = '".$categoryInfo[0]['permaLink']."'")
            ->andWhere('type = "CAT"')
            ->fetchArray();
    }

    public static function getCategoryById($categoryId)
    {
        return Doctrine_Query::create()->select()->from('Category')->where('id = '.$categoryId)->fetchArray();
    }

    public static function updateCategoryRoutePermalink($category, $categoryInfo)
    {
        $categoryPermalink = 'category/show/id/'.$category['id'];
        $updateRouteLink = Doctrine_Query::create()->update('RoutePermalink')
            ->set(
                'permalink',
                "'".BackEnd_Helper_viewHelper::stripSlashesFromString($category["permaLink"]) ."'"
            )
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

    public static function getCategoriesInformation()
    {
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $categoriesInformation = Doctrine_Query::create()
            ->select('c.name,c.id,i.path,i.name,c.permaLink,c.featured_category, categoryfeaturedimage.*')
            ->from("Category c")
            ->addSelect(
                "(
                    SELECT count(*) FROM refOfferCategory roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  
                        WHERE  off.deleted = 0 and s.deleted = 0 and roc.categoryId = c.id and off.enddate >
                '".$currentDateAndTime."' and off.discounttype='CD' and off.Visability!='MEM'
                ) 
            as totalCoupons"
            )
            ->leftJoin("c.categoryicon i")
            ->LeftJoin("c.categoryfeaturedimage categoryfeaturedimage")
            ->where("c.deleted=0")
            ->andWhere("c.status= 1")
            ->orderBy("c.featured_category DESC")
            ->fetchArray();
        return $categoriesInformation;
    }

    public static function getCategoryInformation($categoryId)
    {
        $categoryDetails = Doctrine_Query::create()
        ->select(
            "c.*,i.name,i.path,categoryfeaturedimage.name,categoryfeaturedimage.path, categoryheaderimage.name,
            categoryheaderimage.path"
        )
        ->from('Category c')
        ->LeftJoin("c.categoryicon i")
        ->LeftJoin("c.categoryfeaturedimage categoryfeaturedimage")
        ->LeftJoin("c.categoryheaderimage categoryheaderimage")
        ->where("id = ?", $categoryId)
        ->andWhere('c.deleted=0')
        ->fetchArray();
        return $categoryDetails;
    }

    public static function uploadImage($file)
    {
        $uploadPath = UPLOAD_IMG_PATH."category/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH.$uploadPath;
        $files = $adapter->getFileInfo($file);
        
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0776, true);
        }

        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, 'jpg,png');
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        $fileName = $adapter->getFileName($file, false);
        $newImageName = time() . "_" . $fileName;
        $changedImagePath = $rootPath . $newImageName;

        $path = ROOT_PATH . $uploadPath . "thum_" . $newImageName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 135, 95, $path);

        $path = ROOT_PATH. $uploadPath . "thum_medium_" . $newImageName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 50, 50, $path);

        $path = ROOT_PATH . $uploadPath . "thum_large_" . $newImageName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 95, 95, $path);

        $path = ROOT_PATH . $uploadPath . "thum_small_" . $newImageName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 24, 24, $path);

        $adapter
        ->addFilter(
            new Zend_Filter_File_Rename(
                array('target' => $changedImagePath,
                    'overwrite' => true)
            ),
            null,
            $file
        );
        $adapter->receive($file);

        if ($adapter->isValid($file)) {
            return array(
                "fileName" => $newImageName,
                "status" => "200",
                    "path" => $uploadPath);
        } else {
            return array("status" => "-1");
        }
    }

    public static function getAllCategories()
    {
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $allCategories = Doctrine_Query::create()
            ->select('c.*')
            ->from("Category  c")
            ->addSelect(
                "(
                    SELECT count(*) FROM refOfferCategory roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  
                        WHERE  off.deleted = 0 and s.deleted = 0 and roc.categoryId = c.id and off.enddate >
                '".$currentDateAndTime."' and off.discounttype='CD' and off.Visability!='MEM'
                ) 
            as totalCoupons"
            )
            ->where("c.deleted=0")
            ->andWhere('c.status= 1')
            ->orderBy("totalCoupons DESC")
            ->fetchArray();
        return $allCategories;
    }

    public static function getcategoriesAccordingToCouponCount($categroyDetails)
    {
        return $categroyDetails['totalCoupons'];
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
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params["permaLink"]);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');

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
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params["permaLink"]);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');
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

        $varnishUrls = array();


        $cetgoriesPage = FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/' ;

        $articlesCetgoriesPage = 'pluscat' .'/' ;

        # check if a category has permalink then add it into array
        if (isset($data['permaLink']) && mb_strlen($data['permaLink']) > 0 )
        {
            $varnishUrls[] = $cetgoriesPage . $data['permaLink'];
            $varnishUrls[] = $cetgoriesPage . $data['permaLink'] .'/2';
            $varnishUrls[] = $cetgoriesPage . $data['permaLink'] .'/3';
        }

        # check a category has one or more related article category
        if(isset($data['articlecategory']) && count($data['articlecategory']) > 0 ) {
            # traverse through all shops
            foreach($data['articlecategory'] as $value) {
                # check if a category has permalink then add it into array
                if(isset($value['permalink']) && strlen($value['permalink']) > 0 ) {
                    $varnishUrls[] = $articlesCetgoriesPage . $value['permalink'] ;
                }
            }
        }
        return $varnishUrls ;
    }

}
