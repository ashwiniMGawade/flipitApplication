<?php
namespace KC\Repository;
class MoneySaving Extends \KC\Entity\MoneySaving
{
    public static function getMostReadArticles($limit, $userId = "")
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'chap.id as chapterId, chap.content as chapterContent, av.id, sum(av.onload) as pop,
            a.title, a.permalink, a.metadescription, a.content, at.path, at.name, ai.name, ai.path, afi.name, afi.path'
        )
        ->from('KC\Entity\ArticleViewCount', 'av')
        ->leftJoin('av.articles', 'a')
        ->leftJoin('a.thumbnail', 'at')
        ->leftJoin('a.articlefeaturedimage', 'afi')
        ->leftJoin('a.articleImage', 'ai')
        ->leftJoin('a.articleChapter', 'chap')
        ->groupBy('av.articles')
        ->orderBy('pop', 'DESC')
        ->where('a.deleted = 0')
        ->andWhere('a.publish = 1');
        if (isset($userId)  && $userId!= "") {
            $query->andWhere('a.authorid ='.$userId.'');
        }
        $mostReadArticles = $query->setMaxResults($limit);
        $mostReadArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $mostReadArticles;
    }

    public static function getRecentlyAddedArticles($articleId, $limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'DISTINCT a.id, a.title, a.permalink, a.content, a.authorid, a.authorname, a.updated_at,
                a.created_at, a.publishdate, ai.path, ai.name,aai.path, aai.name, ac.name, ac.categorytitlecolor'
            )
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.thumbnail', 'ai')
            ->leftJoin('a.articleImage', 'aai')
            ->leftJoin('a.category', 'ac')
            ->leftJoin('a.articleChapter', 'chap')
            ->Where('a.deleted = 0')
            ->andWhere('a.id !='.$articleId)
            ->andWhere('a.publish = 1')
            ->orderBy('a.publishdate', 'DESC')
            ->setMaxResults($limit);
        $recentlyAddedArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $recentlyAddedArticles;
    }

    public static function getAllMoneySavingArticles($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p,m,r,a,ac')
            ->from('KC\Entity\Page', 'p')
            ->leftJoin('p.moneysaving', 'm')
            ->leftJoin('m.articlecategory', 'ac')
            ->leftJoin('ac.refArticleCategory', 'r')
            ->leftJoin('r.articles', 'a')
            ->where("p.permalink ='".$permalink."'")
            ->andWhere('p.publish=1')
            ->andWhere('p.deleted=0')
            ->andWhere('a.deleted=0')
            ->andWhere('a.publish=1')
            ->orderBy('ac.name');
        $allMoneySavingArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allMoneySavingArticles;
    }

    public static function getCategoryWiseArticles($limit = 0)
    {
        $allCategoryDetails = self::getAllArticleCategories();
        $categoryRelatedArticles = self::getCategoryRelatedArticles($allCategoryDetails, $limit);
        return  $categoryRelatedArticles;
    }

    public static function getAllArticleCategories()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('ac.id, ac.name, ac.categorytitlecolor')
        ->from('KC\Entity\Articlecategory', 'ac')
        ->where('ac.deleted=0');
        $allArticleCategoryDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allArticleCategoryDetails;
    }

    public static function getCategoryRelatedArticles($allCategoryDetails, $limit)
    {
        $categoryRelatedArticles = array();
        foreach ($allCategoryDetails as $categoryDetails) {
            $categoryRelatedArticles[$categoryDetails['name']] =
                self::getArticlesRelatedToCategory($categoryDetails['id'], $limit);
        }
        return $categoryRelatedArticles;
    }

    public static function getArticlesRelatedToCategory($categoryId, $limit)
    {
        $allArticlesRelatedToCategory = $limit == 0 ?
        self:: getAllMoneySavingArticlesOfCategory($categoryId) :
            self:: getTopMoneySavingArticlesOfCategory($categoryId, $limit) ;
        return $allArticlesRelatedToCategory;
    }

    public static function getTopMoneySavingArticlesOfCategory($categoryId, $limit)
    {
        return  self::getAllMoneySavingArticlesOfCategory($categoryId, $limit);
    }

    public static function getAllMoneySavingArticlesOfCategory($categoryId, $limit = 0)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'chap.id as chapterId, chap.content as chapterContent, a.id, a.title, a.permalink, a.content,
                a.authorid, a.authorname, a.plusTitle, a.created_at, a.publishdate, ai.path as articleImagePath,
                ai.name as articleImageName, aai.path, aai.name, ac.categorytitlecolor'
            )
            ->from('KC\Entity\Articles', 'a')
            ->leftJoin('a.thumbnail', 'ai')
            ->leftJoin('a.articleImage', 'aai')
            ->leftJoin('a.refArticleCategory', 'r')
            ->leftjoin('a.category', 'ac')
            ->leftJoin('a.articleChapter', 'chap')
            ->where('ac.id ='.$categoryId)
            ->andWhere('a.deleted = 0')
            ->andWhere('a.publish = 1')
            ->setMaxResults($limit)
            ->orderBy('a.publishdate', 'DESC');
        $articles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articles;
    }

    public static function delartCategories($pageid)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\MoneySaving', 'ms')
            ->setParameter(1, $pageId)
            ->where('ms.page = ?1')
            ->getQuery();
            $query->execute();
        return true;
    }

    public static function generateShopMoneySavingGuideArticle($slug, $limit, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a, ai, at, rs, chap')
        ->from('\KC\Entity\Articles', 'a')
        ->leftJoin('a.articleImage', 'ai')
        ->leftJoin('a.thumbnail', 'at')
        ->leftJoin('a.storearticles', 'rs')
        ->leftJoin('a.articleChapter', 'chap')
        ->where('rs.articleshops='.$shopId)
        ->andWhere('a.deleted=0')
        ->setMaxResults($limit);
        $shopMoneySavingGuideArticle = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopMoneySavingGuideArticle;
    }
}