<?php
class BackEnd_Helper_importOffersExcel
{
	public static function importExcelOffers($localeId)
    {
        $websiteDetails = Website::getWebsiteDetails($localeId);
        $localeName = explode('/', $websiteDetails['name']);
        $locale = isset($localeName[1]) ?  $localeName[1] : "en";
        return $locale;
    }
}