<?php
class Conversions extends BaseConversions
{
    public static function getConversionId($id, $ip, $type = 'offer')
    {
        $query =   Doctrine_Query::create()
            ->select("id, subId")
            ->from("Conversions")
            ->where("ip = ?", $ip);
        if ($type == 'offer') {
            $query->andWhere("offerId= ? ", $id);
        } else {
            $query->andWhere("shopId= ? ", $id);
        }
        return $query->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;
    }

    public static function updateConverted($subId)
    {
        Doctrine_Query::create()
        ->update('Conversions')
        ->set('converted', 1)
        ->where('subid = ?', $subId)->execute();
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_conversion_details');
        return true;
    }

    public static function getConversionDetail($subId)
    {
        return  Doctrine_Query::create()->select('c.subid,c.utma,c.utmz,o.id,s.id,n.*')
            ->from("Conversions c")
            ->leftJoin("c.offer o")
            ->leftJoin("o.shop s")
            ->leftJoin("s.affliatenetwork n")
            ->where('subid = ?', $subId)
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
    }

    public static function getConversionInformationById($id)
    {
        $conversionInfo = array();
        
        if (is_numeric($id)) {
            $conversionInfo = Doctrine_Query::create()->select('c.id,o.title as offerTitle,s.name as shopName,cat.name as categoryName')
                ->from("Conversions c")
                ->leftJoin("c.offer o")
                ->leftJoin("o.shop s")
                ->leftJoin('o.category cat')
                ->where('c.id = '. $id)
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        }
        return $conversionInfo;
    }
}
