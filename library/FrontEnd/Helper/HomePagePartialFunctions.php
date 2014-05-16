<?php
class FrontEnd_Helper_HomePagePartialFunctions extends FrontEnd_Helper_viewHelper{

    public $homePageData = '';
    public function getHomePageLeftColumn($homePageData) {
        $this->homePageData = $homePageData;
        $leftBlockList = '
        <div class="categories-block">
            <a href="' . HTTP_PATH. FrontEnd_Helper_viewHelper::__link('categorieen') .'" class="all">'. $this->zendTranslate->translate('All Categories') .'</a>
            <ul>' 
               .$this->getHomePageLeftColumnList().
            '</ul>
        </div>';
        return $leftBlockList;
    }

    public function getHomePageLeftColumnList() {
        $topOffer = $this->getLeftColumnOffersHtml('topOffers');
        $newOffer = $this->getLeftColumnOffersHtml('newOffers');
        $categories = $this->getLeftColumnCategoriesHtml();
        $specialListPage = $this->getLeftColumnSpicialListHtml();
        $savingGuides = $this->getLeftColumnSavingGuidesListHtml();
        return $topOffer . $newOffer . $categories . $specialListPage. $savingGuides;
    }

    public function getLeftColumnOffersHtml($offerType) {
        $imageName = $offerType=='topOffers' ? HTTP_PATH ."public/images/img-08.png" : HTTP_PATH .'public/images/img-09.png';
        $headerText = $offerType=='topOffers' ? 'Our 10 best coupons' : 'New and Fresh Codes';
        return $leftColumnOffersHtml = $this->getLeftColumnContent($offerType, $imageName, $headerText, 36, $offerType);
    }

    public function getLeftColumnCategoriesHtml() {
        $categoriesHtml = '';
        $categories = $this->homePageData['topCategories'];
        foreach ($categories as $category) {
           $categoryImage = PUBLIC_PATH_CDN.$category['category']['categoryicon']['path']  .'thum_small_'. $category['category']['categoryicon']['name'];
           $categoriesOffers = $category['totalOffers'] . ' ' . $this->zendTranslate->translate('Offers'). ' ' . $category['countOff'] . " " . $this->zendTranslate->translate('coupons');
           $categoriesHtml .= $this->getLeftColumnContent('categories', $categoryImage, $category['category']['name'], 70, $category['category']['permaLink'], $categoriesOffers);
        }
        return $categoriesHtml;
    }

    public function getLeftColumnSpicialListHtml() {
        $specialPageHtml = '';
        $specialListPages = $this->homePageData['specialPages'];
        foreach ($specialListPages as $indexOfPage=>$specialListPage) {
            $totalCoupon = intval($specialListPage['totalCoupons']);
            $totalOffers = intval($specialListPage['totalOffers']);
            $totalCouponsCount = count($this->homePageData['specialPagesOffers'][$specialListPage['page'][0]['permaLink']]);
            $totalOffersOfSpecialPage = (intval($totalCouponsCount) - $totalCoupon) + $totalOffers;
            $specialListPageOffers = $totalOffersOfSpecialPage . ' ' . $this->zendTranslate->translate('Offers'). ' ' . $totalCouponsCount . " " . $this->zendTranslate->translate('coupons');
            $specialPageHtml .= $this->getLeftColumnContent('special', '', $specialListPage['page'][0]['pageTitle'], 70, $specialListPage['page'][0]['permaLink'], $specialListPageOffers);
        }
        return $specialPageHtml;
    }

    public function getLeftColumnSavingGuidesListHtml() {
        $savingGuideOffers = count($this->homePageData['moneySavingGuides']) . " " . $this->zendTranslate->translate('articles worth your time');
        return $this->getLeftColumnContent('savingGuide', '', 'Smarter shopping', 70, 'moneysaving', $savingGuideOffers);
    }

    public function getLeftColumnContent($LisDescription, $imageName, $headerText, $imageSize, $imageDescription, $numberOfOffersContent = '') {
        $leftColumnsLiContent =
        '<li>
            <a id="div_'.$imageDescription.'" href="javascript:void(0)" class="" onclick="showRelatedDiv(this)">
                <div class="left-box">'
                .  $this->getImageOrSpanTag($LisDescription, $imageName, $imageSize, $imageDescription) .
                '</div>
                <div class="box">
                     <h2>'. $this->zendTranslate->translate($headerText).'</h2>'
                     . $this->getNumberOfOfferSpan($LisDescription, $numberOfOffersContent) .
                     
               '</div>
             </a>
        </li>';
        return $leftColumnsLiContent;
    }

