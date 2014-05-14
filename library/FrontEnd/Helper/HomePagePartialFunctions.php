<?php
class FrontEnd_Helper_HomePagePartialFunctions extends FrontEnd_Helper_viewHelper{
    public $homePageData = '';
    public function getLeftColumn($gloablData) {
        $this->homePageData = $gloablData;
        $leftBlockDiv = '
        <div class="categories-block">
            <a href="' . HTTP_PATH. FrontEnd_Helper_viewHelper::__link('categorieen') .'" class="all">All Categories</a>
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
           $categoriesLisHtml .= $this->getLeftColumnsLis('categories', $categoryImage, $category['category']['name'], 70, $category['category']['permaLink'], $categoriesOffers);
        }
        return $categoriesLisHtml;
    }

    public function getLeftColumnSpicialListHtml()
    {
        $specialLisPageHtml = '';
        $specialListPages = $this->homePageData['specialPages'];
        foreach ($specialListPages as $indexOfPage=>$specialListPage) {
            $specialListPageOffers = count($this->homePageData['specialPagesOffers'][$specialListPage['page'][0]['permaLink']]) . " " . $this->zendTranslate->translate('kortingscodes');
            $specialLisPageHtml .= $this->getLeftColumnsLis('special', '', $specialListPage['page'][0]['pageTitle'], 70, $specialListPage['page'][0]['permaLink'], $specialListPageOffers);
        }
        return $specialLisPageHtml;
    }

    public function getLeftColumnSavingGuidesListHtml()
    {
        $savingGuideOffers = count($this->homePageData['moneySavingGuides']) . " " . $this->zendTranslate->translate('articles worth your time');
        return $this->getLeftColumnsLis('savingGuide', '', 'Smarter shopping', 70, 'moneysaving', $savingGuideOffers);
    }

    public function getLeftColumnsLis($LisDescription, $imageName, $headerText, $imageSize, $imageDescription, $numberOfOffersContent = '')
    {
        $leftColumnsLis =
        '<li>
            <a id="div_'.$imageDescription.'" href="javascript:void(0)" class="" onclick="showRelaedDiv(this)">
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

    public function getRightColumnOffersLis()
    {
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

    public function getRightColumnOffersHtml($offerDivName, $goToAllLink, $linkText, $dynamicDivId = '')
    {
        $divId = $offerDivName=='categories' || $offerDivName=='special' ? $dynamicDivId : $offerDivName;
        $rightOfferColumnHtml = 
        '<div id="div_' . $divId .'" class="vouchers">
            <a href="'. $goToAllLink.'" class="all">'.$this->zendTranslate->translate($linkText).'</a>
            <ul>'.
            $this->getOffersHtml($offerDivName, $dynamicDivId).
           '</ul>
        </div>';
        return $rightOfferColumnHtml;
    }

    public function getOffersHtml($offerDivName, $dynamicDivId)
    {
        $offersHtml = '';
        switch ($offerDivName){
            case 'topOffers':
                $offersHtml = $this->getTopOffersRightCoulumnLis();
                break;
            
            case 'newOffers':
                $offersHtml = $this->getNewestOffersRightCoulumnLis();
                break;
            
            case 'categories':
                $offersHtml = $this->getToCategoryRightCoulumnLis($dynamicDivId);
                break;
            
            case 'special':
                $offersHtml = $this->getSpecialPageRightCoulumnLis($dynamicDivId);
                break;
            
            case 'moneysaving':
                $offersHtml = $this->getMoneySavingGuidesRightCoulumnLis($dynamicDivId);
                break;
            default:
            break;
        }
        return $offersHtml;
    }
 
    public function getMoneySavingGuidesRightCoulumnLis($dynamicDivId)
    {
        $moneySavingGuidestHtml = '';
        foreach ($this->homePageData['moneySavingGuides'] as $savingGuide) {
            $savingImage = PUBLIC_PATH_CDN.ltrim($savingGuide['article']['thumbnail']['path'], "/") .'thum_article_medium_'. $savingGuide['article']['thumbnail']['name'];
            $savingPermalink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('bespaarwijzer').'/'.$savingGuide['article']['permalink'];
            $savingTitle = $savingGuide['article']['title'];
            
            $savingContent = '';
            $allowed_tags = '';
            $description = $savingGuide['article']['chapters'][0]['content'];
            $desc = strip_tags($description, $allowed_tags);
            if(mb_strlen($desc, 'UTF-8') > 50){
                $savingContent =  mb_substr($desc, 0, 50, 'UTF-8') . "...";
            }
            else{
                $savingContent = $desc;
            }
            $savingContent = $savingGuide['article']['title'];
            $moneySavingGuidestHtml .= $this->getRightLiHtml($savingImage, $savingPermalink, $savingTitle, $savingContent, '');
        }
        return $moneySavingGuidestHtml;
    }
    
    public function getSpecialPageRightCoulumnLis($dynamicDivId)
    {
        $specialOffersRightHtml = '';
        foreach ($this->homePageData['specialPagesOffers'][$dynamicDivId] as $specialOffers) {
            $shopImage = PUBLIC_PATH_CDN.ltrim($specialOffers['shop']['logo']['path'],"/") .'thum_small_'. $specialOffers['shop']['logo']['name'];
            $shopPermalink = $specialOffers['shop']['permalink'];
            $shopName = $specialOffers['shop']['name'];
            $offerTitle = $specialOffers['title'];
            $offerExclusiveText = $this->getOfferOptionText($specialOffers['exclusiveCode']);
            $specialOffersRightHtml .= $this->getRightLiHtml($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
        return $specialOffersRightHtml;
    }
    
    public function getToCategoryRightCoulumnLis($dynamicDivId)
    {
        $categoryOffersRightHtml = '';
        foreach ($this->homePageData['topCategoriesOffers'][$dynamicDivId] as $categoryOffers)
        {
            $shopImage = PUBLIC_PATH_CDN.ltrim($categoryOffers['shop']['logo']['path'],"/") .'thum_small_'. $categoryOffers['shop']['logo']['name'];
            $shopPermalink = $categoryOffers['shop']['permalink'];
            $shopName = $categoryOffers['shop']['name'];
            $offerTitle = $categoryOffers['title'];
            $offerExclusiveText = $this->getOfferOptionText($categoryOffers['exclusiveCode']);
            $categoryOffersRightHtml .= $this->getRightLiHtml($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
       return $categoryOffersRightHtml;
    }
    public function getTopOffersRightCoulumnLis()
    {
        $topOfferRightHtml = '';
        foreach ($this->homePageData['topOffers'] as $topOffer) {
            $shopImage = PUBLIC_PATH_CDN.ltrim($topOffer['offer']['shop']['logo']['path'],"/") .'thum_small_'. $topOffer['offer']['shop']['logo']['name'];
            $shopPermalink = $topOffer['offer']['shop']['permalink'];
            $shopName = $topOffer['offer']['shop']['name'];
            $offerTitle = $topOffer['offer']['title'];
            $offerExclusiveText = $this->getOfferOptionText($topOffer['offer']['exclusiveCode']);
            $topOfferRightHtml .= $this->getRightLiHtml($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
        return $topOfferRightHtml;
    }
    
    public function getNewestOffersRightCoulumnLis()
    {
        $newestOfferRightHtml = '';
        foreach ($this->homePageData['newOffers'] as $topOffer) {
            $shopImage = PUBLIC_PATH_CDN.ltrim($topOffer['shop']['logo']['path'],"/") .'thum_small_'. $topOffer['shop']['logo']['name'];
            $shopPermalink = $topOffer['shop']['permaLink'];
            $shopName = $topOffer['shop']['name'];
            $offerTitle = $topOffer['title'];
            $offerExclusiveText = $this->getOfferOptionText($topOffer['exclusiveCode']);
            $newestOfferRightHtml .= $this->getRightLiHtml($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
        }
        return $newestOfferRightHtml;
    }
    public function getRightLiHtml($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText)
    {
        $offerLis = '
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
        return $offerLis;
    }
    
    public function getOfferOptionText($offerExclusive)
    {
        $exclusiveText = $offerExclusive==1 ? '<strong class="exclusive"><span class="glyphicon glyphicon-star"></span>'. $this->zendTranslate->translate('Exclusive'). '</strong>'  : '';
        return $exclusiveText;
    }
}

?>