<?php
class Conversions extends BaseConversions
{
    public static function getConversionInformationById($id)
    {
        $conversionInfo = array();
        
        if (is_numeric($id)) {
            $offerOrShopId = self::getConversionOfferOrShopId($id);
            if (!empty($offerOrShopId) && $offerOrShopId['offerId'] != '') {
                $conversionInfo = Doctrine_Query::create()->select('c.id,o.title as offerTitle,s.name as shopName,cat.name as categoryName')
                    ->from("Conversions c")
                    ->leftJoin("c.offer o")
                    ->leftJoin("o.shop s")
                    ->leftJoin('o.category cat');
            } else {
                $conversionInfo = Doctrine_Query::create()->select('c.id,s.name as shopName,cat.name as categoryName')
                    ->from("Conversions c")
                    ->leftJoin("c.shop s")
                    ->leftJoin('s.category cat');
            }
            
            $conversionInfo = $conversionInfo->where('c.id = '. $id)
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        }
        return $conversionInfo;
    }

    public static function getConversionOfferOrShopId($id)
    {
        $conversionInfo = array();
        
        if (is_numeric($id)) {
            $conversionInfo = Doctrine_Query::create()->select('c.shopId,c.offerId')
                ->from("Conversions c")
                ->where('c.id = '. $id)
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        }
        return $conversionInfo;
    }

    public static function addConversion($id, $clickoutType)
    {
        $conversionId = '';
        $clientIP = ip2long(FrontEnd_Helper_viewHelper::getRealIpAddress());

        if ($clickoutType === 'offer') {
            $clickout = new FrontEnd_Helper_ClickoutFunctions($id, null);
        } else {
            $clickout = new FrontEnd_Helper_ClickoutFunctions(null, $id);
        }

        $hasNetwork = $clickout->checkIfShopHasAffliateNetwork();
        if ($hasNetwork) {
            $conversionInfo = self::checkIfConversionExists($id, $clientIP, $clickoutType);
            $conversionId = $conversionInfo['id'];

            if (!isset($conversionInfo['exists'])) {
                $conversionId = self::addNewConversion($id, $clientIP, $clickoutType);
            }
        }
        return $conversionId;
    }

    private static function checkIfConversionExists($id, $clientIP, $clickoutType)
    {
        $conversionInfo = Doctrine_Query::create()
                ->select('count(c.id) as exists,c.id')
                ->from('Conversions c');

        if ($clickoutType === 'offer') {
            $conversionInfo = $conversionInfo->where('c.offerId="'.$id.'"');
        } else {
            $conversionInfo = $conversionInfo->where('c.shopId="'.$id.'"');
        }
    
        $conversionInfo = $conversionInfo->andWhere('c.IP="'.$clientIP.'"')
            ->andWhere("c.converted=0")
            ->groupBy('c.id')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $conversionInfo;
    }

    private static function addNewConversion($id, $clientIP, $clickoutType)
    {
        $conversion = new Conversions();

        if ($clickoutType === 'offer') {
            $conversion->offerId = $id;
        } else {
            $conversion->shopId = $id;
        }
        
        $conversion->IP = $clientIP;
        $conversion->save();
        return $conversion->id;
    }
}
