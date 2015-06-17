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
            ->from('Core\Domain\Entity\Articles', 'a')
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
            ->leftJoin('o.thumbnail', 'a')
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
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a, stores, related, category, chapter, artimg, shops, thum')
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.storearticles', 'stores')
            ->leftJoin('a.category', 'related')
            ->leftJoin('related.refArticlecategoryRelatedcategory', 'category')
            ->leftJoin('a.articleChapter', 'chapter')
            ->leftJoin('a.articleImage', 'artimg')
            ->leftJoin('a.thumbnail', 'thum')
            ->leftJoin('stores.articleshops', 'shops')
            ->setParameter(1, $permalink)
            ->where('a.permalink = ?1')
            ->setParameter(2, '1')
            ->andWhere('a.publish = ?2')
            ->setParameter(3, '0')
            ->andWhere('a.deleted = ?3')
            ->andWhere("a.publishdate <= ". $queryBuilder->expr()->literal($currentDateTime));
            $articleDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articleDetails;
    }

    public static function getAllArticles($limit = 0)
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p, a, stores, related, category, chapter, artimg, thumb, articlefeaturedimage')
            ->from('KC\Entity\PopularArticles', 'p')
            ->leftJoin('p.articles', 'a')
            ->leftJoin('a.storearticles', 'stores')
            ->leftJoin('a.category', 'related')
            ->leftJoin('related.refArticlecategoryRelatedcategory', 'category')
            ->leftJoin('a.articleChapter', 'chapter')
            ->leftJoin('a.articleImage', 'artimg')
            ->leftJoin('a.thumbnail', 'thumb')
            ->leftJoin('a.featuredImage', 'articlefeaturedimage')
            ->where('a.publish = 1')
            ->andWhere('a.deleted = 0')
            ->andWhere('a.publishdate <='. $queryBuilder->expr()->literal($currentDateTime))
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit);
        $allArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allArticles;
    }

    public static function getAllArticlesForHomePage($limit = 0)
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select("p, a, chapter")
            ->from("KC\Entity\PopularArticles", "p")
            ->leftJoin("p.articles", "a")
            ->leftJoin('a.articleChapter', 'chapter')
            ->where("a.publish = 1")
            ->andWhere("a.deleted= 0")
            ->andWhere($queryBuilder->expr()->lte("a.publishdate", $queryBuilder->expr()->literal($currentDateTime)))
            ->orderBy("p.position", "ASC")
            ->setMaxResults($limit);
        $popularArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return array_slice($popularArticles, 0, 10);
    }

    public static function getMostReasArticlesForPlusOverview($limit = 0)
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p, a, artimg, thumb, featuredImage')
            ->from('KC\Entity\PopularArticles', 'p')
            ->leftJoin('p.articles', 'a')
            ->leftJoin('a.articleImage', 'artimg')
            ->leftJoin('a.thumbnail', 'thumb')
            ->leftJoin('a.featuredImage', 'featuredImage')
            ->where('a.publish = 1')
            ->andWhere('a.deleted = 0')
            ->andWhere('a.publishdate <='. $queryBuilder->expr()->literal($currentDateTime))
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit);
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

    public static function getArticlesList()
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a.id, a.title')
            ->from('KC\Entity\Articles', 'a')
            ->where('a.publish = 1')
            ->andWhere('a.deleted= 0')
            ->andWhere($queryBuilder->expr()->lte("a.publishdate", $queryBuilder->expr()->literal($currentDateTime)))
            ->orderBy('a.publishdate', 'DESC');
        $articlesList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articlesList;
    }
    ###############################################
    ########## END REFACTORED CODE ################
    ###############################################

    CONST ArticleStatusDraft = 0;
    CONST ArticleStatusPublished = 1;


    public static function getAuthorList($site_name)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('user.id, user.firstName, user.lastName')
            ->from('\KC\Entity\User\User', 'user')
            ->leftJoin('user.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->where('user.deleted = 0')
            ->andWhere('w.url ='.  $queryBuilder->expr()->literal($site_name))
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
        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array(
                'title',
                'publishdate',
                'permalink',
                'publish',
                'authorname'
            )
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('text', 'art.title')
            ->add('number', 'art.publishdate')
            ->add('text', 'art.permalink')
            ->add('text', 'art.publish')
            ->add('text', 'art.authorname');
        $result = $builder->getTable()->getResponseArray();
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

        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array(
                'id',
                'title',
                'created_at',
                'publish',
                'authorname'
            )
        );

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('text', 'art.title')
            ->add('text', 'art.created_at')
            ->add('text', 'art.publish')
            ->add('text', 'art.authorname');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }

    public static function getArticleData($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a, stores, related, category, chapter, artimg, shops, articlefeaturedimage, thum')
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.storearticles', 'stores')
            ->leftJoin('a.refArticleCategory', 'related')
            ->leftJoin('related.articlecategory', 'category')
            ->leftJoin('a.articleChapter', 'chapter')
            ->leftJoin('a.articleImage', 'artimg')
            ->leftJoin('a.thumbnail', 'thum')
            ->leftJoin('a.featuredImage', 'articlefeaturedimage')
            ->leftJoin('stores.articleshops', 'shops')
            ->where('a.id ='. $params['id'])
            ->andWhere('a.deleted = 0');
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
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $isDraft = true  ;
        $storeIds = explode(',', $params['selectedRelatedStores']);
        $relatedIds = explode(',', $params['selectedRelatedCategory']);
        $data = new \KC\Entity\Articles();
        $data->deleted = 0;
        $data->created_at = new \DateTime('now');
        $data->updated_at = new \DateTime('now');
        $data->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articleTitle']);
        $data->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articlepermalink']);
        $data->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['articlemetaTitle']);
        $data->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString(trim($params['articlemetaDesc']));
        $data->authorid = $params['authorList'];
        $data->authorname = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['authorNameHidden']);

        if (isset($_FILES['articleImage']['name']) && $_FILES['articleImage']['name'] != '') {

            $result = self::uploadImage('articleImage');

            if (@$result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                    @$result['fileName']
                );
                $articleImage = new \KC\Entity\ImageArticlesIcon();
                $articleImage->ext = @$ext;
                $articleImage->path = @$result['path'];
                $articleImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                $articleImage->deleted = 0;
                $articleImage->created_at = new \DateTime('now');
                $articleImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articleImage);
                $entityManagerLocale->flush();
                $data->articleImage = $entityManagerLocale->find('\KC\Entity\ImageArticlesIcon', $articleImage->id);
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

                $articleThumb = new \KC\Entity\ImageArticlesThumb();
                $articleThumb->ext = @$ext;
                $articleThumb->path = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['path']);
                $articleThumb->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['fileName']);
                $articleThumb->deleted = 0;
                $articleThumb->created_at = new \DateTime('now');
                $articleThumb->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articleThumb);
                $entityManagerLocale->flush();
                $data->thumbnail = $entityManagerLocale->find('\KC\Entity\ImageArticlesThumb', $articleThumb->id);
            } else {
                return false;
            }
        }

        if (isset($_FILES['articleFeaturedImage']['name']) && $_FILES['articleFeaturedImage']['name'] != '') {
            $articleFeaturedImage = self::uploadImage('articleFeaturedImage');

            if ($articleFeaturedImage['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                    $articleFeaturedImage['fileName']
                );

                $articlesFeaturedImage = new \KC\Entity\ImageArticleFeaturedImage();
                $articlesFeaturedImage->ext = @$ext;
                $articlesFeaturedImage->path =
                    @\BackEnd_Helper_viewHelper::stripSlashesFromString($articleFeaturedImage['path']);
                $articlesFeaturedImage->name =
                    @\BackEnd_Helper_viewHelper::stripSlashesFromString($articleFeaturedImage['fileName']);
                $articlesFeaturedImage->deleted = 0;
                $articlesFeaturedImage->created_at = new \DateTime('now');
                $articlesFeaturedImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articlesFeaturedImage);
                $entityManagerLocale->flush();
                $data->featuredImage = $entityManagerLocale->find(
                    '\KC\Entity\ImageArticleFeaturedImage',
                    $articlesFeaturedImage->id
                );
            } else {
                return false;
            }
        }

        $data->featuredImageStatus = 0;
        if (isset($params['featuredimagecheckbox']) && $params['featuredimagecheckbox'] == '1') {
            $data->featuredImageStatus = 1;
        }
        
        $data->plusTitle = '';
        if (isset($params['plusTitle']) && $params['plusTitle'] != '') {
            $data->plusTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['plusTitle']);
        }

        if (isset($params['savePagebtn']) && $params['savePagebtn'] == 'draft') {
            $data->publish = self::ArticleStatusDraft;
            $publishDate = date('Y-m-d');
            $data->publishdate = new \DateTime($publishdate);
        } else if ($params['savePagebtn'] == 'publish' && date('Y-m-d', strtotime($params['publishDate'])) > date('Y-m-d')) {
            $data->publish = self::ArticleStatusPublished;
            $publishdate = date('Y-m-d', strtotime($params['publishDate']));
            $data->publishdate = new \DateTime($publishdate);
            $isDraft  = false ;
        } else {
            $data->publish = self::ArticleStatusPublished;
            $publishdate = date('Y-m-d', strtotime($params['publishDate']));
            $data->publishdate = new \DateTime($publishdate);
            $isDraft  = false ;

        }

        try {
            $entityManagerLocale->persist($data);
            $entityManagerLocale->flush();
            $articleId = $data->__get('id');

            if (!empty($params['title']) && !empty($params['content'])) {
                foreach ($params['title'] as $key => $title) {
                    if (!empty($params['title'][$key]) && !empty($params['content'][$key])) {
                        $chapter = new \KC\Entity\ArticleChapter();
                        $chapter->article = $entityManagerLocale->find('\KC\Entity\Articles', $articleId);
                        $chapter->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'][$key]);
                        $chapter->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['content'][$key]);
                        $chapter->created_at = new \DateTime('now');
                        $chapter->updated_at = new \DateTime('now');
                        $entityManagerLocale->persist($chapter);
                        $entityManagerLocale->flush();
                    }
                }
            }

            if ($storeIds[0] != "") {
                foreach ($storeIds as $storeid) {
                    $relatedstores = new \KC\Entity\RefArticleStore();
                    $relatedstores->relatedstores = $entityManagerLocale->find('\KC\Entity\Articles', $articleId);
                    $relatedstores->articleshops = $entityManagerLocale->find('\KC\Entity\Shop', $articleId);
                    $relatedstores->created_at = new \DateTime('now');
                    $relatedstores->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($relatedstores);
                    $entityManagerLocale->flush();
                    $key = 'shop_moneySavingArticles_'  . $storeid . '_list';
                    \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

                }
            }

            if ($relatedIds[0] != "") {
                foreach ($relatedIds as $relatedid) {
                    $relatedcategories = new \KC\Entity\RefArticleCategory();
                    $relatedcategories->articles = $entityManagerLocale->find('\KC\Entity\Articles', $articleId);
                    $relatedcategories->articlecategory = $entityManagerLocale->find(
                        '\KC\Entity\Articlecategory',
                        $relatedid
                    );
                    $relatedcategories->created_at = new \DateTime('now');
                    $relatedcategories->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($relatedcategories);
                    $entityManagerLocale->flush();
                }
            }
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
            $permalinkWithoutSpecilaChracter = str_replace("-", "", $params['articlepermalink']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('article_'.$permalinkWithoutSpecilaChracter.'_details');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');
            return array('articleId' => $articleId , 'isDraft' => $isDraft ) ;
        } catch(Exception $e) {
            return false;
        }
    }


    public static function editArticle($params)
    {
        $storeIds = explode(',', $params['selectedRelatedStores']);
        $relatedIds = explode(',', $params['selectedRelatedCategory']);
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $data = $entityManagerLocale->find('\KC\Entity\Articles', $params['id']);
        $data->deleted = $data->__get('deleted');
        $data->created_at = $data->__get('updated_at');
        $data->updated_at = new \DateTime('now');
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
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(@$result['fileName']);
                $articleImage = $entityManagerLocale->find('\KC\Entity\ImageArticlesIcon', $data->articleImage->id);
                $articleImage->ext = @$ext;
                $articleImage->path = @$result['path'];
                $articleImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                $articleImage->deleted = 0;
                $articleImage->created_at = new \DateTime('now');
                $articleImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articleImage);
                $entityManagerLocale->flush();
                $data->articleImage = $entityManagerLocale->find('\KC\Entity\ImageArticlesIcon', $articleImage->id);

            } else {
                return false;
            }
        }

        if (isset($_FILES['articleImageSmall']['name']) && $_FILES['articleImageSmall']['name'] != '') {
            $artThumbnail = self::uploadImage('articleImageSmall');
            if (@$artThumbnail['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(@$artThumbnail['fileName']);
                $articleThumb = $entityManagerLocale->find('\KC\Entity\ImageArticlesThumb', $data->thumbnail->id);
                $articleThumb->ext = @$ext;
                $articleThumb->path = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['path']);
                $articleThumb->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($artThumbnail['fileName']);
                $articleThumb->deleted = 0;
                $articleThumb->created_at = new \DateTime('now');
                $articleThumb->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articleThumb);
                $entityManagerLocale->flush();
                $data->thumbnail = $entityManagerLocale->find('\KC\Entity\ImageArticlesThumb', $articleThumb->id);
            } else {
                return false;
            }
        }

        if (isset($_FILES['articleFeaturedImage']['name']) && $_FILES['articleFeaturedImage']['name'] != '') {
            $articleFeaturedImage = self::uploadImage('articleFeaturedImage');

            if ($articleFeaturedImage['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                    $articleFeaturedImage['fileName']
                );

                $articlesFeaturedImage = new \KC\Entity\ImageArticleFeaturedImage();
                $articlesFeaturedImage->ext = $ext;
                $articlesFeaturedImage->path =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($articleFeaturedImage['path']);
                $articlesFeaturedImage->name =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($articleFeaturedImage['fileName']);
                $articlesFeaturedImage->deleted = 0;
                $articlesFeaturedImage->created_at = new \DateTime('now');
                $articlesFeaturedImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($articlesFeaturedImage);
                $entityManagerLocale->flush();
                $data->featuredImage = $entityManagerLocale->find(
                    '\KC\Entity\ImageArticleFeaturedImage',
                    $articlesFeaturedImage->id
                );
            } else {
                return false;
            }
        }

        $data->featuredImageStatus = 0;
        if (isset($params['featuredimagecheckbox']) && $params['featuredimagecheckbox'] == '1') {
            $data->featuredImageStatus = 1;
        }
        
        $data->plusTitle = '';
        if (isset($params['plusTitle']) && $params['plusTitle'] != '') {
            $data->plusTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['plusTitle']);
        }

        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        $permalinkWithoutSpecilaChracter = str_replace("-", "", $params['articlepermalink']);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('article_'.$permalinkWithoutSpecilaChracter.'_details');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');

        if (isset($params['savePagebtn']) && $params['savePagebtn'] == 'draft') {
            $data->publish = self::ArticleStatusDraft;
            $publishDate =
            date(
                'Y-m-d',
                strtotime($params['publishDate'])
            );
            $data->publishdate = new \DateTime($publishDate);
        } else if ($params['savePagebtn'] == 'publish' && date('Y-m-d', strtotime($params['publishDate'])) > date('Y-m-d')) {
            $data->publish = self::ArticleStatusPublished;
            $publishdate = date('Y-m-d', strtotime($params['publishDate']));
            $data->publishdate = new \DateTime($publishdate);
        } else {
            $data->publish = self::ArticleStatusPublished;
            $publishdate = date('Y-m-d', strtotime($params['publishDate']));
            $data->publishdate = new \DateTime($publishdate);
        }

        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('a')
            ->from('\KC\Entity\Articles', 'a')
            ->where('a.id ='.$params['id']);
        $getcategory = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
       
        if (!empty($getcategory[0]['permalink'])) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $queryBuilder = $entityManagerLocale->createQueryBuilder();
            $query = $queryBuilder->select('r')
                ->from('\KC\Entity\RoutePermalink', 'r')
                ->where('r.permalink ='.$queryBuilder->expr()->literal($getcategory[0]['permalink']))
                ->andWhere('r.type ='. $queryBuilder->expr()->literal("ART"));
            $getRouteLink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $updateRouteLink = new KC\Entity\RoutePermalink();
        }
        try
        {
            $entityManagerLocale->persist($data);
            $entityManagerLocale->flush();

            if (!empty($getRouteLink)) {
                $exactLink = 'plus/guidedetail/id/'.$params['id'];
            }

            if ($storeIds[0] != "") {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\RefArticleStore', 'rf')
                    ->where("rf.relatedstores=" .$params['id'])
                    ->getQuery()->execute();

                foreach ($storeIds as $storeid) {
                    $relatedstores = new \KC\Entity\RefArticleStore();
                    $relatedstores->relatedstores = $entityManagerLocale->find('\KC\Entity\Articles', $params['id']);
                    $relatedstores->articleshops = $entityManagerLocale->find('\KC\Entity\Shop', $storeid);
                    $relatedstores->created_at = new \DateTime('now');
                    $relatedstores->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($relatedstores);
                    $entityManagerLocale->flush();
                    $key = 'shop_moneySavingArticles_'  . $storeid . '_list';
                    \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

                }
            }

            if ($relatedIds[0] != "") {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\RefArticleCategory', 'rf')
                    ->where("rf.articles=" .$params['id'])
                    ->getQuery()->execute();

                foreach ($relatedIds as $relatedid) {
                    $relatedcategories = new \KC\Entity\RefArticleCategory();
                    $relatedcategories->articles = $entityManagerLocale->find('\KC\Entity\Articles', $params['id']);
                    $relatedcategories->articlecategory = $entityManagerLocale->find(
                        '\KC\Entity\Articlecategory',
                        $relatedid
                    );
                    $relatedcategories->created_at = new \DateTime('now');
                    $relatedcategories->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($relatedcategories);
                    $entityManagerLocale->flush();
                }
            }

            if (!empty($params['title']) && !empty($params['content'])) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\ArticleChapter', 'rf')
                    ->where("rf.article=" .$params['id'])
                    ->getQuery()->execute();
                foreach ($params['title'] as $key => $title) {
                    if (!empty($params['title'][$key]) && !empty($params['content'][$key])) {
                        $chapter = new \KC\Entity\ArticleChapter();
                        $chapter->article = $entityManagerLocale->find('\KC\Entity\Articles', $params['id']);
                        $chapter->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'][$key]);
                        $chapter->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['content'][$key]);
                        $chapter->created_at = new \DateTime('now');
                        $chapter->updated_at = new \DateTime('now');
                        $entityManagerLocale->persist($chapter);
                        $entityManagerLocale->flush();
                    }
                }
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public static function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH);
        }
        $uploadPath = UPLOAD_IMG_PATH . "article/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $files = $adapter->getFileInfo($file);
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 776, true);
        }
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $adapter->addValidator('Size', false, array('min' => 20, 'max' => '2MB'));
        $name = $adapter->getFileName($file, false);
        $newName = time() . "_" . $name;
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

        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 200, 150, $path);
        }

        $adapter
        ->addFilter(
            new \Zend_Filter_File_Rename(
                array('target' => $cp, 'overwrite' => true)
            ),
            null,
            $file
        );

        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return array(
                "fileName" => $newName,
                "status" => "200",
                "msg" => "File uploaded successfully",
                "path" => $uploadPath
            );

        } else {
            return array(
                "status" => "-1",
                "msg" => "Please upload the valid file"
            );
        }
    }

    public static function searchKeyword($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a.title, a.id')
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
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('DISTINCT(p.id)')
        ->from("\KC\Entity\Articles", "a")
        ->leftJoin('a.category', 'artcat')
        ->leftJoin("artcat.moneysaving", 'ms')
        ->leftJoin("ms.page", 'p')
        ->where('a.id='. $artId);
        $pageIdList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageIdList;
    }

    public static function findCategoryId($artId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('artcat.id')
        ->from("\KC\Entity\Articles", "a")
        ->leftJoin('a.category', 'artcat')
        ->where('a.id='. $artId);
        $pageIdList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageIdList;
    }

    public static function exportarticlelist()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('art.id,art.title,art.created_at,art.publish,art.authorname')
            ->from('\KC\Entity\Articles', 'art')
            ->where('art.deleted = 0')
            ->orderBy("art.id", "DESC");
        $articleList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articleList;

    }
   
    public static function deleteArticles($id)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query= $queryBuilder->update('\KC\Entity\Articles', 'a')
            ->set('a.deleted', '2')
            ->where('a.id=' . $id);
        $query->getQuery()->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');
        return 1;
    }
   
    public static function restoreArticles($id)
    {
        if ($id) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $queryBuilder = $entityManagerLocale->createQueryBuilder();
            $query = $queryBuilder->select('s.id, s.permaLink')
                ->from('\KC\Entity\Articles', 'a')
                ->leftJoin('a.storearticles', 'artStore')
                ->leftJoin('artStore.articleshops', 's')
                ->where('a.id ='. $id);
            $getVal = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
            foreach ($getVal as $st):
                $key = 'shop_moneySavingArticles_'  . $st['id'] . '_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $permalinkWithoutSpecilaChracter = str_replace("-", "", $st['permaLink']);
                $key = 'article_'.$permalinkWithoutSpecilaChracter.'_details';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            endforeach;

            $queryBuilder  = $entityManagerLocale->createQueryBuilder();
            $query= $queryBuilder->update('\KC\Entity\Articles', 'a')
                ->set('a.deleted', '0')
                ->where('a.id=' . $id);
            $query->getQuery()->execute();

        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        $pageIds = self::findPageIds($id);
        $page_ids = array_unique($pageIds);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');
        $catIds = self::findCategoryId($id);
        return $id;
    }


    public static function moveToTrash($id)
    {
        if ($id) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $queryBuilder  = $entityManagerLocale->createQueryBuilder();
            $query= $queryBuilder->update('\KC\Entity\Articles', 'a')
                ->set('a.deleted', '1')
                ->where('a.id=' . $id);
            $query->getQuery()->execute();
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_mostreadMsArticlePage_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('2_recentlyAddedArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('7_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('4_categoriesArticles_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_topOffers_list');
        return $id;
    }

    public static function searchArticles($keyword, $flag, $limit)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder
            ->select('a, img')
            ->from('\KC\Entity\Articles', 'a')
            ->leftJoin('a.articleImage', 'img')
            ->where('a.deleted='. $flag)
            ->andWhere("a.title LIKE '%$keyword%' or a.content LIKE '%$keyword%'")
            ->setMaxResults($limit)
            ->orderBy('a.title', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function MostPopularFashionGuide()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('a, img')
            ->from('\KC\Entity\Articles', 'a')
            ->leftJoin('a.articleImage', 'img')
            ->where('a.deleted= 0')
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function otherhelpfullSavingTips()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('a, img')
            ->from('\KC\Entity\Articles', 'a')
            ->leftJoin('a.thumbnail', 'img')
            ->where('a.deleted= 0')
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function deletechapters($id)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->delete('\KC\Entity\ArticleChapter', 'a')
            ->where('a.id=' . $id);
        $data = $query->getQuery()->execute();
        return $data;
    }

    public static function getAllUrls($id)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('a, c, artStore, s')
            ->from('\KC\Entity\Articles', 'a')
            ->leftJoin('a.storearticles', 'artStore')
            ->leftJoin('artStore.articleshops', 's')
            ->leftJoin('a.category', 'c')
            ->where('a.id ='. $id);
        $article = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $redactie =  User::returnEditorUrl($article['authorid']);
        $urlsArray = array();
        $cetgoriesPage = 'pluscat' .'/' ;
        if (isset($article['permalink'])) {
            $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_plus') . '/'. $article['permalink'];
        }
        if (isset($redactie['permalink']) && strlen($redactie['permalink']) > 0) {
            $urlsArray[] = $redactie['permalink'] ;
        }
        if (isset($article['category']) && count($article['category']) > 0) {
            foreach ($article['category'] as $value) {
                if (isset($value['permalink']) && strlen($value['permalink']) > 0) {
                    $urlsArray[] = $cetgoriesPage . $value['permalink'] ;
                }
            }
        }

        if (isset($article['storearticles']) && count($article['storearticles']) > 0) {
            foreach ($article['storearticles'] as $value) {
                if (isset($value['articleshops']['permaLink']) && strlen($value['articleshops']['permaLink']) > 0) {
                    $urlsArray[] = $value['articleshops']['permaLink'] ;
                }
            }
        }
        return $urlsArray ;
    }

    public static function getArticleId()
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('a.id')
            ->from('\KC\Entity\Articles', 'a')
            ->orderBy("a.id", "DESC")
            ->setMaxResults(1);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
}
