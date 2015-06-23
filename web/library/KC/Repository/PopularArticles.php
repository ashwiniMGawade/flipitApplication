<?php
namespace KC\Repository;
class PopularArticles Extends \KC\Entity\PopularArticles
{
    public static function clearCacheOfArticles()
    {
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_moneySaving_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homemoneysaving_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categoriesArticles_list');
    }

    public static function getPopularArticles()
    {
        $currentDateTime = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select("p.id, a.id as articleId, a.title, p.position")
            ->from("KC\Entity\PopularArticles", "p")
            ->leftJoin("p.articles", "a")
            ->where("a.publish = 1")
            ->andWhere("a.deleted= 0")
            ->andWhere($queryBuilder->expr()->lte("a.publishdate", $queryBuilder->expr()->literal($currentDateTime)))
            ->orderBy("p.position", "ASC");
        $popularArticles = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularArticles;
    }

    public static function savePopularArticlePosition($articleIds)
    {
        if (!empty($articleIds)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->delete('KC\Entity\PopularArticles', 'p')
            ->where('p.id > 0')
            ->getQuery();
            $query->execute();
            $articleIds = explode(',', $articleIds);
            $i = 1;
            foreach ($articleIds as $articleId) {
                self::savePopularArticle($articleId, $i);
                $i++;
            }
        }
        self::clearCacheOfArticles();
    }

    public static function changeArticlesDataForSorting($popularArticles)
    {
        $changedPopularArticles = array();
        foreach ($popularArticles as $article) {
            $changedPopularArticles[] = $article['articleId'];
        }
        return $changedPopularArticles;
    }

    public static function getMaxPosition()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.position')
            ->from('KC\Entity\PopularArticles', 'p')
            ->orderBy('p.position', 'DESC')
            ->setMaxResults(1);
        $maxPosition = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($maxPosition)) {
            $position = $maxPosition[0]['position'];
        } else {
            $position = 0;
        }
        return $position;
    }

    public static function savePopularArticle($articleId, $position)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $popularArticle = new \KC\Entity\PopularArticles();
        $popularArticle->articles = $entityManagerLocale->find('KC\Entity\Articles', $articleId);
        $popularArticle->position = $position;
        $popularArticle->deleted = 0;
        $popularArticle->created_at = new \DateTime('now');
        $popularArticle->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($popularArticle);
        $entityManagerLocale->flush();
        return true;
    }

    public static function updateArticles($changedArticlesDataForSorting)
    {
        foreach ($changedArticlesDataForSorting as $id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->update('KC\Entity\PopularArticles', 'p')
            ->set('p.position', $id)
            ->where('p.articles=' .$id)
            ->getQuery();
            $query->execute();
        }
    }

    public static function saveArticles($articlesList, $changedArticlesDataForSorting, $flag = true)
    {
        foreach ($articlesList as $article) {
            if (!in_array($article['id'], $changedArticlesDataForSorting)) {
                $position = intval(self::getMaxPosition() + 1);
                self::savePopularArticle($article['id'], $position);
            }
        }
        if ($flag) {
            self::clearCacheOfArticles();
        } else {
            array_map('unlink', glob(PUBLIC_PATH.'tmp/*'));
        }
        return true;
    }

    public static function deletePopularArticles()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->delete('KC\Entity\PopularArticles', 'p')
            ->where('p.id > 0')
            ->getQuery();
            $query->execute();
        return true;
    }
}