<?php
class Ipaddresses extends BaseIpaddresses
{
    public static function getAllIpaddresses($params)
    {
        $ipaddresses = Doctrine_Query::create()
            ->select('e.name,e.ipaddress,e.created_at')
            ->from("Ipaddresses as e")
            ->orderBy("e.created_at DESC");
        $ipaddressesList = DataTable_Helper::generateDataTableResponse(
            $ipaddresses,
            $params,
            array("__identifier" => 'e.id','e.id','name','ipaddress','created_at'),
            array(),
            array()
        );
        return $ipaddressesList;
    }

    public static function addIpaddress($params)
    {
        if (isset($params['id'])) {
            $ipaddress = Doctrine_Core::getTable('Ipaddresses')->find($params['id']);
        } else {
            $ipaddress = new Ipaddresses();
        }
        $ipaddress->name = $params['name'];
        $ipaddress->ipaddress = $params['ipaddress'];
        $ipaddress->save();
        self::updateAdminIpAddressInHtaccess();
        return true;
    }

    public static function deleteIpaddress($id)
    {
        $query = Doctrine_Query::create()->delete()
            ->from('Ipaddresses e')
            ->where("e.id=" . $id)
            ->execute();
        self::updateAdminIpAddressInHtaccess();
        return true;
    }

    public static function getIpaddressForEdit($id)
    {
        $ipaddress = Doctrine_Query::create()
            ->select("k.*")
            ->from("Ipaddresses as k")
            ->where("k.id =".$id)
            ->fetchArray();
        return $ipaddress;
    }

    public static function getIpAdressList()
    {
        $ipaddressList = Doctrine_Query::create()
            ->select("ipaddress")
            ->from("Ipaddresses")
            ->fetchArray();
        return $ipaddressList;
    }

    public static function updateAdminIpAddressInHtaccess()
    {
        $htaccessFilePath = ".htaccess";
        $allowedIpsList = self::getIpAdressList();
        $content = "order allow,deny";
        $content .="\n";
        foreach ($allowedIpsList as $allowedIpList) {
            $content .= 'Allow from '.$allowedIpList['ipaddress']."\n";
        }
        $content .= "deny from all";
        $ipAddressHandle = fopen($htaccessFilePath, 'w');
        fwrite($ipAddressHandle, $content);
        fclose($ipAddressHandle);
        return true;
    }
}
