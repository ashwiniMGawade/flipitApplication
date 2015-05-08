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
            $visitorIds = explode(',', $visitorIds);
            foreach ($visitorIds as $visitorId) {
                $codeAlertInformation = Doctrine_Query::create()
                    ->select("c.id")
                    ->from("CodeAlertVisitors c")
                    ->where('c.offerId = '.$offerId)
                    ->andWhere('c.visitorId = '.$visitorId)
                    ->fetchArray();
                if (empty($codeAlertInformation)) {
                    $codeAlertQueue = new CodeAlertVisitors();
                    $codeAlertQueue->offerId = $offerId;
                    $codeAlertQueue->visitorId = $visitorId;
                    $codeAlertQueue->save();
                }
            }
        }
        return true;
    }
}