    public function getNumberOfOfferSpan($listType, $numberOfOffersContent) {
        return $listType =='categories' || 'special' ? '<span>' .$numberOfOffersContent. '</span>' : '';
    }

    public function getImageOrSpanTag($listType, $imageName, $imageSize, $imageDescription) {
        $imageTagOrSpan = '';
        if($listType =='special' || $listType =='savingGuide') {
            $listType = $listType=='savingGuide' ? 'FLIP IT' : $listType;
           $imageTagOrSpan = '<span class="discount-label" >' . $this->zendTranslate->translate($listType). '</span>' ;
        } else {
           $imageTagOrSpan = '<img src="'.$imageName.'" width="'.$imageSize.'" height="'.$imageSize.'" alt="'. $imageDescription.'">';
        }
        return $imageTagOrSpan;
    }

    public function getHomePageRightColumnOffersList() {
        $topOffer = $this->getRightColumnOffersHtml('topOffers', HTTP_PATH. FrontEnd_Helper_viewHelper::__link('top20'), 'All Top Codes');
        $newOffer = $this->getRightColumnOffersHtml('newOffers', HTTP_PATH. FrontEnd_Helper_viewHelper::__link('nieuw'), 'All New Codes');
        $categories = $this->getRighColumnCategoriesHtml();
        $specialListPage = $this->getRightColumnSpicialListHtml();
        $savingGuides = $this->getRightColumnSavingGuidesListHtml();
        return $topOffer . $newOffer . $categories . $specialListPage.$savingGuides;
    }

    public function getRightColumnSavingGuidesListHtml() {
        return $this->getRightColumnOffersHtml('moneysaving', HTTP_PATH. FrontEnd_Helper_viewHelper::__link('bespaarwijzer') , 'All Saving Guides', 'moneysaving'); 
    }

    public function getRightColumnSpicialListHtml() {
        $specialListHtml = '';
        foreach ($this->homePageData['specialPagesOffers'] as $pageId=>$specialPageOffers) {
            $specialListHtml .= $this->getRightColumnOffersHtml('special', $pageId , 'All Special Codes', $pageId);
        }
        return $specialListHtml;
    }

     public function getRighColumnCategoriesHtml() {
         $categegoriesHtml = '';
         foreach ($this->homePageData['topCategoriesOffers'] as $categoryId=>$topCategorieOffers) {
           $categegoriesHtml .= $this->getRightColumnOffersHtml('categories', HTTP_PATH. FrontEnd_Helper_viewHelper::__link('categorieen') .'/'. $categoryId, 'All Category Codes', $categoryId);
         }
         return $categegoriesHtml;
    }

    public function getRightColumnOffersHtml($offerDivName, $goToAllLink, $linkText, $dynamicDivId = '') {
        $divId = $offerDivName=='categories' || $offerDivName=='special' ? $dynamicDivId : $offerDivName;
        $rightOfferColumnHtml = 
        '<div id="div_' . $divId .'" class="vouchers">
            <a href="'. $goToAllLink.'" class="all">'.$this->zendTranslate->translate($linkText).'</a>
            <ul>'.
            $this->getRightColumnOffersList($offerDivName, $dynamicDivId).
           '</ul>
        </div>';
        return $rightOfferColumnHtml;
    }

    public function getRightColumnOffersList($offerDivName, $dynamicDivId) {
        $offersHtml = '';
        switch ($offerDivName){
            case 'topOffers':
                $offersHtml = $this->getTopOffersRightCoulumnList();
                break;

            case 'newOffers':
                $offersHtml = $this->getNewestOffersRightCoulumnList();
                break;

            case 'categories':
                $offersHtml = $this->getToCategoryRightCoulumnList($dynamicDivId);
                break;

            case 'special':
                $offersHtml = $this->getSpecialPageRightCoulumnList($dynamicDivId);
                break;

            case 'moneysaving':
                $offersHtml = $this->getMoneySavingGuidesRightCoulumnList($dynamicDivId);
                break;
            default:
            break;
        }
        return $offersHtml;
    }
 
