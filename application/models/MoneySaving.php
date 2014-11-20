<?php

/**
 * MoneySaving
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ## Raman ## <##EMAIL##>
 * @version    1.0
 */

class MoneySaving extends BaseMoneySaving
{
    #####################################################
    ############# REFACTORED CODE #######################
    ####################################################
    /**
     * Function generate most read Articles.
     *
     * @version 1.0
     */
    public static function getMostReadArticles($limit, $userId = "")
    {
        $mostReadArticles = Doctrine_Query::create()
        ->select('chap.*,av.id, av.articleid, (sum(av.onload)) as pop, a.*, at.path, at.name, ai.name, ai.path,afi.name, afi.path')
        ->from('ArticleViewCount av')
        ->leftJoin('av.articles a')
        ->leftJoin('a.thumbnail at')
        ->leftJoin('a.articlefeaturedimage afi')
        ->leftJoin('a.articleImage ai')
        ->leftJoin('a.chapters chap')
        ->groupBy('av.articleid')
        ->orderBy('pop DESC')
        ->where('a.deleted = 0')
        ->andWhere('a.publish = 1');
        if (isset($userId)  && $userId!= "") {
            $mostReadArticles->andWhere('a.authorId ='.$userId.'');
        }
        $mostReadArticles = $mostReadArticles->limit($limit)->fetchArray();
        return $mostReadArticles;
    }
    public static function getRecentlyAddedArticles($articleId, $limit)
    {
        $recentlyAddedArticles = Doctrine_Query::create()
            ->select(
                'DISTINCT a.id, a.title, a.permalink, a.content, a.authorid, a.authorname, a.updated_at,
                a.created_at, a.publishdate, ai.path, ai.name,aai.path, aai.name, ac.name, ac.categorytitlecolor'
            )
            ->from('Articles a')
            ->leftJoin('a.thumbnail ai')
            ->leftJoin('a.articleImage aai')
            ->leftJoin('a.articlecategory ac')
            ->leftJoin('a.chapters chap')
            ->Where('a.deleted = 0')
            ->andWhere('a.id !='.$articleId)
            ->orderBy('a.publishdate DESC')
            ->limit($limit)
            ->fetchArray();
        return $recentlyAddedArticles;
    }
   
    public static function getAllMoneySavingArticles($permalink)
    {
        $allMoneySavingArticles = Doctrine_Query::create()
            ->select(
                'p.pageAttributeId,m.pageid,m.categoryid,r.articleid,
                r.relatedcategoryid,a.title, ac.name,a.permalink, ac.permalink'
            )
            ->from('page p')
            ->leftJoin('p.moneysaving m')
            ->leftJoin('m.articlecategory ac')
            ->leftJoin('m.refarticlecategory r')
            ->leftJoin('r.articles a')
            ->where("p.permaLink ='".$permalink."'")
            ->andWhere('p.publish=1')
            ->andWhere('p.deleted=0')
            ->andWhere('a.deleted=0')
            ->andWhere('a.publish=1')
            ->orderBy('ac.name')
            ->fetchArray();
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
        $allArticleCategoryDetails = Doctrine_Query::create()
        ->select('id, name, categorytitlecolor')
        ->from('Articlecategory ac')
        ->where('ac.deleted=0')
        ->fetchArray();
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
        $articles = Doctrine_Query::create()
            ->select(
                'chap.*, a.id, a.title, a.plusTitle, a.permalink, a.content, a.authorid, 
                    a.authorname, a.created_at, a.publishdate, ai.path, ai.name,aai.path, aai.name, ac.categorytitlecolor'
            )
            ->from('Articles a')
            ->leftJoin('a.thumbnail ai')
            ->leftJoin('a.articleImage aai')
            ->leftJoin('a.refarticlecategory r')
            ->leftjoin('a.articlecategory ac')
            ->leftJoin('a.chapters chap')
            ->where('r.relatedcategoryid ='.  "'$categoryId'")
            ->andWhere('a.deleted=0')
            ->andWhere('a.publish = 1')
            ->limit($limit)
            ->orderBy('a.publishdate DESC')
            ->fetchArray();
        return $articles;
    }

    public static function getPopularArticlesAndCategory()
    {
        return self::getPopularArticlesOfCategory();
    }

    public static function getPopularArticlesOfCategory()
    {
        $popularArticles = Doctrine_Query::create()
            ->select(
                'p.*, chap.*, a.id, a.title, a.plusTitle, a.permalink, a.content, a.authorid, 
                a.authorname, a.created_at, a.publishdate, ai.path, ai.name,aai.path, aai.name,
                ac.categorytitlecolor, ac.name'
            )
            ->from('PopularArticles p')
            ->leftJoin('p.articles a')
            ->leftJoin('a.thumbnail ai')
            ->leftJoin('a.articleImage aai')
            ->leftJoin('a.refarticlecategory r')
            ->leftjoin('a.articlecategory ac')
            ->leftJoin('a.chapters chap')
            ->andWhere('a.deleted=0')
            ->andWhere('a.publish = 1')
            ->orderBy('p.position ASC')
            ->fetchArray();
        return $popularArticles;
    }

 ################## REFACTORED #######################
    /**
     * Get article for Money saving article from database
     * @author Raman
     * @version 1.0
     * @return array $data
     */
    public static function getSaving()
    {
        $data = Doctrine_Query::create()
        ->select('p.id,o.title,p.type,p.position,p.articleId')
        ->from('MoneysavingArticle p')
        ->leftJoin('p.article o')
        ->orderBy('p.position ASC')->fetchArray();

        return $data;

    }

    // function used for delete category
    public static function delartCategories($pageid)
    {

        $a = Doctrine_Query::create()->delete('MoneySaving')
        ->where('pageid=' . $pageid)->execute();
        //change position by 1 of each below element
        return true;

    }

