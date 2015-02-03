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
        return true;
    }

    public function deleteIpaddress($id)
    {
        $query = Doctrine_Query::create()->delete()
            ->from('Ipaddresses e')
            ->where("e.id=" . $id)
            ->execute();
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
}
