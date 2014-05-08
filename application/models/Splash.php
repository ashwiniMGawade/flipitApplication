<?php

class Splash extends BaseSplash
{
    public function getCurrentOffer()
    {
        
    }
    
    public function saveOffer($request,$locale)
    {
        $offerId = $request->getParam('searchOfferId' , false);
    
        if(! $offerId) {
            return false ;
        }

        $offer = Doctrine_Core::getTable("Offer")->find($offerId);

        if($offer->id > 0) {

            $offerName = $offer->title;
            $websiteId = $request->getParam('locale' , false);
            
            if($offerName && $website) {
                try {
                    $this->websiteId = $websiteId;
                    $this->offerName = $offerName;
                    $this->offerId = $offerId;
                    $this->locale = $locale;
                    $this->save();
                    return $this->id ;
                } catch (Exception $e) {
                    return  false ;
                }
            }
        } else {
            return false ;
        }
    }
}