    /**
     * All the money saving articles under all the categories on money saving page
     * @author kraj
     * @version 1.0
     */
    public static function getAllMoneySavingArticleForSearch($keyword,$limit)
    {
        $papularArticle = Doctrine_Query::create()->select('DISTINCT a.*,ai.*,chap.*')
        ->from('Articles a')
        ->leftJoin('a.thumbnail ai')
        ->innerJoin('a.refarticlecategory r')
        ->innerJoin('r.moneysaving m')
        ->leftJoin('a.chapters chap')->limit(6)
        ->where('a.deleted= 0')
        ->andWhere("a.title LIKE '%?%' or a.content LIKE '%?%'",$keyword,$keyword)
        ->fetchArray();
        return $papularArticle;

    }

    /**
     * generate all Money Saving Articles
     * @author Raman
     * @version 1.0
     */


    public static function generateMoneySavingArticles($id)
    {
        $papularArticle = Doctrine_Query::create()->select('DISTINCT a.id')
        ->from('Articles a')
        ->innerJoin('a.refarticlecategory r')
        ->innerJoin('r.moneysaving m')
        ->innerJoin('m.page p')
        ->where('p.id =' . "'$id'")
        ->fetchArray();
        return $papularArticle;

    }


    /**
     * generate papular Articles at the moment by formula
     * @author Raman
     * @version 1.0
     */

    public static function getMostpopularArticles($id, $limit)
    {
        $format = 'Y-m-j H:m:s';
        $date = date($format);
        $past1Day = date($format, strtotime('-1 day' . $date));
        $start = date('Y-m-d').' 00:00:00';
        $end = date('Y-m-d').' 23:59:59';
        $nowDate = $date;
        $papularArticle = Doctrine_Query::create()
        ->select('chap.*,av.articleid, a.permalink, ((sum(av.onclick)) / (DATEDIFF(NOW(),a.publishdate))) as pop, a.title,a.content, a.authorname, a.authorid,  a.publishdate, ai.path, ai.name')
        ->from('ArticleViewCount av')
        ->leftJoin('av.articles a')
        ->leftJoin('a.articleImage ai')
        ->leftJoin('a.chapters chap')
        ->where('av.updated_at >=' . "'$start' AND av.updated_at <="."'$end'")
        ->limit($limit)
        ->groupBy('av.articleid')
        ->orderBy('pop DESC')
        ->fetchArray();
        //echo "<pre>";
        //print_r($papularArticle); die;
        return $papularArticle;
    }
    /**
     * generate MS Articles related to a shop
     * @author Raman
     * @version 1.0
     */

    public static function generateShopMoneySavingGuideArticle($slug, $limit, $shopId)
    {
        $shopMoneySavingGuideArticle = Doctrine_Query::create()
        ->select('chap.*,a.permalink,a.title,a.content, a.authorname, a.authorid, ai.path, ai.name, at.path, at.name')
        ->from('Articles a')
        ->leftJoin('a.articleImage ai')
        ->leftJoin('a.thumbnail at')
        ->leftJoin('a.relatedstores rs')
        ->leftJoin('a.chapters chap')
        ->where('rs.storeid='.$shopId)
        ->andWhere('a.deleted=0')
        ->limit($limit)
        ->fetchArray();
        return $shopMoneySavingGuideArticle;
    }



    /**
     * generate most read Articles of a specific category ever
     * @author Raman
     * @version 1.0
     */
    public static function generateMostReadArticleOfcategory($catId, $limit=6)
    {
        $moneySavingArticles = self::generateMoneySavingArticlesOfcategory($catId);
        $artArr = array();
        for($i=0;$i<count($moneySavingArticles);$i++){
            $artArr[] = $moneySavingArticles[$i]['id'];
        }

        $mostReadArticle = Doctrine_Query::create()->select('chap.*, aai.path, aai.name, ai.path, ai.name,av.id, av.articleid, (sum(av.onclick)) as pop, a.title,a.content, a.authorname, a.permalink, a.authorid, a.publishdate')
        ->from('ArticleViewCount av')
        ->leftJoin('av.articles a')
        ->leftJoin('a.thumbnail ai')
        ->leftJoin('a.chapters chap')
        ->where('a.deleted=0')
        ->andWhereIn('av.articleid', $artArr)
        ->groupBy('av.articleid')
        ->orderBy('pop DESC')
        ->limit($limit)
        ->fetchArray();
        //echo "<pre>";
        //print_r($mostReadArticle); die;
        return $mostReadArticle;

    }


    /**
     * generate Money Saving Articles on category basis
     * @author Raman
     * @version 1.0
     */


    public static function generateMoneySavingArticlesOfcategory($catId)
    {
        $article = Doctrine_Query::create()->select('DISTINCT a.id')
        ->from('Articles a')
        ->innerJoin('a.refarticlecategory r')
        ->where('r.relatedcategoryid =' . "'$catId'")
        ->fetchArray();
        return $article;

    }

    /**
     * generate all related voucher code categories of money saving categories
     * @author Raman
     * @version 1.0
     */


    public static function generateRelatedCategory($catId, $limit=10)
    {
        $relatedCategory = Doctrine_Query::create()->select('ac.id, r.name, r.permaLink, ci.path, ci.name')
        ->from('Articlecategory ac')
        ->leftJoin('ac.relatedcategory r')
        ->leftJoin('r.categoryicon ci')
        ->where('ac.id ='."'$catId'")
        ->limit($limit)
        //->getSqlQuery();
        ->fetchArray();
        return $relatedCategory;

    }

    /**
     * generate all Money Saving Articles on category basis
     * @author Raman
     * @version 1.0
     */



}
