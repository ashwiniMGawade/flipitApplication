<?php

namespace KC\Repository;

class ArticleViewCount extends \KC\Entity\ArticleViewCount
{

    ##########################################
    ########### REFACTORED CODE ##############
    ##########################################
    public static function getArticleClick($articleId, $clientIp)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $articleClick = $queryBuilder
            ->select('count(avc) as exist')
            ->from('KC\Entity\ArticleViewCount', 'avc')
            ->where('avc.deleted=0')
            ->andWhere('avc.onclick!=0')
            ->andWhere($queryBuilder->expr()->eq('IDENTITY(avc.articles)', $queryBuilder->expr()->literal($articleId)))
            ->andWhere($queryBuilder->expr()->eq('avc.ip', $queryBuilder->expr()->literal($clientIp)))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articleClick[0]['exist'];
    }

    public static function saveArticleClick($articleId, $clientIp)
    {
        $articleViewCount  = new \KC\Entity\ArticleViewCount();
        $articleViewCount->articles = \Zend_Registry::get('emLocale')->find('KC\Entity\Articles', $articleId);
        $articleViewCount->onclick = 1;
        $articleViewCount->ip = $clientIp;
        $articleViewCount->onload = 0;
        $articleViewCount->deleted = 0;
        $articleViewCount->created_at = new \DateTime('now');
        $articleViewCount->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($articleViewCount);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function getArticleOnload($articleId, $clientIp)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $articleOnLoad = $queryBuilder
            ->select('count(avc) as exist')
            ->from('KC\Entity\ArticleViewCount', 'avc')
            ->where('avc.deleted=0')
            ->andWhere('avc.onload!=0')
            ->andWhere($queryBuilder->expr()->eq('IDENTITY(avc.articles)', $queryBuilder->expr()->literal($articleId)))
            ->andWhere($queryBuilder->expr()->eq('avc.ip', $queryBuilder->expr()->literal($clientIp)))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $articleOnLoad[0]['exist'];
    }

    public static function saveArticleOnLoad($articleId, $clientIp)
    {
        $articleViewCount  = new \KC\Entity\ArticleViewCount();
        $articleViewCount->articles = \Zend_Registry::get('emLocale')->find('KC\Entity\Articles', $articleId);
        $articleViewCount->onclick = 1;
        $articleViewCount->ip = $clientIp;
        $articleViewCount->onload = 0;
        $articleViewCount->deleted = 0;
        $articleViewCount->created_at = new \DateTime('now');
        $articleViewCount->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($articleViewCount);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    ##########################################
    ########### END REFACTORED CODE ##########
    ##########################################

    public static function generatePopularArticle($limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:m:s';
        $date = date($format);
        // - 4 days from today
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;
        $papularArticle = $queryBuilder->select('av.id, IDENTITY(av.articles) as articleid, ((sum(av.onclick)) / (DATE_DIFF(CURRENT_TIMESTAMP(),a.publishdate))) as pop, a.publishdate')
            ->from('KC\Entity\ArticleViewCount', 'av')
            ->leftJoin('av.articles', 'a')
            ->where('av.updated_at <=' . "'$nowDate' AND av.updated_at >="."'$past4Days'")
            ->groupBy('av.articles')
            ->orderBy('pop', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $papularArticle;
    }
}
