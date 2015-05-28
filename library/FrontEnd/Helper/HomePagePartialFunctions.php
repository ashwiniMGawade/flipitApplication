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
            . \FrontEnd_Helper_viewHelper::__link('link_categorieen')
            .'" class="all">'. \FrontEnd_Helper_viewHelper::__form('form_All Categories') .'</a>
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
            ? \FrontEnd_Helper_viewHelper::__form('form_Our 10 best coupons')
            : \FrontEnd_Helper_viewHelper::__form('form_New and Fresh Codes');
        return $leftColumnOffersHtml = $this->getLeftColumnContent(
            $offerType,
            $imageName,
            \FrontEnd_Helper_viewHelper::__form($headerText),
            70,
            $offerType,
            '',
            $offerType=='topOffers' ? '' : 'newOffer'
        );
    }

    public function getLeftColumnCategoriesHtml()
    {
        $categoriesHtml = '';
        $categories = $this->homePageData['topCategories'];
        foreach ($categories as $category) {
            $categoryPath = isset($category['category'][0]['categoryicon']['path'])
                ? $category['category']['categoryicon']['path'] : $category['category']['categoryicon']['path'];
            $categoryName = isset($category['category'][0]['categoryicon']['name'])
                ? $category['category']['categoryicon']['name'] : $category['category']['categoryicon']['name'];
            $categoryImage =
                PUBLIC_PATH_CDN
                .$categoryPath
                . $categoryName;
            $categoriesOffers =
                $category['total_offers'] . ' '
                . \FrontEnd_Helper_viewHelper::__form('form_Offers'). ' ' . $category['total_coupons']
                . " "
                . \FrontEnd_Helper_viewHelper::__form('form_coupons');
            $categoriesHtml .= $this->getLeftColumnContent(
                'categories',
                $categoryImage,
                $category['category']['name'],
                70,
                $category['category']['permaLink'],
                $categoriesOffers,
                $category['category']['id']
            );
        }
        return $categoriesHtml;
    }

    public function getLeftColumnSpicialListHtml()
    {
        $specialPageHtml = '';
        $specialListPages = $this->homePageData['specialPages'];
        foreach ($specialListPages as $indexOfPage => $specialListPage) {
            $specialPageListIndex = $specialListPage['page']['permalink'] .','
               .$specialListPage['page']['pageTitle'];
            $totalCouponsCount = isset($specialListPage['total_offers'])
                ? $specialListPage['total_offers']
                : 0;
            $specialListPageOffers = $totalCouponsCount . " " . \FrontEnd_Helper_viewHelper::__form('form_coupons');
            $specialPageHtml .=
                $this->getLeftColumnContent(
                    'special',
                    '',
                    $specialListPage['page']['pageTitle'],
                    70,
                    $specialListPage['page']['permalink'],
                    $specialListPageOffers
                );
        }
        return $specialPageHtml;
    }

    public function getLeftColumnSavingGuidesListHtml()
    {
        $savingGuideText = $this->homePageData['moneySavingGuidesCount'] . " "
            . \FrontEnd_Helper_viewHelper::__form('form_articles worth your time');
        return $this->getLeftColumnContent(
            'savingGuide',
            '',
            \FrontEnd_Helper_viewHelper::__form('form_Smarter shopping'),
            70,
            'moneysaving',
            $savingGuideText,
            'moneysaving'
        );
    }

    public function getLeftColumnContent(
        $LisDescription,
        $imageName,
        $headerText,
        $imageSize,
        $imageDescription,
        $numberOfOffersContent = '',
        $leftContentId = ''
    ) {
        $leftColumnsLiContent =
        '<li>
            <a data="'. $leftContentId .'" id="div_'.$imageDescription.'" href="javascript:void(0)" class="" onclick="showRelatedDiv(this)">
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
        if ($listType == 'categories') {
            $imageTagOrSpan = self::getLeftPanelImage($imageName, $imageSize, $imageDescription);
        } else {
            if ($listType =='special') {
                $pageLeftImage = KC\Repository\Page::getPageHomeImageByPermalink($imageDescription);
                if (empty($pageLeftImage)) {
                    $imageTagOrSpan =
                    '<span class="discount-label">'
                        . \FrontEnd_Helper_viewHelper::__translate($listType)
                    . '</span>';
                } else {
                    $imageTagOrSpan = self::getLeftPanelImage($pageLeftImage, $imageSize, $imageDescription);
                }
            } else if ($listType =='savingGuide') {
                $pageLeftImage = KC\Repository\Page::getPageHomeImageByPermalink(FrontEnd_Helper_viewHelper::__link('link_plus'));
                if (empty($pageLeftImage)) {
                    $cssClassForPlusImage =  LOCALE=='' ? "kc_menu_image_home" : 'flipit-menu_image_home';
                    $imageTagOrSpan ='<span class="' . $cssClassForPlusImage . '" ></span>';
                } else {
                    $imageTagOrSpan = self::getLeftPanelImage($pageLeftImage, $imageSize, $imageDescription);
                }
            } else if ($listType =='topOffers') {
                $pageLeftImage = KC\Repository\Page::getPageHomeImageByPermalink(FrontEnd_Helper_viewHelper::__link('link_top-20'));
                $pageLeftImage = !empty($pageLeftImage) ? $pageLeftImage : HTTP_PATH ."public/images/img-08.png";
                $imageTagOrSpan = self::getLeftPanelImage($pageLeftImage, $imageSize, $imageDescription);
            } else if ($listType =='newOffers') {
                $pageLeftImage = KC\Repository\Page::getPageHomeImageByPermalink(FrontEnd_Helper_viewHelper::__link('link_nieuw'));
                $pageLeftImage = !empty($pageLeftImage) ? $pageLeftImage : HTTP_PATH ."public/images/img-09.png";
                $imageTagOrSpan = self::getLeftPanelImage($pageLeftImage, $imageSize, $imageDescription);
            }
        }
        return $imageTagOrSpan;
    }

    public function getLeftPanelImage($imageName, $imageSize, $imageDescription)
    {
        $leftImage =
        '<img src="'.$imageName.'" width="'.$imageSize.'" height="'.$imageSize.'" 
        alt="'. $imageDescription.'" title="'. $imageDescription.'">';
        return $leftImage;
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
        $guidesHtml = self::getMoneySavingGuidesRightForAjax(
            $this->homePageData['moneySavingGuidesList'],
            'moneysaving',
            FrontEnd_Helper_viewHelper::__form('form_All Saving Guides'),
            HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_plus')
        );

        $specialListPage = self::getRightColumnSpicialListHtml();
        $categoryListPage = self::renderCategoryData();
        return $topOffer.$newOffer.$categoryListPage.$specialListPage.$guidesHtml;
    }

    public function renderCategoryData()
    {
        $rightDiv = '';
        foreach ($this->homePageData['topCategories'] as $category) {
            $categoryPermalink = HTTP_PATH_LOCALE.FrontEnd_Helper_viewHelper::__link('link_categorieen').'/'.$category['category']['permaLink'];
            $rightDiv.=
            self::getRightDivByAjax(
                $this->homePageData['categoriesOffers'][$category['category']['permaLink']],
                $category['category']['permaLink'],
                FrontEnd_Helper_viewHelper::__form('form_All') . " " . $category['category']['permaLink']. " "
                . FrontEnd_Helper_viewHelper::__form('form_Code'),
                $categoryPermalink
            );
        }
        return $rightDiv;
    }
    
    public function getRightColumnOffersHtml($offerDivName, $goToAllLink, $linkText, $dynamicDivId = '')
    {
        if ($offerDivName == 'special') {
            $splittedDivId = explode(',', $dynamicDivId);
            $divId = $splittedDivId[0];
        } else {
            $divId = $offerDivName;
        }
        $rightOfferColumnHtml =
        '<div id="div_' . $divId .'" class="vouchers">
            <a href="'. $goToAllLink.'" class="all">'.\FrontEnd_Helper_viewHelper::__form($linkText).'</a>
            <ul>'.
            $this->getRightColumnOffersList($offerDivName, $dynamicDivId).
           '</ul>
        </div>';
        return $rightOfferColumnHtml;
    }

    public function getRightColumnSpicialListHtml()
    {
        $specialListHtml = '';
        foreach ($this->homePageData['specialPagesOffers'] as $pageId => $specialPageOffers) {
            $splitedSpecialPageId = explode(',', $pageId);
            $specialListHtml .= $this->getRightColumnOffersHtml(
                'special',
                HTTP_PATH_LOCALE.$splitedSpecialPageId[0],
                \FrontEnd_Helper_viewHelper::__form('form_All') . " " . $splitedSpecialPageId[1]
                . " " . \FrontEnd_Helper_viewHelper::__form('form_Codes'),
                $pageId
            );
        }
        return $specialListHtml;
    }

    public function getRightColumnOffersList($offerDivName, $dynamicDivId)
    {
        $offersHtml = '';
        switch ($offerDivName){
            case 'topOffers':
                $offersHtml = $this->getTopOffersRightColumnList('topOffers');
                break;
            case 'newOffers':
                $offersHtml = $this->getTopOffersRightColumnList('newOffers');
                break;
            case 'special':
                $offersHtml = $this->getSpecialPageRightCoulumnList($dynamicDivId);
                break;
            default:
                break;
        }
        return $offersHtml;
    }

    public function getTopOffersRightColumnList($topOffers)
    {
        $topOfferRightHtml = '';
        if ($topOffers == 'topOffers') {
            $leftPanelSelection = 'topOffers';
        } else {
            $leftPanelSelection = '';
        }
        foreach ($this->homePageData[$topOffers] as $topOffer) {
            $topOfferRightHtml .= $this->getRightColumnOffersHtmlForAllOffersTypes($topOffer, $leftPanelSelection);
        }
        return $topOfferRightHtml;
    }

    public function getSpecialPageRightCoulumnList($dynamicDivId)
    {
        $specialOffersRightHtml = '';
        if (is_array($this->homePageData['specialPagesOffers'][$dynamicDivId])) {
            $topTenSpecialListPageOffers = array_slice($this->homePageData['specialPagesOffers'][$dynamicDivId], 0, 10);
            foreach ($topTenSpecialListPageOffers as $specialOffer) {
                $offers = isset($specialOffer['offers']) ? $specialOffer['offers'] : $specialOffer;
                $specialOffersRightHtml .= $this->getRightColumnOffersHtmlForAllOffersTypes($offers);
            }
        }
        return $specialOffersRightHtml;

    }

    public function getRightColumnOffersHtmlForAllOffersTypes($offer, $leftPanelSelection = '')
    {
        $shopImage = '';
        if (!empty($offer['shopOffers']['logo'])) {
            $shopImage =
                PUBLIC_PATH_CDN.ltrim($offer['shopOffers']['logo']['path'], "/")
                .'thum_medium_'. $offer['shopOffers']['logo']['name'];
        }
        $shopPermalink = $offer['shopOffers']['permaLink'];
        $shopName = $offer['shopOffers']['name'];
        $offerTitle = mb_strlen($offer['title'], 'UTF-8') > 160
            ? mb_substr($offer['title'], 0, 160, 'UTF-8') . "..."
            : $offer['title'];
        $offerExclusiveText = $this->getOfferOptionText($offer['exclusiveCode']);
        return $this->getRighColumnContent(
            $shopImage,
            $shopPermalink,
            $shopName,
            $offerTitle,
            $offerExclusiveText,
            '',
            $leftPanelSelection,
            $offer['id']
        );
    }

    public function getMoneySavingGuidesRightCoulumnList($dynamicDivId)
    {
        $moneySavingGuidestHtml = '';
        $topTenMoneySavingGuides = array_slice($this->homePageData['moneySavingGuides'], 0, 10);
        foreach ($topTenMoneySavingGuides as $savingGuide) {
            $savingImage = PUBLIC_PATH_CDN.ltrim($savingGuide['thumbnail']['path'], "/")
                . $savingGuide['thumbnail']['name'];
            $savingPermalink = \FrontEnd_Helper_viewHelper::__link('link_plus').'/'.$savingGuide['permalink'];

            $savingTitle = mb_strlen($savingGuide['title'], 'UTF-8') > 50
                ? mb_substr($savingGuide['title'], 0, 50, 'UTF-8') . "..."
                : $savingGuide['title'];
                
            $allowed_tags = '';
            $guideDescription = strip_tags(
                isset($savingGuide['chapters'][0]['content'])
                ? $savingGuide['chapters'][0]['content'] : '',
                $allowed_tags
            );
            $savingContent = mb_strlen($guideDescription, 'UTF-8') > 50
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
        $dynamicDivId = '',
        $leftPanelSelection, 
        $offerId = ''
    ) {
        $imageDimensions = 'width="84" height="42"';
        if ($dynamicDivId == 'saving-guides') {
            $imageDimensions = 'width="70"';
        }
        $rightColumnContent = '<li> <div class="top-box">';
        if ($leftPanelSelection == 'topOffers') {
            $rightColumnContent .= ' 
                <div class="logo-box '.$dynamicDivId.'">
                    <img '.$imageDimensions.' alt="' . $shopName .'" src="' . $shopImage .'" title="' . $shopName .'">
                </div>';
        }
        if ($offerId != '') {
            $offerRedirectUrl = HTTP_PATH_LOCALE.$shopPermalink.'?popup='.$offerId.'&type=code#'.$offerId;
        } else {
            $offerRedirectUrl = HTTP_PATH_LOCALE.$shopPermalink;
        }
        $rightColumnContent .= '
                <div class="box">
                    <h3>
                       <span>'. $shopName .'</span>'.$offerExclusiveText.'
                    </h3>
                    <a href="'.$offerRedirectUrl.'" target="_blank" rel = "nofollow">
                        <p class="sub-text">' . FrontEnd_Helper_viewHelper::replaceStringVariableForOfferTitle($offerTitle) .'</p>
                    </a>
                </div>
            </div>
        </li>';
        return $rightColumnContent;
    }

    public function getOfferOptionText($offerExclusive)
    {
        $exclusiveText =
            $offerExclusive==1
            ? '<strong class="exclusive"><span class="glyphicon glyphicon-star"></span>'
            . \FrontEnd_Helper_viewHelper::__translate('Exclusive'). '</strong>'
            : '';
        return $exclusiveText;
    }

    public static function getFlipitHomePageStatus()
    {
        $httpScheme = \FrontEnd_Helper_viewHelper::getServerNameScheme();
        if (HTTP_HOST == $httpScheme.'.flipit.com' && $_SERVER['REQUEST_URI'] == '/') {
            return false;
        } else {
            return true;
        }
    }

    public function getRightDivByAjax($offers, $divId, $textButtonLink, $link)
    {
        $rightDiv =
            '<div id="div_'. $divId .'" class="vouchers">
                <a href="'. $link.'" class="all">'.$textButtonLink.'</a><ul>';
        foreach ($offers as $offer) {
            $rightDiv.= $this->getRightColumnOffersHtmlForAllOffersTypes($offer);
        }
        $rightDiv.='</ul></div>';
        return $rightDiv;
    }

    public function getMoneySavingGuidesRightForAjax($savingGuides, $divId, $textButtonLink, $link)
    {
        $rightDiv =
            '<div id="div_'. $divId .'" class="vouchers">
                <a href="'. $link.'" class="all">'.$textButtonLink.'</a><ul>';
        $moneySavingGuidestHtml = '';
        foreach ($savingGuides as $savingGuide) {
            $savingImage = !empty($savingGuide['articles']['thumbnail'])
                ? PUBLIC_PATH_CDN.ltrim($savingGuide['articles']['thumbnail']['path'], "/"). $savingGuide['articles']['thumbnail']['name']
                : '';
            $savingPermalink = \FrontEnd_Helper_viewHelper::__link('link_plus').'/'.$savingGuide['articles']['permalink'];

            $savingTitle = mb_strlen($savingGuide['articles']['title'], 'UTF-8') > 50
                ? mb_substr($savingGuide['articles']['title'], 0, 50, 'UTF-8') . "..."
                : $savingGuide['articles']['title'];
                
            $allowedTags = '';
            $guideDescription = strip_tags(
                isset($savingGuide['articles']['articleChapter'][0]['content'])
                ? $savingGuide['articles']['articleChapter'][0]['content'] : '',
                $allowedTags
            );
            $savingContent = mb_strlen($guideDescription, 'UTF-8') > 85
                ? mb_substr($guideDescription, 0, 85, 'UTF-8') . "..."
                : $guideDescription;
            $moneySavingGuidestHtml .= $this->getRighColumnContent(
                $savingImage,
                $savingPermalink,
                $savingTitle,
                $savingContent,
                '',
                'saving-guides',
                ''
            );
        }
        $rightDiv.=$moneySavingGuidestHtml .'</ul></div>';
        return $rightDiv;
    }
}
