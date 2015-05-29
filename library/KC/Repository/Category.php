<?php
namespace KC\Repository;

class Category extends \KC\Entity\Category
{
    #####################################################
    ############# REFACORED CODE ########################
    #####################################################

    public static function categoryExistOrNot($categoryId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('c')
            ->from('KC\Entity\Category', 'c')
            ->setParameter(1, $categoryId)
            ->where('c.id = ?1');
        $category = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $category;
    }

    public static function getCategoryInformationForNewsLetter($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
             ->select("c.id, c.name, c.permalink")
            ->from('KC\Entity\Category', 'c')
            ->where("c.id = " . $id);
        $category = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $category;
    }

    public static function getCategoryVoucherCodes($categoryId, $numberOfOffers = 0, $pageName = '', $offerIds = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $query = $queryBuilder
        ->select("roc, c, o, s, terms, l")
        ->from("KC\Entity\RefOfferCategory", "roc")
        ->leftJoin("roc.categories", "c")
        ->leftJoin("roc.offers", "o")
        ->leftJoin("o.shopOffers", "s")
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin("s.logo", "l")
        ->where("roc.categories =".$categoryId)
        ->andWhere("c.deleted = 0")
        ->andWhere("c.status= 1")
        ->andWhere("o.discountType=" . $queryBuilder->expr()->literal('CD'))
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (
            SELECT count(cc.id)  FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id AND cc.status=1)  > 0
            ) or o.couponCodeType = 'GN'"
        )
        ->andWhere("s.deleted = 0")
        ->andWhere("s.status = 1")
        ->andWhere("o.deleted = 0")
        ->andWhere("o.userGenerated = 0")
        ->andWhere('o.endDate > '. $queryBuilder->expr()->literal($currentDateAndTime))
        ->andWhere('o.startDate < '. $queryBuilder->expr()->literal($currentDateAndTime))
        ->andWhere('o.discountType='. $queryBuilder->expr()->literal('CD'))
        ->andWhere('o.Visability!='. $queryBuilder->expr()->literal('MEM'))
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startDate', 'DESC');
        if ($numberOfOffers!=0) {
            $query->setMaxResults($numberOfOffers);
        }
        if (!empty($offerIds) && !empty($offerIds['offers'])) {
            $savedCategoryOffersByAdmin = array();
            foreach ($offerIds as $offerId) {
                if (!empty($offerId['offers'])) {
                    $savedCategoryOffersByAdmin[] = $offerId['offers']['id'];
                }
            }
            $commaSepratedOfferIds = implode(',', $savedCategoryOffersByAdmin);
            $query->andWhere($queryBuilder->expr()->notIn('o.id', $commaSepratedOfferIds));
        }
        $categoryOffersList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return self::changeDataAccordingToOfferHtml($categoryOffersList);
    }

    public static function changeDataAccordingToOfferHtml($categoryOffersList)
    {
        $categoryOffers = array();
        foreach ($categoryOffersList as $offer) {
            $categoryOffers[] = $offer['offers'];
        }
        return $categoryOffers;
    }
    
    public static function getPopularCategories($categoriesLimit = 0, $pageName = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $popularCategories = $queryBuilder
            ->select('p, o, i')
            ->from('KC\Entity\PopularCategory', 'p')
            ->leftJoin('p.category', 'o')
            ->leftJoin('o.categoryicon', 'i')
            ->where('o.deleted=0')
            ->andWhere('o.status= 1')
            ->orderBy("p.total_coupons", "DESC")
            ->setMaxResults($categoriesLimit)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularCategories;
    }

    public static function getCategoryDetails($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $categoryDetails = $queryBuilder
        ->select("c, i, categoryfeaturedimage, categoryheaderimage")
        ->from('KC\Entity\Category', 'c')
        ->LeftJoin("c.categoryicon", "i")
        ->LeftJoin("c.categoryFeaturedImage", "categoryfeaturedimage")
        ->LeftJoin("c.categoryHeaderImage", "categoryheaderimage")
        ->where("c.permaLink =". $queryBuilder->expr()->literal($permalink))
        ->andWhere('c.deleted=0')
        ->andWhere('c.status= 1')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $categoryDetails;

    }
   
    public static function saveCategories($categoryParameter)
    {
        $entityManagerLoacle  = \Zend_Registry::get('emLocale');
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
        if (!empty($categoryIconId)) {
            $category->categoryicon =  $entityManagerLoacle->find('KC\Entity\ImageCategoryIcon', $categoryIconId);
        }
        if (!empty($categoryFeaturedImageId)) {
            $category->categoryFeaturedImage =  $entityManagerLoacle->find('KC\Entity\ImageCategoryIcon', $categoryFeaturedImageId);
        }
        
        if (!empty($categoryHeaderImageId)) {
            $category->categoryHeaderImage = $entityManagerLoacle->find('KC\Entity\ImageCategoryIcon', $categoryHeaderImageId);
        }
        
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $categoryParameter["permaLink"]);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');
        try {
            $entityManagerLoacle->persist($category);
            $entityManagerLoacle->flush();
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
            $category->categoryicon = \Zend_Registry::get('emLocale')->find('KC\Entity\ImageCategoryIcon', $categoryIconId);
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryFeaturedImageId);
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryHeaderImageId);
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '') {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryicon = \Zend_Registry::get('emLocale')->find('KC\Entity\ImageCategoryIcon', $categoryIconId);
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryFeaturedImageId);
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' && $_FILES['categoryHeaderImage']['name'] != '') {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryicon = \Zend_Registry::get('emLocale')->find('KC\Entity\ImageCategoryIcon', $categoryIconId);
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryHeaderImageId);
        } else if ($_FILES['categoryHeaderImage']['name'] != '' && $_FILES['categoryFeaturedImage']['name'] != '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryHeaderImageId);
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryFeaturedImageId);
        } else if ($_FILES['categoryIconNameHidden']['name'] != '' &&  $_FILES['categoryFeaturedImage']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '' ) {
            $categoryIconId = self::
                setCategoryImage(
                    $_FILES['categoryIconNameHidden']['name'],
                    'categoryIconNameHidden',
                    $category,
                    'thumb'
                );
            $category->categoryicon = \Zend_Registry::get('emLocale')->find('KC\Entity\ImageCategoryIcon', $categoryIconId);
        } else if ($_FILES['categoryFeaturedImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryHeaderImage']['name'] == '') {
            $categoryFeaturedImageId = self::
                setCategoryImage(
                    $_FILES['categoryFeaturedImage']['name'],
                    'categoryFeaturedImage',
                    $category,
                    'featured'
                );
            $category->categoryFeaturedImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryFeaturedImageId);
        } else if ($_FILES['categoryHeaderImage']['name'] != '' &&  $_FILES['categoryIconNameHidden']['name'] == '' &&
            $_FILES['categoryFeaturedImage']['name'] == '') {
            $categoryHeaderImageId = self::
                setCategoryImage($_FILES['categoryHeaderImage']['name'], 'categoryHeaderImage', $category, 'header');
            $category->categoryHeaderImage = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\ImageCategoryIcon')->find($categoryHeaderImageId);
        }
        $category->updated_at = new \DateTime('now');
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
                $categoryIcon =  new \KC\Entity\ImageCategoryIcon();
                $categoryIcon->ext = \BackEnd_Helper_viewHelper::getImageExtension($uploadedImage['fileName']);
                $categoryIcon->path = $uploadedImage['path'];
                $categoryIcon->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($uploadedImage['fileName']);
                $categoryIcon->deleted = 0;
                $categoryIcon->created_at = new \DateTime('now');
                $categoryIcon->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($categoryIcon);
                \Zend_Registry::get('emLocale')->flush();
                return $categoryIcon->id;
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
                $queryBuilder->expr()->literal(\BackEnd_Helper_viewHelper::stripSlashesFromString($category["permaLink"]))
            )
            ->set('rp.type', $queryBuilder->expr()->literal("CAT"))
            ->set('rp.exactlink', $queryBuilder->expr()->literal($categoryPermalink));
        $updateRouteLink->where($queryBuilder->expr()->eq("rp.type", $queryBuilder->expr()->literal("CAT")))
            ->andWhere($queryBuilder->expr()->eq("rp.permalink", $queryBuilder->expr()->literal($categoryInfo[0]['permaLink'])))
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
        $query = $queryBuilder->select(
            "c, i, categoryfeaturedimage"
        )
            ->from("KC\Entity\Category", "c")
            ->addSelect(
                "(
                    SELECT count(roc.id) FROM KC\Entity\RefOfferCategory roc LEFT JOIN roc.offers off LEFT JOIN off.shopOffers s  
                        WHERE  off.deleted = 0 and s.deleted = 0 and roc.categories = c.id and off.endDate >
                '".$currentDateAndTime."' and off.discountType='CD' and off.Visability!='MEM'
                ) 
            as totalCoupons"
            )
            ->leftJoin("c.categoryicon", "i")
            ->LeftJoin("c.categoryFeaturedImage", "categoryfeaturedimage")
            ->where("c.deleted=0")
            ->andWhere("c.status= 1")
            ->orderBy("c.featured_category", "DESC");
            
        $categoriesInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $categoriesInformation;
    }

    public static function getCategoryInformation($categoryId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $categoryDetails = $queryBuilder
            ->select(
                "c,i.id as categoryIconId,i.name as categoryiconname,i.path as categoryiconpath,categoryfeaturedimage.name as categoryfeaturedname,
                categoryfeaturedimage.path as categoryfeaturedpath, categoryheaderimage.name as categoryheaderimagename,
                categoryheaderimage.path as categoryheaderimagepath"
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
                    SELECT count(roc) FROM KC\Entity\RefOfferCategory roc LEFT JOIN roc.offers off LEFT JOIN off.shopOffers s  
                        WHERE  off.deleted = 0 and s.deleted = 0 and roc.categories = c.id and off.endDate >
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
    public static function getCategoryList($params = array())
    {
        $srh = @$params["SearchText"] != 'undefined' ? @$params["SearchText"] : '';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $qb = $queryBuilder
            ->from("KC\Entity\Category", "c")
            ->where("c.deleted = 0")
            ->andWhere($queryBuilder->expr()->like('c.name', $queryBuilder->expr()->literal($srh.'%')));

        $request  = \DataTable_Helper::createSearchRequest($params, array('id', 'name', 'status'));
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder->setQueryBuilder($qb)
            ->add('number', 'c.id')
            ->add('text', 'c.name')
            ->add('text', 'c.status');
        $list = $builder->getTable()->getResponseArray();
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
            ->getQuery()
            ->execute();
        $queryBuilderselect = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilderselect
        ->select('c.permaLink')
        ->from("KC\Entity\Category", "c")
        ->where('c.id='.$params['id']);
        $categoriesPermalink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_category_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $categoriesPermalink[0]["permaLink"]);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_data');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('category_'.$permalinkWithoutSpecilaChracter.'_voucherCodes');

    }

    public static function deleteCategory($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $q = $queryBuilder->update('KC\Entity\Category', 'c')
            ->set('c.deleted', 1)
            ->where('c.id='. $params['id'])
            ->getQuery()
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
