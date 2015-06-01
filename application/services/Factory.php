<?php

class Application_Service_Factory
{
    public static function topOffers($limit)
    {
        $topOffers = new Application_Service_Offer_TopOffer(new KC\Repository\Offer, $limit);
        return $topOffers->execute($limit);
    }
}
