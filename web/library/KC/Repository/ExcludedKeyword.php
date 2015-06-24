<?php
namespace KC\Repository;

class ExcludedKeyword extends \Core\Domain\Entity\ExcludedKeyword
{
    ##############################################################################
    ################## REFACTORED CODE ###########################################
    ##############################################################################
    public static function getExcludedKeywords($keywordForSearch)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $excludedKeywords = $queryBuilder
            ->select("k,es,s")
            ->from("KC\Entity\ExcludedKeyword", "k")
            ->leftJoin('k.keywords', 'es')
            ->leftJoin('es.keywords', 's')
            ->where($queryBuilder->expr()->eq('k.keyword', $queryBuilder->expr()->literal($keywordForSearch)))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $excludedKeywords;
    }
    ##############################################################################
    ################## END REFACTORED CODE #######################################
    ##############################################################################

    public static function addKeywords($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $data = new \KC\Entity\ExcludedKeyword();
        $data->keyword = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['keyword']);
        $data->action = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['actionType']);
        $data->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectTo']);
        $data->created_at = new \DateTime('now');
        $data->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($data);
        $entityManagerLocale->flush();

        if ($params['actionType'] == 1) {
            $splitedVal = explode(',', $params['selectedShopForSearchbar']);
            foreach ($splitedVal as $sp) {
                $relKeyWords =  new \KC\Entity\RefExcludedkeywordShop();
                $relKeyWords->shops = $entityManagerLocale->find('\KC\Entity\ExcludedKeyword', $data->id);
                $relKeyWords->keywords = $entityManagerLocale->find('\KC\Entity\Shop', $sp);
                $relKeyWords->keywordname = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['keyword']);
                $relKeyWords->created_at = new \DateTime('now');
                $relKeyWords->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($relKeyWords);
                $entityManagerLocale->flush();
            }
        }

        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_excludedkeyword_list');
    }

    public static function getKeywordList($params)
    {
        $request  = \DataTable_Helper::createSearchRequest($params, array('id','keyword', 'action', 'created_at'));
        $qb = \Zend_Registry::get('emLocale')->createQueryBuilder()->from('\Core\Domain\Entity\ExcludedKeyword', 'e');
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('number', 'e.id')
            ->add('text', 'e.keyword')
            ->add('text', 'e.action')
            ->add('number', 'e.created_at');
        $results = $builder->getTable()->getResponseArray();
        return $results;
    }

    public static function getKeywordForEdit($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                "k.id, k.keyword, k.url, k.action, k.created_at, k.updated_at,
                es.id as keywordid,s.id as sid , s.name as name"
            )
            ->from("KC\Entity\ExcludedKeyword", "k")
            ->leftJoin('k.keywords', 'es')
            ->leftJoin('es.keywords', 's')
            ->where("k.id =".$id);
        $getdata = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $getdata;
    }

    public static function updateKeyword($params)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $data = \Zend_Registry::get('emLocale')->find('\KC\Entity\ExcludedKeyword', $params['id']);
        $data->keyword = \BackEnd_Helper_viewHelper::stripSlashesFromString($params["keyword"]);
        $data->action = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['actionType']);

        if (isset($params['redirectTo']) && $params['redirectTo']!="") {
            $data->url = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['redirectTo']);
        }

        $data->created_at = $data->created_at;
        $data->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($data);
        $entityManagerLocale->flush();

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if ($params['actionType'] == 1) {
            $splitedVal = explode(',', $params['selectedShopForSearchbar']);
            $del = $queryBuilder->delete('KC\Entity\RefExcludedkeywordShop', 'res')
                ->where('res.shops='.$params['id'])
                ->getQuery()
                ->execute();
            foreach ($splitedVal as $sp) {
                if ($sp != '') {
                    $relKeyWords =  new \KC\Entity\RefExcludedkeywordShop();
                    $relKeyWords->shops = $entityManagerLocale->find('\KC\Entity\ExcludedKeyword', $data->id);
                    $relKeyWords->keywords = $entityManagerLocale->find('\KC\Entity\Shop', $sp);
                    $relKeyWords->keywordname = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['keyword']);
                    $relKeyWords->created_at = new \DateTime('now');
                    $relKeyWords->updated_at = new \DateTime('now');
                    $relKeyWords->deleted = 0;
                    \Zend_Registry::get('emLocale')->persist($relKeyWords);
                    \Zend_Registry::get('emLocale')->flush();
                }
            }
        } else {
            $queryBuilder
                ->delete('KC\Entity\RefExcludedkeywordShop', 'rs')
                ->where('rs.shops='.$params['id'])
                ->getQuery()
                ->execute();
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_excludedkeyword_list');
    }

    public static function exportKeywordList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $keywordList = $queryBuilder
            ->select('e')
            ->from("KC\Entity\ExcludedKeyword", "e")
            ->orderBy("e.id", "DESC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $keywordList;
    }

    public static function deleteKeyword($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $searchbarDel = \Zend_Registry::get('emLocale')->find('\KC\Entity\ExcludedKeyword', $id);
        $q = $queryBuilder->delete('KC\Entity\ExcludedKeyword', 'e')
            ->where("e.id=" . $id)
            ->getQuery()
            ->execute();
    }

    public static function searchShops($keyword, $selectedShop)
    {
        $SP = $selectedShop!='' ? $selectedShop: 0;
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('s.name as name,s.id as id')
            ->from("KC\Entity\Shop", "s")
            ->where('s.deleted=0')
            ->andWhere($queryBuilder->expr()->notIn('s.id', $SP))
            ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword . '%')))
            ->orderBy("s.name", "ASC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function checkStoreExistOrNot($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $Shop = $queryBuilder->select('s.id')
                ->from("KC\Entity\Shop", "s")
                ->where('s.id ='.$id);
        $flag = 0;
        if (!empty($Shop)) {
            $flag = $id;
        }
        return $flag;
    }

    public static function getExRedirectKeywordsList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $keywordList = $queryBuilder
            ->select('e.keyword as keyword')
            ->from("KC\Entity\ExcludedKeyword", "e")
            ->where("e.action = 0")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $newArray = array();

        foreach($keywordList as $keys):
            $newArray[] = $keys['keyword'];
        endforeach;

        return $newArray;
    }
}
