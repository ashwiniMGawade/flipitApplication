<?php
namespace KC\Repository;
class IpAddresses Extends \KC\Entity\User\IpAddresses
{
    public static function getAllIpaddresses($params)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->from('KC\Entity\User\IpAddresses', 'ipa');

        $request  = \DataTable_Helper::createSearchRequest($params, array('name', 'ipaddress', 'created_at'));

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emUser'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('number', 'ipa.id')
            ->add('text', 'ipa.name')
            ->add('number', 'ipa.ipaddress')
            ->add('number', 'ipa.created_at');
        $ipAddressesList = $builder->getTable()->getResponseArray();
        return $ipAddressesList;
    }

    public static function addIpaddress($params)
    {
        $entityManagerUser = \Zend_Registry::get('emUser');
        if (isset($params['id'])) {
            $ipaddress = $entityManagerUser->find('KC\Entity\User\IpAddresses', \FrontEnd_Helper_viewHelper::sanitize($params['id']));
        } else {
            $ipaddress = new \KC\Entity\User\IpAddresses();
        }
        $ipaddress->name = \FrontEnd_Helper_viewHelper::sanitize($params['name']);
        $ipaddress->ipaddress = \FrontEnd_Helper_viewHelper::sanitize($params['ipaddress']);
        $ipaddress->deleted = 0;
        $ipaddress->created_at = new \DateTime('now');
        $ipaddress->updated_at = new \DateTime('now');
        $entityManagerUser->persist($ipaddress);
        $entityManagerUser->flush();
        return true;
    }

    public static function deleteIpaddress($id)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\User\IpAddresses', 'ipAddresses')
                ->setParameter(1, \FrontEnd_Helper_viewHelper::sanitize($id))
                ->where('ipAddresses.id = ?1')
                ->getQuery();
            $query->execute();
        return true;
    }

    public static function getIpaddressForEdit($id)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('ips')
            ->from('\KC\Entity\User\IpAddresses', 'ips')
            ->where("ips.id =".\FrontEnd_Helper_viewHelper::sanitize($id));
        $ipAddress = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $ipAddress;
    }

    public static function getIpAdressList()
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('ipaddress')
            ->from('\KC\Entity\User\IpAddresses', 'ipaddress');
        $ipAddressesList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $ipAddressesList;
    }
}