<?php
class FrontEnd_Helper_HomePagePartialFunctions extends FrontEnd_Helper_viewHelper{
    public $homePageData = '';
    public function getLeftColumn($gloablData) {
        $this->homePageData = $gloablData;
        $leftBlockDiv = '
        <div class="categories-block">
            <a href="#" class="all">All Categories</a>
            <ul>' 
               .$this->getAllLiForLeftColumn().
            '</ul>
        </div>';
        return $leftBlockDiv;
    }

    public function getAllLiForLeftColumn()
    {
        $topOffer = $this->getLeftColumnOffersHtml('topOffers');
        $newOffer = $this->getLeftColumnOffersHtml('newOffers');
        $categories = $this->getLeftColumnCategoriesHtml();
        $specialListPage = $this->getLeftColumnSpicialListHtml();
        $savingGuides = $this->getLeftColumnSavingGuidesListHtml();
        return $topOffer . $newOffer . $categories . $specialListPage. $savingGuides;
    }

    public function getLeftColumnOffersHtml($offerType)
    {
        $imageName = $offerType=='topOffers' ? PUBLIC_PATH ."images/img-08.png" : PUBLIC_PATH .'images/img-09.png';
        $headerText = $offerType=='topOffers' ? 'Our 10 best coupons' : 'New and Fresh Codes';
        return $leftColumnTopOfferHtml = $this->getLeftColumnsLis($offerType, $imageName, $headerText, 36, $offerType);
    }

    public function getLeftColumnCategoriesHtml()
    {
        $categoriesLisHtml = '';
        $categories = $this->homePageData['topCategories'];
        foreach ($categories as $category) {
           $categoryImage = PUBLIC_PATH_CDN.$category['category']['categoryicon']['path']  .'thum_small_'. $category['category']['categoryicon']['name'];
           $categoriesOffers = $category['countOff'] . " " . $this->zendTranslate->translate('kortingscodes');
           $categoriesLisHtml .= $this->getLeftColumnsLis('categories', $categoryImage, $category['category']['name'], 70, $category['category']['name'], $categoriesOffers);
        }
        return $categoriesLisHtml;
    }

    public function getLeftColumnSpicialListHtml()
    {
        $specialLisPageHtml = '';
        $specialListPages = $this->homePageData['specialPages'];
        foreach ($specialListPages as $indexOfPage=>$specialListPage) {
            $specialListPageOffers = $this->homePageData['specialPagesOffers'][$indexOfPage] . " " . $this->zendTranslate->translate('kortingscodes');
            $specialLisPageHtml .= $this->getLeftColumnsLis('special', '', $specialListPage['page'][0]['pageTitle'], 70, $specialListPage['page'][0]['pageTitle'], $specialListPageOffers);
        }
        return $specialLisPageHtml;
    }

    public function getLeftColumnSavingGuidesListHtml()
    {
        $savingGuideOffers = count($this->homePageData['moneySavingGuides']) . " " . $this->zendTranslate->translate('articles worth your time');
        return $this->getLeftColumnsLis('savingGuide', '', 'Smarter shopping', 70, 'Smarter shopping', $savingGuideOffers);
    }

    public function getLeftColumnsLis($LisDescription, $imageName, $headerText, $imageSize, $imageDescription, $numberOfOffersContent = '')
    {
        $leftColumnsLis =
        '<li>
            <a id="'.$imageDescription.'" href="javascript:void(0)" class="" onclick="showRelaedDiv('.$imageDescription.')">
                <div class="left-box">'
                .  $this->getImageOrSpanTag($LisDescription, $imageName, $imageSize, $imageDescription) .
                '</div>
                <div class="box">
                     <h2>'. $this->zendTranslate->translate($headerText).'</h2>'
                     . $this->getNumberOfOfferSpan($LisDescription, $numberOfOffersContent) .
                     
               '</div>
             </a>
        </li>';
        return $leftColumnsLis;
    }

    public function getNumberOfOfferSpan($listType, $numberOfOffersContent)
    {
        return $listType =='categories' || 'special' ? '<span>' .$numberOfOffersContent. '</span>' : '';
    }

    public function getImageOrSpanTag($listType, $imageName, $imageSize, $imageDescription)
    {
        $imageTagOrSpan = '';
        if($listType =='special' || $listType =='savingGuide') {
        	$listType = $listType=='savingGuide' ? 'FLIP IT' : $listType;
           $imageTagOrSpan = '<span class="discount-label" >' . $this->zendTranslate->translate($listType). '</span>' ;
        } else {
           $imageTagOrSpan = '<img src="'.$imageName.'" width="'.$imageSize.'" height="'.$imageSize.'" alt="'. $imageDescription.'">';
        }
        return $imageTagOrSpan;
    }
}

?>