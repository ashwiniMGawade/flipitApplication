<?php
class FrontEnd_Helper_OfferPartialFunctions extends FrontEnd_Helper_viewHelper {
	
	public static function getUrlToShow($currentOffer)
	{
	
		if ($currentOffer->refURL != "" || $currentOffer->shop['refUrl']!= "" || $currentOffer->shop['actualUrl'] != "") {
			$urlToShow = self::getOfferBounceUrl($currentOffer->id);
		} else {
			$urlToShow = HTTP_PATH_LOCALE.$currentOffer->shop['permalink'];
		}
	
		if ($currentOffer->discountType=='PA') {
			if ($currentOffer->refOfferUrl != "") {
				$urlToShow = self::getOfferBounceUrl($currentOffer->id);
			} elseif (count($currentOffer->logo)>0) {
				$urlToShow = PUBLIC_PATH_CDN.ltrim($currentOffer->logo['path'], "/").$currentOffer->logo['name'];
			}
		}
		return $urlToShow;
	}
	
	public static function getOfferBounceUrl($offerId)
	{
		return HTTP_PATH_LOCALE."out/offer/".$offerId;
	}
	
	public static function getDiscountImage($currentOffer){
		$offerDiscountImage ='';
		if (!empty ( $currentOffer->tiles)) {
			$offerDiscountImage = PUBLIC_PATH_CDN . ltrim(@$currentOffer->tiles['path'], "/").@$currentOffer->tiles['name'];
		}
		return $offerDiscountImage;
	}
	
	public static function getOfferTermsAndCondition($currentOffer)
	{
		$termsAndConditions = '';
		if (count($currentOffer->termandcondition) > 0) {
			$termsAndConditions = $currentOffer->termandcondition[0]['content'];
		}
		return $termsAndConditions;
	}
	
	public static function getDaysDifference($endDate)
	{
		$currentDate = date('Y-m-d');
		$offerEndDate = date('Y-m-d', strtotime($endDate));
		$timeStampStart = strtotime($offerEndDate);
		$timeStampEnd = strtotime($currentDate);
	
		$dateDifference = abs($timeStampEnd - $timeStampStart);
		$dayDifference = floor($dateDifference/(60*60*24));
	}
	
	public static function getUserIsLoginOrNot()
	{
		$userLoginStatus = false;
		if (Auth_VisitorAdapter::hasIdentity()) :
		$userLoginStatus = true;
		endif;
		return $userLoginStatus;
	}
	
	public static function getOfferOption($string)
	{
		$string = '<strong class="exclusive">
		<span class="glyphicon glyphicon-star"></span>'.$string.'</strong>';
		return $string;
	}
	
	public static function getOfferExclusiveOrEditor($currentOffer)
	{
		$offerOption = '';
		$trans = Zend_Registry::get('Zend_Translate');
	
		if ($currentOffer->exclusiveCode == '1'):
		$offerOption = self::getOfferOption($trans->translate('Exclusive'));
		elseif ($currentOffer->editorPicks =='1'):
		$offerOption = self::getOfferOption($trans->translate('Editor'));
		endif;
	
		return $offerOption;
	}
	
	public static function dateStringFormat($currentOffer, $dayDifference)
	{
		$trans = Zend_Registry::get('Zend_Translate');
	
		$offerOption = self::getOfferExclusiveOrEditor($currentOffer);
	
		$stringAdded = $trans->translate('Added');
		$stringOnly = $trans->translate('Only');
		$startDate = new Zend_Date(strtotime($currentOffer->startDate));
	
		$dateFormatString = '';
		if($currentOffer->discountType == "CD"):
		$dateFormatString .= $stringAdded;
		$dateFormatString .= ':';
		$dateFormatString .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
		$dateFormatString .= ',';
			
		if ($dayDifference ==5 || $dayDifference ==4 || $dayDifference ==3 || $dayDifference ==2) {
			$dateFormatString .= $stringOnly;
			$dateFormatString .= '&nbsp;';
			$dateFormatString .= $dayDifference;
			$dateFormatString .= '&nbsp;';
			$dateFormatString .= $trans->translate('days valid!');
	
		} elseif ($dayDifference ==1) {
			$dateFormatString .= $stringOnly;
			$dateFormatString .= '&nbsp;';
			$dateFormatString .= $dayDifference;
			$dateFormatString .= '&nbsp;';
			$dateFormatString .= $trans->translate('day only!');
	
		} elseif ($dayDifference ==0) {
			$dateFormatString .= $trans->translate('Expires today');
	
		} else {
			$endDate = new Zend_Date(strtotime($this->endDate));
			$dateFormatString .= $trans->translate('Expires on').':';
			$dateFormatString .= ucwords($endDate->get(Zend_Date::DATE_MEDIUM));
		}
			
		elseif ($currentOffer->discountType == "PR" || $currentOffer->discountType == "SL" || $currentOffer->discountType == "PA"):
		$dateFormatString .= $stringAdded;
		$dateFormatString .= ':';
		$dateFormatString .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
		endif;
	
		return $offerOption . $dateFormatString;
	}
	
	public static function getClassNameForOffer($currentOffer)
	{
		$className = 'code';
		if ($currentOffer->discountType == "PR" && $currentOffer->discountType == "PA") {
			$className .= ' purple';
		} elseif($currentOffer->discountType=='SL') {
			$className .= ' red';
		} else if($currentOffer->extendedOffer =='1') {
			$className .= ' blue';
		}
		return $className;
	}
}
?>