<?php
namespace KC\Repository;

class Category extends \KC\Entity\Category
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################

    public static function getCategoryVoucherCodes($categoryId, $numberOfOffers = 0, $pageName = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $categoryOffersList = $queryBuilder
        ->select(
            "roc.offerId as oid,roc.categoryId as cid,c.permalink as categoryPermalink,c.name as categoryName,
            o.*,s.id as shopId, s.refUrl, s.actualUrl, s.name,s.permaLink as permalink,l.path,l.name,
            fv.shopId,fv.visitorId,fv.Id,terms.content"
        )
        ->from("KC\Entity\RefOfferCategory", "roc")
        ->leftJoin("roc.Category", "c")
        ->leftJoin("roc.Offer", "o")
        ->leftJoin("o.shop", "s")
        ->leftJoin('o.termandcondition', 'terms')
        ->leftJoin("s.logo", "l")
        ->leftJoin('s.favoriteshops', 'fv')
        ->where("roc.categoryId =".$categoryId)
        ->andWhere("c.deleted = 0")
        ->andWhere("c.status= 1")
        ->andWhere('o.discounttype="CD"')
        ->andWhere(
            "(couponCodeType = 'UN' AND (
            SELECT count(id)  FROM KC\Entity\CouponCode cc WHERE cc.offerid = o.id and status=1)  > 0
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
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startDate', 'DESC')
        ->setMaxResults($numberOfOffers)
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $popularCategories = $queryBuilder
            ->select('p.id, o.name,o.categoryiconid,i.type,i.path,i.name,p.type,p.position,p.categoryId,o.permaLink')
            ->from('KC\Entity\PopularCategory', 'p')
            ->addSelect(
                "(
                    SELECT  count(roc) FROM KC\Entity\RefOfferCategory roc LEFT JOIN roc.Offer off LEFT JOIN off.shop s  WHERE  
                    off.deleted = 0 and s.deleted = 0 and roc.categoryId = p.categoryId and off.enddate >'"
                .$currentDateAndTime."' and off.discounttype='CD' and off.Visability!='MEM') as countOff"
            )
            ->addSelect(
                "(SELECT count(off1.id) FROM KC\Entity\RefShopCategory roc1 LEFT JOIN roc1.shops s1 LEFT JOIN s1.offer off1  
                    WHERE  s1.deleted = 0 and 
                    s1.status = 1 and off1.deleted = 0 and roc1.categoryId = p.categoryId  
                    and off1.enddate >'".$currentDateAndTime."' and off1.startdate < '".$currentDateAndTime."') 
                    as totalOffers"
            )
            ->leftJoin('p.category', 'o')
            ->leftJoin('o.categoryicon', 'i')
            ->where('o.deleted=0')
            ->andWhere('o.status= 1')
            ->orderBy("countOff", "DESC")
            ->setMaxResults($categoriesLimit)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularCategories;
    }

    public static function getCategoryDetails($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $categoryDetails = $queryBuilder
        ->select(
            "c.*,i.name,i.path, categoryfeaturedimage.name, categoryfeaturedimage.path, categoryheaderimage.name,
            categoryheaderimage.path"
        )
        ->from('KC\Entity\Category', 'c')
        ->LeftJoin("c.categoryicon", "i")
        ->LeftJoin("c.categoryfeaturedimage", "categoryfeaturedimage")
        ->LeftJoin("c.categoryheaderimage", "categoryheaderimage")
        ->where("permalink = ?", $permalink)
        ->andWhere('c.deleted=0')
        ->andWhere('c.status= 1')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $categoryDetails;

    }
   
    public static function saveCategories($categoryParameter)
    {
        $category = new \KC\Entity\Category();
        self::getCategoryParameters($categoryParameter, $category);
        $category->status = '1';
        $category->deleted = 0;
        $category->created_at = new \DateTime('now');
        $category->updated_at = new \DateTime('now');
        $categoryIconId = self::
            setCategoryImage($_FILES['categoryIconNameHidden']['name'], 'categoryIconNameHidden', $category, 'thumb');
        $categoryFeaturedImageId = self::
            setCategoryImage($_FILES['categoryFeaturedImage']['name'], 'categoryFeaturedImage', $category, 'featured');
        $categoryHeaderImageId = self::
            setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
        $category->categoryicon = \Zend_Registry::get('emLocale')->find('KC\Entity\CategoryIcon', $categoryIconId);
        $category->categoryFeaturedImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\CategoryIcon')->find($categoryFeaturedImageId);
        $category->categoryHeaderImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\CategoryIcon')->find($categoryHeaderImageId);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $categoryParameter["permaLink"]);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');
