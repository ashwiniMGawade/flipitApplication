<?php
namespace KC\Repository;

class Articlecategory extends \Core\Domain\Entity\Articlecategory
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
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\Articlecategory', 'rf')
            ->getQuery()
            ->execute();
    }
    public static function deleteReferenceArticleCategory()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->delete('KC\Entity\RefArticlecategoryRelatedcategory', 'rf')
            ->getQuery()
            ->execute();
    }

    public static function getAllUrls($id)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('ac.permalink,a.permalink')
            ->from('\Core\Domain\Entity\Articlecategory', 'ac')
             ->leftJoin("ac.articles", "a")
            ->where("ac.id=". $id);
        $getRouteLink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $urlsArray = array();
        if (isset($artcileData['permalink'])) {
            $urlsArray[] = $artcileData['permalink'];
        }
        if (isset($artcileData['articles']) && count($artcileData['articles']) > 0) {
            foreach ($artcileData['articles'] as $artcileValue) {
                if (isset($artcileValue['permalink']) && strlen($artcileValue['permalink']) > 0) {
                    $urlsArray[] = $artcileValue['permalink'];
                }
            }
        }
        return $urlsArray ;
    }
    ####################### Refactored ##############################

    public function addcategory($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $artCategory = new \KC\Entity\Articlecategory();
        $artCategory->name = strtolower(\BackEnd_Helper_viewHelper::stripSlashesFromString($params['categoryName']));
        $artCategory->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['permaLink']);
        $artCategory->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['metaTitle']);
        $artCategory->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['metaDescription']);
        $artCategory->description = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['description']);
        $artCategory->categorytitlecolor = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['titlecolor']);
        $artCategory->deleted = 0;
        $artCategory->created_at = new \DateTime('now');
        $artCategory->updated_at = new \DateTime('now');
        if ($_FILES['categoryIconNameHidden']['name']!=null) {
            $result = self::uploadImage('categoryIconNameHidden');
            if ($result['status'] == '200') {
                $articleCategoryIcon = new \KC\Entity\ImageArticleCategoryIcon();
                $categoryImageExtension = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $articleCategoryIcon->ext = $categoryImageExtension;
                $articleCategoryIcon->path = $result['path'];
                $articleCategoryIcon->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                $articleCategoryIcon->deleted = 0;
                $articleCategoryIcon->created_at = new \DateTime('now');
                $articleCategoryIcon->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articleCategoryIcon);
                $entityManagerLocale->flush();
                $artCategory->ArtCatIcon = $entityManagerLocale->find(
                    'KC\Entity\ImageArticleCategoryIcon',
                    $articleCategoryIcon->id
                );
            } else {
                return false;
            }
        }
        try {
            $entityManagerLocale->persist($artCategory);
            $entityManagerLocale->flush();
            $categoryId = $artCategory->id;

            foreach ($params['selectedCategoryies'] as $relatedCategory) {
                $refRelatedCategory = new \KC\Entity\RefArticlecategoryRelatedcategory();
                $refRelatedCategory->articlecategory = $entityManagerLocale->find(
                    'KC\Entity\Articlecategory',
                    $categoryId
                );
                $refRelatedCategory->category = $entityManagerLocale->find('KC\Entity\Category', $relatedCategory);
                $refRelatedCategory->deleted = 0;
                $refRelatedCategory->created_at = new \DateTime('now');
                $refRelatedCategory->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($refRelatedCategory);
                $entityManagerLocale->flush();
            }
            return $categoryId;
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
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH);
        }

        // generate upload path for images related to shop
        $uploadPath = UPLOAD_IMG_PATH . "articlecategory/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();

        // generate real path for upload path
        $rootPath = ROOT_PATH . $uploadPath;

        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 776, true);
        }

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

        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 132, 95, $path);

        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 64, 32, $path);


        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_samll_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 60, 45, $path);

        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_medium_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 220, 165, $path);


        $path = ROOT_PATH . $uploadPath . "thum_articleCategory_big_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 250, 0, $path);

        /**
         *   generating thumnails for upload logo if file in shop logo
         */
        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;

            \BackEnd_Helper_viewHelper::resizeImage(
                $files[$file],
                $newName,
                200,
                150,
                $path
            );
        }

        // apply filter to rename file name and set target
        $adapter
            ->addFilter(
                new \Zend_Filter_File_Rename(
                    array(
                        'target' => $cp,
                        'overwrite' => true
                    )
                ),
                null,
                $file
            );

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

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->from('\Core\Domain\Entity\Articlecategory', 'cat')
        ->where('cat.deleted = 0')
        ->andWhere($queryBuilder->expr()->like("cat.name", $queryBuilder->expr()->literal($srh.'%')));
        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array('cat.name','cat.permalink','cat.metatitle')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('text', 'cat.name')
            ->add('text', 'cat.permalink')
            ->add('text', 'cat.metatitle');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }

    public static function getartCategories()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
        ->from('\Core\Domain\Entity\Articlecategory', 'cat')
        ->where('cat.deleted = 0');
        $request  = \DataTable_Helper::createSearchRequest(
            array(),
            array('cat.id','cat.name','cat.permalink','cat.metatitle')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('text', 'cat.name')
            ->add('text', 'cat.permalink')
            ->add('text', 'cat.metatitle');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }


    public static function searchKeyword($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('c.name as name')
            ->from('\Core\Domain\Entity\Articlecategory', 'c')
            ->where('c.deleted = 0')
            ->andWhere($queryBuilder->expr()->like('c.name', $queryBuilder->expr()->literal($keyword.'%')))
            ->orderBy("c.name", "ASC")
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public function editCategory($params, $type)
    {

        if ($type == 'post') {

            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $edit = $entityManagerLocale->find('\KC\Entity\Articlecategory', $params['id']);

            $edit->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['categoryName']);
            $edit->permalink =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['permaLink']);
            $edit->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['metaTitle']);
            $edit->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['metaDescription']);
            $edit->description = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['description']);
            $edit->categorytitlecolor = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['titlecolor']);
            $edit->deleted = 0;
            $edit->created_at = $edit->created_at;
            $edit->updated_at = new \DateTime('now');
            //check this code when test
            if (isset($_FILES['categoryIconNameHidden']) && $_FILES['categoryIconNameHidden']['name'] != '') {

                $result = self::uploadImage('categoryIconNameHidden');

                if ($result['status'] == '200') {
                    $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']
                    );

                    $articleCategoryIcon = $entityManagerLocale->find(
                        'KC\Entity\ImageArticleCategoryIcon',
                        $edit->ArtCatIcon->id
                    );
                    $categoryImageExtension = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                    $articleCategoryIcon->ext = $categoryImageExtension;
                    $articleCategoryIcon->path = $result['path'];
                    $articleCategoryIcon->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                    $articleCategoryIcon->deleted = 0;
                    $articleCategoryIcon->created_at = $edit->ArtCatIcon->created_at;
                    $articleCategoryIcon->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($articleCategoryIcon);
                    $entityManagerLocale->flush();
                    $artCategory->ArtCatIcon = $entityManagerLocale->find(
                        'KC\Entity\ImageArticleCategoryIcon',
                        $articleCategoryIcon->id
                    );

                } else {
                    return false;
                }
            }
            
            $queryBuilder = $entityManagerLocale->createQueryBuilder();
            $query = $queryBuilder->select('a')
                ->from('\Core\Domain\Entity\Articlecategory', 'a')
                ->where('a.id ='.$edit->id);
            $getPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!empty($getPage[0]['permalink'])) {
                $repo = $entityManagerLocale->getRepository('KC\Entity\RoutePermalink');
                $updateRouteLink = $repo->findBy(array('permalink' =>  $getPage[0]['permalink']));
                
            } else {
                $updateRouteLink = new RoutePermalink();
            }
           
            //$ext = BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
            //$edit->ArtCatIcon->ext = $ext;

            try {
                $entityManagerLocale->persist($edit);
                $entityManagerLocale->flush();

                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\RefArticlecategoryRelatedcategory', 'rf')
                    ->where("rf.articlecategory=" . $params['id'])
                    ->getQuery()->execute();

                foreach ($params['selectedCategoryies'] as $relatedCategory) {
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    $query = $queryBuilder->select('rf')
                        ->from('\Core\Domain\Entity\RefArticlecategoryRelatedcategory', 'rf')
                        ->where("rf.category=" .$relatedCategory)
                        ->andWhere("rf.articlecategory=" . $params['id']);
                    $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                    if (sizeof($data) == 0) {
                        $refRelatedCategory = new \KC\Entity\RefArticlecategoryRelatedcategory();
                        $refRelatedCategory->articlecategory = $entityManagerLocale->find(
                            'KC\Entity\Articlecategory',
                            $params['id']
                        );
                        $refRelatedCategory->category = $entityManagerLocale->find('KC\Entity\Category', $relatedCategory);
                        $refRelatedCategory->deleted = 0;
                        $refRelatedCategory->created_at = new \DateTime('now');
                        $refRelatedCategory->updated_at = new \DateTime('now');
                        $entityManagerLocale->persist($refRelatedCategory);
                        $entityManagerLocale->flush();
                    }
                }
                //that code not needed please delete after migrations
                $pageIds = self::findPageId($params['id']);
                $artArr = array();
                for ($i=0; $i < count($pageIds); $i++) {
                    $artArr[] = $pageIds[$i]['pageid'];
                }
                $page_ids = array_unique($artArr);
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');
                return true;
            } catch (Exception $e) {
                return false;
            }

        } else {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('artcat, icon, related, articlecategory')
                ->from('\Core\Domain\Entity\Articlecategory', 'artcat')
                ->leftJoin('artcat.ArtCatIcon', "icon")
                ->leftJoin('artcat.refArticlecategoryRelatedcategory', 'related')
                ->leftJoin('related.category', 'articlecategory')
                ->where('artcat.id='.$params['id']);
            $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $data;
        }
    }

    public static function permanentDeleteArticleCategory($id)
    {
        if ($id) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\RefArticlecategoryRelatedcategory', 'rf')
                    ->where("rf.articlecategory=" .$id)
                    ->getQuery()->execute();

                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\Articlecategory', 'rf')
                    ->where("rf.id=" .$id)
                    ->getQuery()->execute();
        } else {

            $id = null;
        }

        //that code not needed please delete after migrations
        $pageIds = self::findPageId($id);
        $artArr = array();
        for ($i=0; $i < count($pageIds); $i++) {
            $artArr[] = $pageIds[$i]['pageid'];
        }
        $page_ids = array_unique($artArr);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');

        return $id;

    }

    public static function exportarticlecategorylist()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('cat.id,cat.name,cat.permalink,cat.metatitle')
            ->from('\Core\Domain\Entity\Articlecategory', 'cat')
            ->where("cat.deleted= 0")
            ->orderBy("cat.id", "DESC");
        $categoryList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $categoryList;
    }

   
    public static function getArticleCategory($catid)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('c.name, c.metatitle,c.permalink, c.metadescription,c.description, ai.path, ai.name')
            ->from('\Core\Domain\Entity\Articlecategory', 'c')
            ->leftJoin("c.ArtCatIcon", "ai")
            ->where("c.deleted=0")
            ->andWhere("c.permaLink = '".$catid."'");
        $category = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $category;
    }

    public static function findPageId($catId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p.id')
            ->from('\Core\Domain\Entity\MoneySaving', 'ms')
            ->leftJoin('ms.page', 'p')
            ->where('ms.articlecategory='. $catId);
        $pageIdList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageIdList;
    }
}