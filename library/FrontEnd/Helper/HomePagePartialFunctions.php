<?php
class FrontEnd_Helper_HomePagePartialFunctions
{

    public $homePageData = '';
    public function getHomePageLeftColumn($homePageData)
    {
        $this->homePageData = $homePageData;
        $leftBlockList = '
        <div class="categories-block">
            <a href="'
            . HTTP_PATH_LOCALE
            . FrontEnd_Helper_viewHelper::__link('link_categorieen')
            .'" class="all">'. FrontEnd_Helper_viewHelper::__form('form_All Categories') .'</a>
            <ul>'
               .$this->getHomePageLeftColumnList().
            '</ul>
        </div>';
        return $leftBlockList;
    }

    public function getHomePageLeftColumnList()
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
        $imageName =
            $offerType=='topOffers'
            ? HTTP_PATH ."public/images/img-08.png"
            : HTTP_PATH .'public/images/img-09.png';
        $headerText =
            $offerType=='topOffers'
            ? FrontEnd_Helper_viewHelper::__form('form_Our 10 best coupons')
            : FrontEnd_Helper_viewHelper::__form('form_New and Fresh Codes');
        return $leftColumnOffersHtml = $this->getLeftColumnContent(
            $offerType,
            $imageName,
            FrontEnd_Helper_viewHelper::__form($headerText),
            36,
            $offerType
        );
    }

    public function getLeftColumnCategoriesHtml()
    {
        $categoriesHtml = '';
        $categories = $this->homePageData['topCategories'];
        foreach ($categories as $category) {
            $categoryImage =
                PUBLIC_PATH_CDN
                .$category['category']['categoryicon']['path']
                . $category['category']['categoryicon']['name'];
            $categoriesOffers =
                $category['totalOffers'] . ' '
                . FrontEnd_Helper_viewHelper::__form('form_Offers'). ' ' . $category['countOff'] . " "
                . FrontEnd_Helper_viewHelper::__form('form_coupons');
            $categoriesHtml .= $this->getLeftColumnContent(
                'categories',
                $categoryImage,
                $category['category']['name'],
                70,
                $category['category']['permaLink'],
                $categoriesOffers
            );
        }
        return $categoriesHtml;
    }

    public function getLeftColumnSpicialListHtml()
    {
        $specialPageHtml = '';
        $specialListPages = $this->homePageData['specialPages'];
        foreach ($specialListPages as $indexOfPage => $specialListPage) {
            $totalCoupon = intval($specialListPage['totalCoupons']);
            $totalOffers = intval($specialListPage['totalOffers']);
            $specialPageListIndex = $specialListPage['page'][0]['permaLink'] .','
               .$specialListPage['page'][0]['pageTitle'];
            $totalCouponsCount = count($this->homePageData['specialPagesOffers'][$specialPageListIndex]);
            $specialListPageOffers = $totalCouponsCount . " " . FrontEnd_Helper_viewHelper::__form('form_coupons');
            $specialPageHtml .=
                $this->getLeftColumnContent(
                    'special',
                    '',
                    $specialListPage['page'][0]['pageTitle'],
                    70,
                    $specialListPage['page'][0]['permaLink'],
                    $specialListPageOffers
                );
        }
        return $specialPageHtml;
    }

    public function getLeftColumnSavingGuidesListHtml()
    {
        $savingGuideOffers =
            count($this->homePageData['moneySavingGuides']) . " "
            . FrontEnd_Helper_viewHelper::__form('form_articles worth your time');
        return $this->getLeftColumnContent(
            'savingGuide',
            '',
            FrontEnd_Helper_viewHelper::__form('form_Smarter shopping'),
            70,
            'moneysaving',
            $savingGuideOffers
        );
    }

    public function getLeftColumnContent(
        $LisDescription,
        $imageName,
        $headerText,
        $imageSize,
        $imageDescription,
        $numberOfOffersContent = ''
    ) {
        $leftColumnsLiContent =
        '<li>
            <a id="div_'.$imageDescription.'" href="javascript:void(0)" class="" onclick="showRelatedDiv(this)">
                <div class="left-box">'
                .  $this->getImageOrSpanTag($LisDescription, $imageName, $imageSize, $imageDescription) .
                '</div>
                <div class="box">
                     <h2>'. $headerText.'</h2>'
                     . $this->getNumberOfOfferSpan($LisDescription, $numberOfOffersContent) .
                     
               '</div>
             </a>
        </li>';
        return $leftColumnsLiContent;
    }

    public function getNumberOfOfferSpan($listType, $numberOfOffersContent)
    {
        return $listType =='categories' || 'special' ? '<span>' .$numberOfOffersContent. '</span>' : '';
    }

    public function getImageOrSpanTag($listType, $imageName, $imageSize, $imageDescription)
    {
        $imageTagOrSpan = '';
        if ($listType =='special' || $listType =='savingGuide') {
            $cssClassForPlusImage = $listType == 'savingGuide' ? 'home_plus_menu_image' : 'discount-label';
            $imageTagOrSpan =
                '<span class="' . $cssClassForPlusImage . '" >'
                    . FrontEnd_Helper_viewHelper::__form($listType)
                . '</span>' ;
        } else {
            $imageTagOrSpan =
            '<img src="'.$imageName.'" width="'.$imageSize.'" height="'.$imageSize.'" 
            alt="'. $imageDescription.'">';
        }
        return $imageTagOrSpan;
    }

    public function getHomePageRightColumnOffersList()
    {
        $topOffer = $this->getRightColumnOffersHtml(
            'topOffers',
            HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_top-20'),
            FrontEnd_Helper_viewHelper::__form('form_All Top Codes')
        );
        $newOffer = $this->getRightColumnOffersHtml(
            'newOffers',
            HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_nieuw'),
            FrontEnd_Helper_viewHelper::__form('form_All New Codes')
        );
        $categories = $this->getRighColumnCategoriesHtml();
        $specialListPage = $this->getRightColumnSpicialListHtml();
        $savingGuides = $this->getRightColumnSavingGuidesListHtml();
        return $topOffer . $newOffer . $categories . $specialListPage.$savingGuides;
    }

    public function getRightColumnSavingGuidesListHtml()
    {
        return $this->getRightColumnOffersHtml(
            'moneysaving',
            HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_plus'),
            FrontEnd_Helper_viewHelper::__form('form_All Saving Guides'),
            FrontEnd_Helper_viewHelper::__form('form_moneysaving')
        );
    }

    public function getRightColumnSpicialListHtml()
    {
        $specialListHtml = '';
        foreach ($this->homePageData['specialPagesOffers'] as $pageId => $specialPageOffers) {
            $splitedSpecialPageId = explode(',', $pageId);
            $specialListHtml .= $this->getRightColumnOffersHtml(
                'special',
                HTTP_PATH_LOCALE.$splitedSpecialPageId[0],
                FrontEnd_Helper_viewHelper::__form('form_All') . " " . $splitedSpecialPageId[1]
                . " " . FrontEnd_Helper_viewHelper::__form('form_Codes'),
                $pageId
            );
        }
        return $specialListHtml;
    }

    public function getRighColumnCategoriesHtml()
    {
        $categegoriesHtml = '';
        foreach ($this->homePageData['topCategoriesOffers'] as $categoryId => $topCategoryOffers) {
            $splitedCategoryId = explode(',', $categoryId);
            $categegoriesHtml .= $this->getRightColumnOffersHtml(
                'categories',
                HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/'. $splitedCategoryId[0],
                FrontEnd_Helper_viewHelper::__form('form_All') . " " . $splitedCategoryId[1] . " "
                . FrontEnd_Helper_viewHelper::__form('form_Code'),
                $categoryId
            );
        }
         return $categegoriesHtml;
    }

    public function getRightColumnOffersHtml($offerDivName, $goToAllLink, $linkText, $dynamicDivId = '')
    {
        if ($offerDivName == 'categories' || $offerDivName == 'special') {
            $splitedDivId = explode(',', $dynamicDivId);
            $divId = $splitedDivId[0];
        } else {
            $divId = $offerDivName;
        }
        $rightOfferColumnHtml =
        '<div id="div_' . $divId .'" class="vouchers">
            <a href="'. $goToAllLink.'" class="all">'.FrontEnd_Helper_viewHelper::__form($linkText).'</a>
            <ul>'.
            $this->getRightColumnOffersList($offerDivName, $dynamicDivId).
           '</ul>
        </div>';
        return $rightOfferColumnHtml;
    }

    public function getRightColumnOffersList($offerDivName, $dynamicDivId)
    {
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
 
    public function getTopOffersRightCoulumnList()
    {
        $topOfferRightHtml = '';
        foreach ($this->homePageData['topOffers'] as $topOffer) {
            $topOfferRightHtml .= $this->getRightColumnOffersHtmlForAllOffersTypes($topOffer);
        }
        return $topOfferRightHtml;
    }

    public function getNewestOffersRightCoulumnList()
    {
        $newestOfferRightHtml = '';
        foreach ($this->homePageData['newOffers'] as $newOffer) {
            $newestOfferRightHtml .= $this->getRightColumnOffersHtmlForAllOffersTypes($newOffer);
        }
        return $newestOfferRightHtml;
    }

    public function getToCategoryRightCoulumnList($dynamicDivId)
    {
        $categoryOffersRightHtml = '';
        $topTenCategoryOffers = array_slice($this->homePageData['topCategoriesOffers'][$dynamicDivId], 0, 10);
        foreach ($topTenCategoryOffers as $categoryOffer) {
            $categoryOffersRightHtml .= $this->getRightColumnOffersHtmlForAllOffersTypes($categoryOffer);
        }
        return $categoryOffersRightHtml;
    }

    public function getSpecialPageRightCoulumnList($dynamicDivId)
    {
        $specialOffersRightHtml = '';
        $topTenSpecialListPageOffers = array_slice($this->homePageData['specialPagesOffers'][$dynamicDivId], 0, 10);
        foreach ($topTenSpecialListPageOffers as $specialOffer) {
            $specialOffersRightHtml .= $this->getRightColumnOffersHtmlForAllOffersTypes($specialOffer);
        }
        return $specialOffersRightHtml;
    }

    public function getRightColumnOffersHtmlForAllOffersTypes($offer)
    {
        $shopImage =
            PUBLIC_PATH_CDN.ltrim($offer['shop']['logo']['path'], "/") .'thum_small_'. $offer['shop']['logo']['name'];
        $shopPermalink = $offer['shop']['permalink'];
        $shopName = $offer['shop']['name'];
        $offerTitle = $offer['title'];
        $offerExclusiveText = $this->getOfferOptionText($offer['exclusiveCode']);
        return $this->getRighColumnContent($shopImage, $shopPermalink, $shopName, $offerTitle, $offerExclusiveText);
    }

    public function getMoneySavingGuidesRightCoulumnList($dynamicDivId)
    {
        $moneySavingGuidestHtml = '';
        $topTenMoneySavingGuides = array_slice($this->homePageData['moneySavingGuides'], 0, 10);
        foreach ($topTenMoneySavingGuides as $savingGuide) {
            $savingImage =
                PUBLIC_PATH_CDN.ltrim($savingGuide['thumbnail']['path'], "/")
                . $savingGuide['thumbnail']['name'];
            $savingPermalink =
                FrontEnd_Helper_viewHelper::__link('link_plus').'/'.$savingGuide['permalink'];
            $savingTitle = $savingGuide['title'];
            $allowed_tags = '';
            $guideDescription = strip_tags($savingGuide['chapters'][0]['content'], $allowed_tags);
            $savingContent =
                mb_strlen($guideDescription, 'UTF-8') > 50
                ? mb_substr($guideDescription, 0, 50, 'UTF-8') . "..."
                : $guideDescription;
            $moneySavingGuidestHtml .= $this->getRighColumnContent(
                $savingImage,
                $savingPermalink,
                $savingTitle,
                $savingContent,
                '',
                'saving-guides'
            );
        }
        return $moneySavingGuidestHtml;
    }

    public function getRighColumnContent(
        $shopImage,
        $shopPermalink,
        $shopName,
        $offerTitle,
        $offerExclusiveText,
        $dynamicDivId = ''
    ) {
        $imageDimensions = 'width="84" height="42"';
        if ($dynamicDivId == 'saving-guides') {
            $imageDimensions = 'width="70"';
        }

        $rightColumnContent = '
        <li>
            <a href="'.HTTP_PATH_LOCALE.$shopPermalink.'">
                <div class="logo-box '.$dynamicDivId.'">
                    <img '.$imageDimensions.' alt="' . $shopName .'" src="' . $shopImage .'">
                </div>
                <div class="box">
                    <h2>
                       <span>'. $shopName .'</span>'.$offerExclusiveText.'
                    </h2>
                   <p class="sub-text">' . $offerTitle .'</p>
                </div>
            </a>
        </li>';
        return $rightColumnContent;
    }

    public function getOfferOptionText($offerExclusive)
    {
        $exclusiveText =
            $offerExclusive==1
            ? '<strong class="exclusive"><span class="glyphicon glyphicon-star"></span>'
            . FrontEnd_Helper_viewHelper::__translate('Exclusive'). '</strong>'
            : '';
        return $exclusiveText;
    }

    public static function getFlipitHomePageStatus()
    {
        if (HTTP_HOST == 'www.flipit.com' && $_SERVER['REQUEST_URI'] == '/') {
                return false;
        } else {
                return true;
        }
    }
}
