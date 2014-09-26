<?php

class CodeAlertVisitors extends BaseCodeAlertVisitors
{
    public static function getVisitorsToRemoveInCodeAlert($visitorId, $offerId)
    {
        $visitors = Doctrine_Query::create()
            ->select("c.visitorId")
            ->from("CodeAlertVisitors c")
            ->where('offerId = '.$offerId)
            ->andWhere('visitorId = '.$visitorId)
            ->fetchArray();
        return $visitors;
    }

    public static function saveCodeAlertVisitors($visitorIds, $offerId)
    {
        if (isset($visitorIds) && $visitorIds != '') {
            $codeAlertInformation = Doctrine_Query::create()
                ->select()
                ->from("CodeAlertVisitors")
                ->where('offerId = '.$offerId)
                ->fetchArray();

            if (empty($codeAlertInformation)) {
                $visitorIds = explode(',', $visitorIds);
                foreach ($visitorIds as $visitorValue) {
                    $codeAlertQueue = new CodeAlertVisitors();
                    $codeAlertQueue->offerId = $offerId;
                    $codeAlertQueue->visitorId = $visitorValue;
                    $codeAlertQueue->save();
                }
            }
        }
        return true;
    }
}
