<?php
namespace KC\Repository;

class Articles extends \KC\Entity\Articles
{
    ##############################################
    ####### REFACTORED CODE ######################
    ##############################################
    public static function getAllArticlesCount()
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('COUNT(a)')
            ->from('KC\Entity\Articles', 'a')
            ->setParameter(1, '1')
            ->where('a.publish = ?1')
            ->setParameter(2, '0')
            ->andWhere('a.deleted = ?2')
            ->setParameter(3, $currentDateTime)
            ->andWhere('a.publishdate <= ?3');
        $allArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return isset($allArticles[0][1]) ? $allArticles[0][1] : 0;
    }
 
    public static function getMoneySavingArticles($limit = 0)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p, o, a, chap')
            ->from('\KC\Entity\MoneysavingArticle', 'p')
            ->leftJoin('p.moneysaving', 'o')
            ->leftJoin('o.imagearticle', 'a')
            ->leftJoin('o.articleChapter', 'chap')
            ->setParameter(1, '0')
            ->where('o.deleted = ?1')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit);
        $moneySavingArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $moneySavingArticles;
    }

    public static function getArticleByPermalink($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a, stores, related, category, chapter, artimg, shops')
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.storearticles', 'stores')
            ->leftJoin('a.category', 'related')
            ->leftJoin('related.articlecategory', 'category')
            ->leftJoin('a.articleChapter', 'chapter')
            ->leftJoin('a.imagearticle', 'artimg')
            ->leftJoin('a.imagearticle', 'thum')
            ->leftJoin('stores.articleshops', 'shops')
            ->setParameter(1, $permalink)
            ->where('a.permalink = ?1')
            ->setParameter(2, '1')
            ->andWhere('a.publish = ?2')
            ->setParameter(3, '0')
            ->andWhere('a.deleted = ?3');
            $articleDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articleDetails;
    }

    public static function getAllArticles($limit = 0)
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a, stores, related, category, chapter, artimg')
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.storearticles', 'stores')
            ->leftJoin('a.category', 'related')
            ->leftJoin('related.articlecategory', 'category')
            ->leftJoin('a.articleChapter', 'chapter')
            ->leftJoin('a.imagearticle', 'artimg')
            ->leftJoin('a.imagearticle', 'thum')
            ->setParameter(1, '1')
            ->where('a.publish = ?1')
            ->setParameter(2, '0')
            ->andWhere('a.deleted = ?2')
            ->setParameter(3, $currentDateTime)
            ->andWhere('a.publishdate <= ?3');
            $allArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allArticles;
    }

    public static function generateArticlePermalinks()
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a')
            ->from('KC\Entity\Articles', 'a')
            ->setParameter(1, '1')
            ->where('a.publish = ?1')
            ->setParameter(2, '0')
            ->andWhere('a.deleted = ?2')
            ->setParameter(3, $currentDateTime)
            ->andWhere('a.publishdate <= ?3');
            $allArticlesPermalink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allArticlesPermalink;
    }
    ###############################################
    ########## END REFACTORED CODE ################
    ###############################################

    CONST ArticleStatusDraft = 0;
    CONST ArticleStatusPublished = 1;

    /**
     * This function is used to get author list to display
     * get value from database and display on home page
     *
     * @author Chetan
     *
     */

    public static function getAuthorList($site_name)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('user.id, user.firstName, user.lastName')
            ->from('\KC\Entity\User', 'user')
            ->leftJoin('user.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->setParameter(1, '0')
            ->where('user.deleted = ?1')
            ->setParameter(2, $site_name)
            ->andWhere('rf.url = ?2')
            ->orderBy("user.firstName", "ASC");
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getAllStores($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.name as name,s.id as id')
            ->from('\KC\Entity\Shop', 's')
            ->setParameter(1, '0')
            ->where('s.deleted = ?1')
            ->setParameter(2, $keyword.'%')
            ->andWhere($queryBuilder->expr()->like('s.name', '?2'))
            ->orderBy("s.name", "ASC");
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getArticleList($params)
    {
        $srh =  $params["searchText"]=='undefined' ? '' : $params["searchText"];
        $flag = $params['flag'] ;
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $qb = $queryBuilder
            ->from('KC\Entity\Articles', 'art')
            ->where('art.deleted ='. $flag)
            ->andWhere($queryBuilder->expr()->like('art.title', $queryBuilder->expr()->literal($srh.'%')));

        $request  = \DataTable_Helper::createSearchRequest($params, array('title', 'publishdate', 'publish', 'authorname'));

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('text', 'art.title')
            ->add('number', 'art.publishdate')
            ->add('text', 'art.publish')
            ->add('text', 'art.authorname');
        $data = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $result = \DataTable_Helper::getResponse($data, $request);
        return $result;
    }

    public function getTrashedList($params)
    {
        $srh =  $params["searchText"]=='undefined' ? '' : $params["searchText"];
        $flag = $params['flag'] ;

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $qb = $queryBuilder
            ->from('KC\Entity\Articles', 'art')
            ->where('art.deleted ='. $flag)
            ->andWhere($queryBuilder->expr()->like('art.title', $queryBuilder->expr()->literal($srh.'%')));

        $request  = \DataTable_Helper::createSearchRequest($params, array('id', 'title', 'created_at', 'publish', 'authorname'));

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('number', 'art.id')
            ->add('text', 'art.title')
            ->add('text', 'art.created_at')
            ->add('text', 'art.publish')
            ->add('text', 'art.authorname');
        $data = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $result = \DataTable_Helper::getResponse($data, $request);
        return $result;
    }

    public static function getArticleData($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a, stores, related, category, chapter, artimg, shops')
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.storearticles', 'stores')
            ->leftJoin('a.category', 'related')
            ->leftJoin('related.articlecategory', 'category')
            ->leftJoin('a.articleChapter', 'chapter')
            ->leftJoin('a.imagearticle', 'artimg')
            ->leftJoin('a.imagearticle', 'thum')
            ->leftJoin('stores.articleshops', 'shops')
            ->setParameter(1, $params['id'])
            ->where('a.id = ?1')
            ->setParameter(3, '0')
            ->andWhere('a.deleted = ?3');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($data)) {
            return $data;
        } else {
            return false ;
        }
    }

   
    public static function searchToFiveArticle($keyword, $type)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a')
            ->from('\KC\Entity\Articles', 'a')
            ->setParameter(1, $type)
            ->where('a.deleted = ?1')
            ->setParameter(2, $keyword.'%')
            ->andWhere($queryBuilder->expr()->like('a.title', '?2'));

        $role = \Zend_Auth::getInstance()->getIdentity()->users->id;
        if ($role == '4' || $role == '3') {
            $query->setParameter(3, '0')
                ->andWhere('a.articlesLock= ?2');
        }
        $query->orderBy("a.title", "ASC")->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }


    public static function saveArticle($params)
    {
        $isDraft = true  ;
        $storeIds = explode(',', $params['selectedRelatedStores']);
        $relatedIds = explode(',', $params['selectedRelatedCategory']);
        $data = new KC\Entity\Articles();
        //echo "<pre>"; print_r($data); die();
        $data->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articleTitle']);
        $data->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articlepermalink']);
        $data->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articlemetaTitle']);
        $data->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString(trim($params['articlemetaDesc']));
        $data->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $data->authorid = $params['authorList']; //Auth_StaffAdapter::getIdentity()->id;
        $data->authorname = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['authorNameHidden']);

        if (isset($_FILES['articleImage']['name']) && $_FILES['articleImage']['name'] != '') {

            $result = self::uploadImage('articleImage');

            if (@$result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                    @$result['fileName']
                );

                $data->articleImage->ext = @$ext;
                $data->articleImage->path = @$result['path'];
                $data->articleImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
            } else {
                return false;
            }
        }

        if (isset($_FILES['articleImageSmall']['name']) && $_FILES['articleImageSmall']['name'] != '') {

            $artThumbnail = self::uploadImage('articleImageSmall');

            if (@$artThumbnail['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                    @$artThumbnail['fileName']
                );

                $data->thumbnail->ext = @$ext;
                $data->thumbnail->path = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['path']);
                $data->thumbnail->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['fileName']);
            } else {
                return false;
            }
        }
    /*  $ext = BackEnd_Helper_viewHelper::getImageExtension(@$result['fileName']);
        $data->articleImage->ext = $ext;*/

        if (isset($params['savePagebtn']) && $params['savePagebtn'] == 'draft') {
            $data->publish = self::ArticleStatusDraft;
        } else if($params['savePagebtn'] == 'publish' && date('Y-m-d', strtotime($params['publishDate'])).' '.date('H:i:s', strtotime($params['publishTimehh'])) > date('Y-m-d H:i:s')) {
            
            $data->publish = self::ArticleStatusPublished;
            $data->publishdate = date('Y-m-d', strtotime($params['publishDate'])).' '.date('H:i:s', strtotime($params['publishTimehh']));
            $isDraft  = false ;

        } else {
            
            $data->publish = self::ArticleStatusPublished;
            $data->publishdate = date('Y-m-d', strtotime($params['publishDate'])).' '.date('H:i:s', strtotime($params['publishTimehh']));
            $isDraft  = false ;

        }


        try {
            $data->save();
            $articleId = $data->id;
            /* $route = new RoutePermalink();
            $route->permalink =  $params['articlepermalink'];
            $route->type = 'ART';
            $route->exactlink = 'plus/guidedetail/id/'.$data->id;
            $route->save(); */

            if (!empty($params['title']) && !empty($params['content'])) {
                foreach ($params['title'] as $key => $title) {
                    if (!empty($params['title'][$key]) && !empty($params['content'][$key])) {
                        $chapter = new KC\Entity\ArticleChapter();
                        $chapter->articleId = $data->id;
                        $chapter->title = BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'][$key]);
                        $chapter->content = BackEnd_Helper_viewHelper::stripSlashesFromString($params['content'][$key]);
                        $chapter->save();
                    }
                }
            }

            if ($storeIds[0] != "") {
                foreach ($storeIds as $storeid) {

                    $relatedstores = new KC\Entity\RefArticleStore();
                    $relatedstores->articleid = $data->id;
                    $relatedstores->storeid = $storeid;
                    $relatedstores->save();
                    $key = 'shop_moneySavingArticles_'  . $storeid . '_list';
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

                }
            }

            if ($relatedIds[0] != "") {
                foreach ($relatedIds as $relatedid) {

                    $relatedcategories = new KC\Entity\RefArticleCategory();
                    $relatedcategories->articleid = $data->id;
                    $relatedcategories->relatedcategoryid = $relatedid;
                    $relatedcategories->save();
                }
            }
            $pageIds = self::findPageIds($data->id);
            $artArr = array();
            for ($i=0; $i < count($pageIds); $i++) {
                $artArr[] = $pageIds[$i]['pageid'];
            }
            $page_ids = array_unique($artArr);

            return array('articleId' => $articleId , 'isDraft' => $isDraft ) ;
        } catch(Exception $e) {

            return false;
        }

        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params['articlepermalink']);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('article_'.$permalinkWithoutSpecilaChracter.'_details');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');
    }


    public static function editArticle($params)
    {
        $storeIds = explode(',', $params['selectedRelatedStores']);
        $relatedIds = explode(',', $params['selectedRelatedCategory']);
        $data = Doctrine_Core::getTable("Articles")->find($params['id']);

        $data->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articleTitle']);
        $data->permalink =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articlepermalink']);
        $data->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articlemetaTitle']);
        $md = trim($params['articlemetaDesc']);
        $data->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($md);
        $data->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $data->authorid = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['authorList']);
        $data->authorname = trim(\BackEnd_Helper_viewHelper::stripSlashesFromString($params['authorNameHidden']));

        if (isset($_FILES['articleImage']['name']) && $_FILES['articleImage']['name'] != '') {

            $result = self::uploadImage('articleImage');

            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $data->articleImage->ext = $ext;
                $data->articleImage->path = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                $data->articleImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);

            } else {

                return false;
            }

        }



        if (isset($_FILES['articleImageSmall']['name']) && $_FILES['articleImageSmall']['name'] != '') {

            $artThumbnail = self::uploadImage('articleImageSmall');

            if (@$artThumbnail['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(@$artThumbnail['fileName']);

                $data->thumbnail->ext = @$ext;
                $data->thumbnail->path = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['path']);
                $data->thumbnail->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['fileName']);
            } else {
                return false;
            }
        }


        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');

        $catIds = self::findCategoryId($params['id']);
        $catArr = array();
        for ($i=0;$i<count($catIds);$i++) {
            $catArr[] = $catIds[$i]['relatedcategoryid'];
        }

        
        $pageIds = self::findPageIds($params['id']);
        $artArr = array();
        for ($i=0;$i<count($pageIds);$i++) {
            $artArr[] = $pageIds[$i]['pageid'];
        }
        $page_ids = array_unique($artArr);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params['articlepermalink']);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('article_'.$permalinkWithoutSpecilaChracter.'_details');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');

        /*
        $ext = BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
        $data->articleImage->ext = $ext;*/

        if (isset($params['savePagebtn']) && $params['savePagebtn'] == 'draft') {
            $data->publish = self::ArticleStatusDraft;
        } else if ($params['savePagebtn'] == 'publish' && date('Y-m-d', strtotime($params['publishDate'])).' '.date('H:i:s', strtotime($params['publishTimehh']))  > date('Y-m-d H:i:s')){
            $data->publish = self::ArticleStatusPublished;
            $data->publishdate = date('Y-m-d',strtotime($params['publishDate'])).' '.date('H:i:s',strtotime($params['publishTimehh']));
        } else {
            $data->publish = self::ArticleStatusPublished;
            $data->publishdate = date('Y-m-d', strtotime($params['publishDate'])).' '.date('H:i:s', strtotime($params['publishTimehh']));
        }
        $getcategory = Doctrine_Query::create()->select()->from('Articles')->where('id = '.$params['id'])->fetchArray();
        if (!empty($getcategory[0]['permalink'])) {
            $getRouteLink = Doctrine_Query::create()->select()->from('RoutePermalink')->where('permalink = "'.$getcategory[0]['permalink'].'"')->andWhere('type = "ART"')->fetchArray();
            //$updateRouteLink = Doctrine_Core::getTable('RoutePermalink')->findOneBy('permalink', $getcategory[0]['permaLink'] );
        } else {
            $updateRouteLink = new KC\Entity\RoutePermalink();
        }
        try {
            $data->save();


            if (!empty($getRouteLink)) {
                $exactLink = 'plus/guidedetail/id/'.$data->id;

            }

            if ($storeIds[0] != "") {
                $delRelatedStores = Doctrine_Query::create()->delete("RefArticleStore")->where("articleid = ".$data->id)->execute();
                foreach ($storeIds as $storeid) {

                    $relatedstores = new KC\Entity\RefArticleStore();
                    $relatedstores->articleid = $data->id;
                    $relatedstores->storeid = $storeid;
                    $relatedstores->save();
                    $key = 'shop_moneySavingArticles_'  . $storeid . '_list';
                    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

                }
            }

            if ($relatedIds[0] != "") {
                $delRelatedStores = Doctrine_Query::create()->delete("RefArticleCategory")->where("articleid = ".$data->id)->execute();
                foreach ($relatedIds as $relatedid) {

                    $relatedcategories = new KC\Entity\RefArticleCategory();
                    $relatedcategories->articleid = $data->id;
                    $relatedcategories->relatedcategoryid = $relatedid;
                    $relatedcategories->save();
                }
            }

            if (!empty($params['title']) && !empty($params['content'])) {
                $delChapters = Doctrine_Query::create()->delete("ArticleChapter")->where("articleId = ".$data->id)->execute();
                foreach ($params['title'] as $key => $title) {
                    if (!empty($params['title'][$key]) && !empty($params['content'][$key])) {
                        $chapter = new KC\Entity\ArticleChapter();
                        $chapter->articleId = $data->id;
                        $chapter->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'][$key]);
                        $chapter->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['content'][$key]);
                        $chapter->save();
                    }
                }
            }

            return true;
        }catch(Exception $e){
            return false;
        }



    }
    /**
     * upload image
     * @param $_FILES[index]  $file
     */
    public static function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH);
        }

        // generate upload path for images related to shop
        $uploadPath = UPLOAD_IMG_PATH . "article/";
        $adapter = new Zend_File_Transfer_Adapter_Http();

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

        $path = ROOT_PATH . $uploadPath . "thum_article_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 64, 32, $path);


        $path = ROOT_PATH . $uploadPath . "thum_article_samll_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 0, 46, $path);

        $path = ROOT_PATH . $uploadPath . "thum_article_medium_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 0, 60, $path);


        $path = ROOT_PATH . $uploadPath . "thum_article_big_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 960, 340, $path);


        $path = ROOT_PATH . $uploadPath . "thum_article_all_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 110, 0, $path);


        /**
         *   generating thumnails for upload logo if file in shop logo
         */
        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 200, 150, $path);
        }

        // apply filter to rename file name and set target
        $adapter
        ->addFilter(
            new Zend_Filter_File_Rename(
                array('target' => $cp, 'overwrite' => true)
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

    public static function searchKeyword($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a.title')
            ->from('\KC\Entity\Articles', 'a')
            ->where('a.deleted ='. $flag)
            ->andWhere($queryBuilder->expr()->like('a.title', $queryBuilder->expr()->literal($keyword.'%')))
            ->orderBy("a.title", "ASC")
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function findPageIds($artId)
    {
        $pageIdList = Doctrine_Query::create()->select('ms.pageid')
        ->from("MoneySaving ms")
        ->leftJoin("ms.refarticlecategory refac")
        ->where('refac.articleid='.$artId.'')
        ->fetchArray();
        return $pageIdList;

    }

    /**
     * Get Category Id
     * @author Raman
     * @version 1.0
     */

    public static function findCategoryId($artId)
    {
        $catIdList = Doctrine_Query::create()->select('rac.relatedcategoryid')
        ->from("RefArticleCategory rac")
        ->where('rac.articleid='.$artId.'')
        ->fetchArray();
        return $catIdList;

    }

    /**
     * export article list
     * @author chetan
     * @version 1.0
     */

    public static function exportarticlelist()
    {
        $articleList = Doctrine_Query::create()->select('art.id,art.title,art.created_at,art.publish,art.authorname')
        ->from("Articles  art")
        ->where("art.deleted=0")
        ->orderBy("art.id DESC")
        ->fetchArray();
        return $articleList;

    }
    /**
     * delete page permanently
     * @author jsingh
     * @version 1.0
     */

    public static function deleteArticles($id)
    {
            $getVal = Doctrine_Query::create()->from('Articles a')
                ->leftJoin('a.relatedstores')
                ->where('a.id='.$id)->fetchArray();

            foreach ($getVal[0]['relatedstores'] as $st):
                $key = 'shop_moneySavingArticles_'  . $st['storeid'] . '_list';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $permalinkWithoutSpecilaChracter = str_replace("-", "", $st['permalink']);
                $key = 'article_'.$permalinkWithoutSpecilaChracter.'_details';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            endforeach;


        $O = Doctrine_Query::create()->update('Articles')->set('deleted', '2')
        ->where('id=' . $id);
        $O->execute();
        //call cache function



        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');

        $pageIds = self::findPageIds($id);
            $artArr = array();
            for($i=0;$i<count($pageIds);$i++) {
                $artArr[] = $pageIds[$i]['pageid'];
            }
            $page_ids = array_unique($artArr);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');

            $catIds = self::findCategoryId($id);
            $catArr = array();
            for($i=0;$i<count($catIds);$i++){
                $catArr[] = $catIds[$i]['relatedcategoryid'];
            }


        return 1;
    }
    /**
     * restore shop by id
     * @param $id
     * @author jsingh
     * @version 1.0
     */
    public static function restoreArticles($id)
    {
        if ($id) {
            //update status of record by id(deleted=0)
            $getVal = Doctrine_Query::create()->from('Articles a')
            ->leftJoin('a.relatedstores')
            ->where('a.id='.$id)->fetchArray();

            foreach ($getVal[0]['relatedstores'] as $st):
            $key = 'shop_moneySavingArticles_'  . $st['storeid'] . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'article_'.$st['permalink'].'_details';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            endforeach;

            $O = Doctrine_Query::create()->update('Articles')->set('deleted', '0')
            ->where('id=' . $id);
            $O->execute();


        } else {
            $id = null;
        }
        //call cache function

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');

        $pageIds = self::findPageIds($id);
            $artArr = array();
            for($i=0;$i<count($pageIds);$i++) {
                $artArr[] = $pageIds[$i]['pageid'];
            }
            $page_ids = array_unique($artArr);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');


            $catIds = self::findCategoryId($id);
            $catArr = array();
            for($i=0;$i<count($catIds);$i++){
                $catArr[] = $catIds[$i]['relatedcategoryid'];
            }

            


        return $id;
    }

    /**
     * Move record in trash.
     * @param integer $id
     * @version 1.0
     * @author  jsingh
     * @return integer $id
     */
    public static function moveToTrash($id)
    {
        if ($id) {
            //find record by id
            $u = Doctrine_Core::getTable("Articles")->find($id);
            $getVal = Doctrine_Query::create()->from('Articles a')
                ->leftJoin('a.relatedstores')
                ->where('a.id='.$id)->fetchArray();

            foreach ($getVal[0]['relatedstores'] as $st):
                $key = 'shop_moneySavingArticles_'  . $st['storeid'] . '_list';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $key = 'article_'.$st['permalink'].'_details';
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            endforeach;

            $u->delete();


        } else {

            $id = null;
        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        $pageIds = self::findPageIds($id);
            $artArr = array();
            for($i=0;$i<count($pageIds);$i++) {
                $artArr[] = $pageIds[$i]['pageid'];
            }
            $page_ids = array_unique($artArr);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');

            $catIds = self::findCategoryId($id);
            $catArr = array();
            for($i=0;$i<count($catIds);$i++){
                $catArr[] = $catIds[$i]['relatedcategoryid'];
            }



        return $id;

    }



    /*********************************************Front fuction for displaying articles********************************************/
    public static function searchArticles($keyword, $flag,$limit)
    {
        $data = Doctrine_Query::create()->select()
                                        ->from("Articles art")
                                        ->leftJoin("art.articleImage")
                                        ->where('art.deleted=' .$flag )
                                        ->andWhere("art.title LIKE '%$keyword%' or art.content LIKE '%$keyword%'")
                                        ->limit($limit)
                                        ->orderBy("art.title ASC")
                                        ->fetchArray();
        return $data;

    }

    /**
     * Most popular Fashion Guide article fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */

    public static function MostPopularFashionGuide()
    {
        $data = Doctrine_Query::create()
        ->select('a.*,img.*')
        ->from('Articles a')
        ->leftJoin('a.articleImage img')
        ->where('a.deleted= 0')
        ->limit('5')
        ->fetchArray();
        return $data;
    }

    /**
     * Other helpful money saving fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */

    public static function otherhelpfullSavingTips()
    {
        $data = Doctrine_Query::create()
        ->select('a.*,img.*')
        ->from('Articles a')
        ->leftJoin('a.thumbnail img')
        ->where('a.deleted= 0')
        ->limit('5')
        ->fetchArray();
        return $data;
    }

    /**
     * Other helpful money saving fronthome page
     * @author kkumar
     * @version 1.0
     * @return array $data
     */

    public static function deletechapters($id)
    {
        $data = Doctrine_Query::create()
                                ->delete('ArticleChapter a')
                                ->where('a.id ='.$id)
                                ->execute();

        return $data;
    }

    /**
     * @author Raman
     * @return Article permalinks
     */




    /**
     * getAllUrls
     *
     * returns the all the urls related to the article like  realted store pages, realted category pages
     * @param integer $id article id
     * @author Surinderpal Singh
     * @return array array of urls
     */
    public static function getAllUrls($id)
    {
        $article  =Doctrine_Query::create()->select("a.permalink,a.authorid,c.permalink,s.permaLink")
                        ->from('Articles a')
                        ->leftJoin("a.shop s")
                        ->leftJoin("a.articlecategory c")
                        ->where("a.id=? " , $id)
                        ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

        # redactie permalink
        $redactie =  User::returnEditorUrl($article['authorid']);

        $urlsArray = array();

        $cetgoriesPage = 'pluscat' .'/' ;

        # check for article permalink
        if(isset($article['permalink'])) {
            $urlsArray[] = FrontEnd_Helper_viewHelper::__link('link_plus') . '/'. $article['permalink'];
        }

        # check if an editor  has permalink then add it into array
        if(isset($redactie['permalink']) && strlen($redactie['permalink']) > 0 ) {
            $urlsArray[] = $redactie['permalink'] ;
        }

        # check an article has one or more categories
        if(isset($article['articlecategory']) && count($article['articlecategory']) > 0) {

            # traverse through all catgories
            foreach($article['articlecategory'] as $value) {
                # check if a category has permalink then add it into array
                if(isset($value['permalink']) && strlen($value['permalink']) > 0 ) {
                    $urlsArray[] = $cetgoriesPage . $value['permalink'] ;
                }
            }
        }

        # check for related shops
        if(isset($article['shop']) && count($article['shop']) > 0) {
            # traverse through all shops
            foreach( $article['shop'] as $value) {
                # check if a shops has permalink then add it into array
                if(isset($value['permaLink']) && strlen($value['permaLink']) > 0 ) {
                    $urlsArray[] = $value['permaLink'] ;
                }
            }
        }
        return $urlsArray ;
    }
}