\Zend_Registry::get('emLocale')->persist($category); \Zend_Registry::get('emLocale')->flush(); die;    try {

            
            
            self::updateFeaturedCategory($category->id);
            self::categoryRoutePermalinkSave($categoryParameter, $category);
            return array($category->id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateCategory($categoryParameter)
    {
        $category = \Zend_Registry::get('emLocale')->find('KC\Entity\Category', $categoryParameter['id']);
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
            $category->categoryicon = $categoryIconId;
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = $categoryFeaturedImageId;
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = $categoryHeaderImageId;
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '') {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryicon = $categoryIconId;
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = $categoryFeaturedImageId;
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryHeaderImage']['name'] != '') {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryicon = $categoryIconId;
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = $categoryHeaderImageId;
        } else if ($_FILES['categoryHeaderImage']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = $categoryHeaderImageId;
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = $categoryFeaturedImageId;
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' &&  $_FILES['categoryFeaturedImage']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '' ) {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryicon = $categoryIconId;
        } else if ($_FILES['categoryFeaturedImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '') {
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = $categoryFeaturedImageId;
        } else if ($_FILES['categoryHeaderImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryFeaturedImage']['name'] == '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = $categoryHeaderImageId;
        }
        $categoryInfo = self::getCategoryById($categoryParameter['id']);

        if (!empty($categoryInfo[0]['permaLink'])) {
            $getRouteLink = self::getCategoryRoutePermalink($categoryInfo);
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $categoryParameter["permaLink"]);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');
        try {
            \Zend_Registry::get('emLocale')->persist($category);
            \Zend_Registry::get('emLocale')->flush();
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
        $category->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["categoryName"]);
        $category->permaLink = \BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["permaLink"]);
        $category->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["metaTitle"]);
        $category->metaDescription = \BackEnd_Helper_viewHelper::
            stripSlashesFromString($categoryParameter["metaDescription"]);
        $category->description = \BackEnd_Helper_viewHelper::stripSlashesFromString($categoryParameter["description"]);
        $category->featured_category = $categoryParameter["featuredCategory"];
        return true;
    }

    public static function setCategoryImage($categoryIconFileName, $categoryIconName, $category, $imageType)
    {
        if (isset($categoryIconFileName) && $categoryIconFileName != '') {
            $uploadedImage = self::uploadImage($categoryIconName);
            if ($uploadedImage['status'] == '200') {
                $category1 = new \KC\Entity\CategoryIcon();
                $category2 = new \KC\Entity\Image();
                $category1->ext = \BackEnd_Helper_viewHelper::getImageExtension($uploadedImage['fileName']);
                $category1->path = $uploadedImage['path'];
                $category1->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($uploadedImage['fileName']);
                $category1->testdel(0);
                $category1->test('CATICON');
                $category1->testc(new \DateTime('now'));
                $category1->testcd(new \DateTime('now'));
                \Zend_Registry::get('emLocale')->persist($category1);
                \Zend_Registry::get('emLocale')->persist($category2);
                \Zend_Registry::get('emLocale')->flush();
                return $category1->id;
            } else {
                return false;
            }
        }
    }

    public static function categoryRoutePermalinkSave($categoryInfo, $category)
    {
        $categoryRoute = new \KC\Entity\RoutePermalink();
        $categoryRoute->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($categoryInfo['permaLink']);
        $categoryRoute->type = 'CAT';
        $categoryRoute->exactlink = 'category/show/id/'.$category->id;
        $categoryRoute->deleted = 0;
        $categoryRoute->created_at = new \DateTime('now');
        $categoryRoute->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($categoryRoute);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function getCategoryRoutePermalink($categoryInfo)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $queryBuilder
            ->select('rp')
            ->from('KC\Entity\RoutePermalink', 'rp')
            ->where("rp.permalink = '".$categoryInfo[0]['permaLink']."'")
            ->andWhere("rp.type = 'CAT'")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function getCategoryById($categoryId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $queryBuilder
            ->select('c')
            ->from('KC\Entity\Category', 'c')
            ->where('c.id = '.$categoryId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function updateCategoryRoutePermalink($category, $categoryInfo)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $categoryPermalink = 'category/show/id/'.$category['id'];
        $updateRouteLink = $queryBuilder->update('KC\Entity\RoutePermalink', 'rp')
            ->set(
                'rp.permalink',
                "'".\BackEnd_Helper_viewHelper::stripSlashesFromString($category["permaLink"]) ."'"
            )
            ->set('rp.type', "'CAT'")
            ->set('rp.exactlink', "'".$categoryPermalink."'");
        $updateRouteLink->where('rp.type = "CAT"')
            ->andWhere("rp.permalink = '".$categoryInfo[0]['permaLink']."'")
            ->getQuery()
            ->execute();
        return true;
    }

    public static function updateFeaturedCategory($categoryId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder->update('KC\Entity\Category', 'c')
            ->set('c.featured_category', 0)
            ->where('c.id !='. $categoryId)
            ->getQuery()
            ->execute();
        return true;
    }

    public static function getCategoriesInformation()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $categoriesInformation = $queryBuilder->select("c.name as categoryName,c.id,i.path,i.name,c.permaLink,c.featured_category")
            ->from("KC\Entity\Category", "c")
            ->addSelect(
                "(
                    SELECT count(roc) FROM KC\Entity\RefOfferCategory roc LEFT JOIN roc.category off LEFT JOIN off.shopOffers s  
                        WHERE  off.deleted = 0 and s.deleted = 0 and roc.offer = c.id and off.endDate >
                '".$currentDateAndTime."' and off.discountType='CD' and off.Visability!='MEM'
                ) 
            as totalCoupons"
            )
            ->leftJoin("c.categoryicon", "i")
            ->LeftJoin("c.categoryFeaturedImage", "categoryfeaturedimage")
            ->where("c.deleted=0")
            ->andWhere("c.status= 1")
            ->orderBy("c.featured_category", "DESC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //echo "<pre>";print_r($categoriesInformation);die;
        return $categoriesInformation;
    }

    public static function getCategoryInformation($categoryId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $categoryDetails = $queryBuilder
            ->select(
                "c,i.id as categoryIconId,i.name as iconname,i.path as iconpath,categoryfeaturedimage.name as featuredname,
                categoryfeaturedimage.path as featuredpath, categoryheaderimage.name as headername,
                categoryheaderimage.path as headerpath"
            )
            ->from('KC\Entity\Category', 'c')
            ->LeftJoin("c.categoryicon", "i")
            ->LeftJoin("c.categoryFeaturedImage", "categoryfeaturedimage")
            ->LeftJoin("c.categoryHeaderImage", "categoryheaderimage")
            ->where("c.id = ".$categoryId)
            ->andWhere('c.deleted=0')
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $categoryDetails;
    }

    public static function uploadImage($file)
    {
        $uploadPath = UPLOAD_IMG_PATH."category/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
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
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 135, 95, $path);

        $path = ROOT_PATH. $uploadPath . "thum_medium_" . $newImageName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 50, 50, $path);

        $path = ROOT_PATH . $uploadPath . "thum_large_" . $newImageName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 95, 95, $path);

        $path = ROOT_PATH . $uploadPath . "thum_small_" . $newImageName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newImageName, 24, 24, $path);

        $adapter
        ->addFilter(
            new \Zend_Filter_File_Rename(
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
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $allCategories = $queryBuilder
            ->select('c')
            ->from("KC\Entity\Category", "c")
            ->addSelect(
                "(
                    SELECT count(roc) FROM KC\Entity\RefOfferCategory roc LEFT JOIN roc.category off LEFT JOIN off.shopOffers s  
                        WHERE  off.deleted = 0 and s.deleted = 0 and roc.offer = c.id and off.endDate >
                '".$currentDateAndTime."' and off.discountType='CD' and off.Visability!='MEM'
                ) 
            as totalCoupons"
            )
            ->where("c.deleted=0")
            ->andWhere('c.status= 1')
            ->orderBy("totalCoupons", "DESC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allCategories;
    }

    public static function getcategoriesAccordingToCouponCount($categroyDetails)
    {
        return $categroyDetails['totalCoupons'];
    }
    #####################################################
    ############# ENd REFACORED CODE ####################
    #####################################################
    public static function getCategoryList($params = "")
    {
        $srh = @$params["SearchText"] != 'undefined' ? @$params["SearchText"] : '';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $qb = $queryBuilder
            ->from("KC\Entity\Category", "c")
            ->where("c.deleted = 0")
            ->andWhere($queryBuilder->expr()->like('c.name', $queryBuilder->expr()->literal($srh.'%')));

        $request  = \DataTable_Helper::createSearchRequest($params, array('id', 'name', 'status'));
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emUser'), $request);
        $builder->setQueryBuilder($qb)
            ->add('number', 'c.id')
            ->add('text', 'c.name')
            ->add('text', 'c.status');

        $list = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $list = \DataTable_Helper::getResponse($list, $request);
        return $list;

    }

    public static function searchToFiveCategory($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $status = "null";
        $data = $queryBuilder
            ->select('c.name as name')
            ->from("KC\Entity\Category", "c")
            ->where('c.deleted=0')
            ->andWhere($queryBuilder->expr()->like("c.name", $queryBuilder->expr()->literal($keyword."%")))
            ->andWhere("c.deleted=0")
            ->orderBy("c.name", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function changeStatus($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $status = $params['status'] == 'offline' ? '0' : '1';
        $q = $queryBuilder
            ->update('KC\Entity\Category', 'c')
            ->set('c.status', $status)
            ->where('c.id='. $params['id'])
            ->getQuery
            ->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params["permaLink"]);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');

    }

    public static function deleteCategory($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $q = $queryBuilder->update('KC\Entity\Category', 'c')
            ->set('c.deleted', 1)
            ->where('c.id='. $params['id'])
            ->getQuery
            ->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params["permaLink"]);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');
    }

    public static function getAuthorId()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('count(o) as total,o.authorName,o.authorId')
            ->from('KC\Entity\Offer', 'o')
            ->groupBy('o.authorId')
            ->where('o.deleted=0')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if ($data) {
            return $data[0];
        }
    }

    public static function generateMostReadArticle()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $mostReadArticle = $queryBuilder
            ->select(
                'av.id, IDENTITY(av.articles) as articleid, (sum(av.onclick)) as pop, a.title,a.content,
                a.permalink, a.authorname, a.authorid, a.publishdate, ai.path, ai.name'
            )
            ->from('KC\Entity\ArticleViewCount', 'av')
            ->leftJoin('av.articles', 'a')
            ->innerJoin('a.articleImage', 'ai')
            ->where('a.deleted=0')
            ->orderBy('pop', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $mostReadArticle;
    }

    public static function getAllUrls($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data  = $queryBuilder->select("c.id,c.permaLink, ac.permalink as articlecategoryPermalink")
                ->from('KC\Entity\Category', 'c')
                ->leftJoin("c.articlecategory", "ac")
                ->where("c.id=".$id)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $varnishUrls = array();


        $cetgoriesPage = \FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/' ;

        $articlesCetgoriesPage = 'pluscat' .'/' ;

        # check if a category has permalink then add it into array
        if (isset($data[0]['permaLink']) && mb_strlen($data[0]['permaLink']) > 0) {
            $varnishUrls[] = $cetgoriesPage . $data[0]['permaLink'];
            $varnishUrls[] = $cetgoriesPage . $data[0]['permaLink'] .'/2';
            $varnishUrls[] = $cetgoriesPage . $data[0]['permaLink'] .'/3';
        }

        # check a category has one or more related article category
        if (isset($data['articlecategory']) && count($data['articlecategory']) > 0) {
            # traverse through all shops
            foreach ($data['articlecategory'] as $value) {
                # check if a category has permalink then add it into array
                if (isset($value['permalink']) && strlen($value['permalink']) > 0) {
                    $varnishUrls[] = $articlesCetgoriesPage . $value['permalink'] ;
                }
            }
        }
        return $varnishUrls ;
    }

}
