<?php
class Ipaddresses extends BaseIpaddresses
{
    public static function getAllIpaddresses($params)
    {
        $ipAddresses = Doctrine_Query::create()
            ->select('e.name,e.ipaddress,e.created_at')
            ->from("Ipaddresses as e")
            ->orderBy("e.created_at DESC");
        $ipAddressesList = DataTable_Helper::generateDataTableResponse(
            $ipAddresses,
            $params,
            array("__identifier" => 'e.id','e.id','name','ipaddress','created_at'),
            array(),
            array()
        );
        return $ipAddressesList;
    }

    public static function checkIpEnrty($clientIp)
    {
        $ipAddress = Doctrine_Query::create()
            ->select('ipaddress')
            ->from("Ipaddresses")
            ->where("ipaddress = '".$clientIp."'")
            ->fetchOne();
        return $ipAddress;
    }

    public static function addIpaddress($params)
    {
        if (isset($params['id'])) {
            $ipaddress = Doctrine_Core::getTable('Ipaddresses')->find($params['id']);
        } else {
            $ipaddress = new Ipaddresses();
        }
        $ipaddress->name = FrontEnd_Helper_viewHelper::sanitize($params['name']);
        $ipaddress->ipaddress = FrontEnd_Helper_viewHelper::sanitize($params['ipaddress']);
        $ipaddress->save();
        self::updateAdminIpAddressInHtaccess();
        return true;
    }

    public static function deleteIpaddress($id)
    {
        Doctrine_Query::create()->delete()
            ->from('Ipaddresses e')
            ->where("e.id=" . $id)
            ->execute();
        self::updateAdminIpAddressInHtaccess();
        return true;
    }

    public static function getIpaddressForEdit($id)
    {
        $ipAddress = Doctrine_Query::create()
            ->select("ips.*")
            ->from("Ipaddresses as ips")
            ->where("ips.id =".$id)
            ->fetchArray();
        return $ipAddress;
    }

    public static function getIpAdressList()
    {
        $ipAddressesList  = Doctrine_Query::create()
            ->select("ipaddress")
            ->from("Ipaddresses")
            ->fetchArray();
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
