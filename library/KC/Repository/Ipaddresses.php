<?php
namespace KC\Repository;
class IpAddresses Extends \KC\Entity\ipAddresses
{
    public static function getAllIpaddresses($params)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->from('KC\Entity\ipAddresses', 'ipa')
            ->orderBy("ipa.created_at", "DESC");

        $request  = \DataTable_Helper::createSearchRequest($params, array('name', 'ipaddress', 'created_at'));

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emUser'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('number', 'ipa.id')
            ->add('text', 'ipa.name')
            ->add('number', 'ipa.ipaddress')
            ->add('number', 'ipa.created_at');
        $data = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $ipAddressesList = \DataTable_Helper::getResponse($data, $request);
        return $ipAddressesList;
    }

    public static function addIpaddress($params)
    {
        $entityManagerUser = \Zend_Registry::get('emUser');
        if (isset($params['id'])) {
            $ipaddress = $entityManagerUser->find('KC\Entity\ipAddresses', $params['id']);
        } else {
            $ipaddress = new \KC\Entity\ipAddresses();
        }
        $ipaddress->name = \FrontEnd_Helper_viewHelper::sanitize($params['name']);
        $ipaddress->ipaddress = \FrontEnd_Helper_viewHelper::sanitize($params['ipaddress']);
        $ipaddress->deleted = 0;
        $ipaddress->created_at = new \DateTime('now');
        $ipaddress->updated_at = new \DateTime('now');
        $entityManagerUser->persist($ipaddress);
        $entityManagerUser->flush();
        self::updateAdminIpAddressInHtaccess();
        return true;
    }

    public static function deleteIpaddress($id)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\ipAddresses', 'ipAddresses')
                ->setParameter(1, $id)
                ->where('ipAddresses.id = ?1')
                ->getQuery();
            $query->execute();
        self::updateAdminIpAddressInHtaccess();
        return true;
    }

    public static function getIpaddressForEdit($id)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('ips')
            ->from('\KC\Entity\ipAddresses', 'ips')
            ->where("ips.id =".$id);
        $ipAddress = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $ipAddress;
    }

    public static function getIpAdressList()
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('ipaddress')
            ->from('\KC\Entity\ipAddresses', 'ipaddress');
        $ipAddressesList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $ipAddressesList;
    }

    public static function updateAdminIpAddressInHtaccess()
    {
        $htaccessFilePath = APPLICATION_PATH."/modules/admin/.htaccess";
        $allowedIpsList = self::getIpAdressList();
        $htaccessContent = "order deny,allow";
        $htaccessContent .="\n";
        $htaccessContent .= "deny from all";
        $htaccessContent .="\n";
        foreach ($allowedIpsList as $allowedIpList) {
            $htaccessContent .= 'Allow from '.$allowedIpList['ipaddress']."\n";
        }
        $ipAddressHandle = fopen($htaccessFilePath, 'w');
        fwrite($ipAddressHandle, $htaccessContent);
        fclose($ipAddressHandle);
        return true;
    }
}