    public function getTopOffersRightCoulumnList() {
        $topOfferRightHtml = '';
        foreach ($this->homePageData['topOffers'] as $topOffer) {
            $shopImage = PUBLIC_PATH_CDN.ltrim($topOffer['offer']['shop']['logo']['path'],"/") .'thum_small_'. $topOffer['offer']['shop']['logo']['name'];
            $shopPermalink = $topOffer['offer']['shop']['permalink'];
            $shopName = $topOffer['offer']['shop']['name'];
            $offerTitle = $topOffer['offer']['title'];
            $offerExclusiveText = $this->getOfferOptionText($topOffer['offer']['exclusiveCode']);
            $topOfferRightHtml .= $this->getRighColumnContent($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
        return $topOfferRightHtml;
    }

    public function getNewestOffersRightCoulumnList() {
        $newestOfferRightHtml = '';
        foreach ($this->homePageData['newOffers'] as $topOffer) {
            $shopImage = PUBLIC_PATH_CDN.ltrim($topOffer['shop']['logo']['path'],"/") .'thum_small_'. $topOffer['shop']['logo']['name'];
            $shopPermalink = $topOffer['shop']['permaLink'];
            $shopName = $topOffer['shop']['name'];
            $offerTitle = $topOffer['title'];
            $offerExclusiveText = $this->getOfferOptionText($topOffer['exclusiveCode']);
            $newestOfferRightHtml .= $this->getRighColumnContent($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
        return $newestOfferRightHtml;
    }

    public function getToCategoryRightCoulumnList($dynamicDivId) {
        $categoryOffersRightHtml = '';
        foreach ($this->homePageData['topCategoriesOffers'][$dynamicDivId] as $categoryOffers)
        {
            $shopImage = PUBLIC_PATH_CDN.ltrim($categoryOffers['shop']['logo']['path'],"/") .'thum_small_'. $categoryOffers['shop']['logo']['name'];
            $shopPermalink = $categoryOffers['shop']['permalink'];
            $shopName = $categoryOffers['shop']['name'];
            $offerTitle = $categoryOffers['title'];
            $offerExclusiveText = $this->getOfferOptionText($categoryOffers['exclusiveCode']);
            $categoryOffersRightHtml .= $this->getRighColumnContent($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
       return $categoryOffersRightHtml;
    }

    public function getSpecialPageRightCoulumnList($dynamicDivId) {
        $specialOffersRightHtml = '';
        foreach ($this->homePageData['specialPagesOffers'][$dynamicDivId] as $specialOffer) {
            $shopImage = PUBLIC_PATH_CDN.ltrim($specialOffer['shop']['logo']['path'],"/") .'thum_small_'. $specialOffer['shop']['logo']['name'];
            $shopPermalink = $specialOffer['shop']['permalink'];
            $shopName = $specialOffer['shop']['name'];
            $offerTitle = $specialOffer['title'];
            $offerExclusiveText = $this->getOfferOptionText($specialOffer['exclusiveCode']);
            $specialOffersRightHtml .= $this->getRighColumnContent($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
        return $specialOffersRightHtml;
    }

    public function getMoneySavingGuidesRightCoulumnList($dynamicDivId) {
        $moneySavingGuidestHtml = '';
        foreach ($this->homePageData['moneySavingGuides'] as $savingGuide) {
            $savingImage = PUBLIC_PATH_CDN.ltrim($savingGuide['article']['thumbnail']['path'], "/") .'thum_article_medium_'. $savingGuide['article']['thumbnail']['name'];
            $savingPermalink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer').'/'.$savingGuide['article']['permalink'];
            $savingTitle = $savingGuide['article']['title'];
            $allowed_tags = '';
            $guideDescription = strip_tags($savingGuide['article']['chapters'][0]['content'], $allowed_tags);
            $savingContent = mb_strlen($guideDescription, 'UTF-8') > 50 ? mb_substr($guideDescription, 0, 50, 'UTF-8') . "..." : $guideDescription;
            $moneySavingGuidestHtml .= $this->getRighColumnContent($savingImage, $savingPermalink, $savingTitle, $savingContent, '');
        }
        return $moneySavingGuidestHtml;
    }

    public function getRighColumnContent($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText) {
        $rightColumnContent = '
        <li>
            <a href="'.$shopPermalink.'">
                <div class="logo-box">
                    <img width="80" height="19" alt="' . $shopName .'" src="' . $shopImage .'">
                </div>
                <div class="box">
                    <h2>
                       <span>'. $shopName .'</span>'.$offerExclusiveText.'
                    </h2>
                   <p>' . $offerTitle .'</p>
                </div>
            </a>
        </li>';
        return $rightColumnContent;
    }

    public function getOfferOptionText($offerExclusive) {
        $exclusiveText = $offerExclusive==1 ? '<strong class="exclusive"><span class="glyphicon glyphicon-star"></span>'. $this->zendTranslate->translate('Exclusive'). '</strong>'  : '';
        return $exclusiveText;
    }
}

?>