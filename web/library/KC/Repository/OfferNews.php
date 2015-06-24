<?php

namespace KC\Repository;

class OfferNews extends \Core\Domain\Entity\OfferNews
{
    public static function saveNewsticker($params)
    {
        $savenews = new \Core\Domain\Entity\OfferNews();
        $savenews->shop = \Zend_Registry::get('emLocale')
            ->getRepository('\Core\Domain\Entity\Shop')
            ->find($params['selctedshop']);
        $savenews->title = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsTitle']);
        $savenews->url = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl']);
        $savenews->content = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription']);
        $savenews->linkstatus = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsdeepLinkStatus']);
        $savenews->startdate = new \DateTime($params['newsStartDate']);
        $savenews->deleted = '0';
        $savenews->created_at = new \DateTime('now');
        $savenews->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($savenews);
        \Zend_Registry::get('emLocale')->flush();
        $key = '4_shopLatestUpdates'  . intval($savenews->shop->id) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return $savenews->id;
    }

    public static function getnewstickerList($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $newstickerList =  $queryBuilder
            ->from("\Core\Domain\Entity\OfferNews", "n")
            ->leftJoin("n.shop", "s")
            ->where("n.deleted = 0");
        $request = \DataTable_Helper::createSearchRequest($params, array());
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($newstickerList)
            ->add('number', 'n.id')
            ->add('text', 'n.title')
            ->add('text', 's.name')
            ->add('text', 'n.startdate')
            ->add('text', 'n.linkstatus');
        $list = $builder->getTable()->getResponseArray();
        return $list;
    }

    public static function deletenewsticker($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $del1 = $queryBuilder
           ->delete("\Core\Domain\Entity\OfferNews", "n")
           ->where("n.id=" . $id)
           ->getQuery()
           ->execute();
        $key = '4_shopLatestUpdates'  . intval($savenews->shop->id) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
    }

    public static function getNewsticker($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select("n,s")
            ->from("\Core\Domain\Entity\OfferNews", "n")
            ->LeftJoin("n.shop", "s")
            ->where("n.id = ". $id)
            ->andWhere('n.deleted=0')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function updateNewsticker($params)
    {
            $data = \Zend_Registry::get('emLocale')
                ->getRepository('\Core\Domain\Entity\OfferNews')
                ->find($params['id']);
            $data->shop = \Zend_Registry::get('emLocale')
                ->getRepository('\Core\Domain\Entity\Shop')
                ->find($params['selctedshop']);
            $data->offerId = null;
            $data->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsTitle']);
            $data->url = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl']);
            $data->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription']);
            $data->linkstatus =  @$params['newsdeepLinkStatus'];
            $data->startdate = new \DateTime($params['newsStartDate']);
            $data->deleted = '0';
            $data->created_at = $data->created_at;
            $data->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($data);
            \Zend_Registry::get('emLocale')->flush();
            $key = '4_shopLatestUpdates'  . intval($data->shop->id) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            return $data->id;
    }

    public static function getAllUrls($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data  = $queryBuilder->select("s,on")
                ->from('\Core\Domain\Entity\OfferNews', 'on')
                ->leftJoin("on.shop", "s")
                ->where("on.id= ". $id)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $urlsArray = array();
        # check an offer news has related shop or not
        if (isset($data['shop']) && $data['shop']['permaLink']) {
            $urlsArray[] = $data['shop']['permaLink'] ;
        }
        return $urlsArray ;
    }

    public static function getnewstickerListForExport()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query  = $queryBuilder
            ->select('n.id as id, n.title as title, n.startdate, s.name, s.id as shopId, n.linkstatus, n.url, n.content')
            ->from("\Core\Domain\Entity\OfferNews", "n")
            ->leftJoin("n.shop", "s")
            ->where("n.deleted = 0")
            ->orderBy("n.title", "ASC");
        $newstickerList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newstickerList;
    }
}
