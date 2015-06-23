<?php
namespace KC\Repository;

class AffliateNetwork extends \Core\Domain\Entity\AffliateNetwork
{
    public function addNewnetwork($params)
    {
        $data = new \KC\Entity\AffliateNetwork();
        $data->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addNetworkText']);
        $data->subId = $params['subId'];
        $data->status = '1';
        $data->deleted = '0';
        $data->created_at = new \DateTime('now');
        $data->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($data);
        \Zend_Registry::get('emLocale')->flush();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_affilatenetwork_page');
        return $data;
    }

    public static function getNetworkList($params = "")
    {
        $srh =  @$params["searchText"] != 'undefined' ? @$params["searchText"] : '';
        $sortBy = isset($params['sortBy']) ? @$params['sortBy'] : '';
        $delVal = isset($params['off']) ?  $params['off'] : '0, 1';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $networkList = $queryBuilder->select("a")
            ->from("KC\Entity\AffliateNetwork", "a")
            ->where($queryBuilder->expr()->like("a.name", $queryBuilder->expr()->literal($srh."%")))
            ->andWhere("a.deleted = 0")
            ->andWhere($queryBuilder->expr()->in('a.status', $delVal))
            ->andWhere("a.affliate_networks IS NULL");
        $request = \DataTable_Helper::createSearchRequest($params, array());
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($networkList)
            ->add('number', 'a.id')
            ->add('text', 'a.name')
            ->add('text', 'a.subId');
        $list = $builder->getTable()->getResponseArray();
        return $list;
    }

    public static function searchTopFiveNetwork($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('a.name as name')
            ->from("KC\Entity\AffliateNetwork", "a")
            ->where('a.deleted=0')
            ->andWhere('a.status=1')
            ->andWhere($queryBuilder->expr()->like("a.name", $queryBuilder->expr()->literal($keyword."%")))
            ->andWhere("a.affliate_networks IS NULL")
            ->orderBy("a.name", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function changeStatus($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $status = $params['status']=='offline' ? '0' : '1';
        $q = $queryBuilder->update("KC\Entity\AffliateNetwork", "a")
            ->set('a.status', $status)
            ->where('a.id='. $params['id'])
            ->getQuery()
            ->execute();
    }

    public static function getNetworkForEdit($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select("a")
            ->from("KC\Entity\AffliateNetwork", "a")
            ->where("a.id = ". $id)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function updateNetwork($params)
    {
        self::replaceNetwork($params);
        $data = \Zend_Registry::get('emLocale')->find('KC\Entity\AffliateNetwork', $params['id']);
        $data->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($params["addNetworkText"]);

        if (isset($params["subId"])) {
            $data->subId = \BackEnd_Helper_viewHelper::stripSlashesFromString($params["subId"]);
        }

        $data->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($data);
        \Zend_Registry::get('emLocale')->flush();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_affilatenetwork_page');
    }

    public static function deleteNetwork($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $q = $queryBuilder->update('KC\Entity\AffliateNetwork', 'a')
            ->set('a.deleted', 1)
            ->where('a.id='. $params['id'])
            ->getQuery()
            ->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_affilatenetwork_page');
    }

    public static function networklistDropdown($params = "")
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $networkList = $queryBuilder->select('a.name as name ,a.id, a.status as status ,IDENTITY(a.affliate_networks) as replaceWithId')
            ->from("KC\Entity\AffliateNetwork", "a")
            ->Where("a.deleted = 0")
            ->andWhere('a.id!='. $params['id'])
            ->andWhere("a.affliate_networks IS NULL")
            ->orderBy("a.name", "ASC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $networkList;
    }

    public function replaceNetwork($params)
    {
        if (intval($params['selectNetworkList'] > 0)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $q = $queryBuilder->update('KC\Entity\AffliateNetwork', 'a')
                ->set('a.affliate_networks', $params['selectNetworkList'])
                ->where('a.id= '. $params['networkUpdatedId'])
                ->getQuery()
                ->execute();
        }
    }
}
