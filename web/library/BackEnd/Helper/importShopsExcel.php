<?php
class BackEnd_Helper_importShopsExcel
{
    public static function importExcelShops($excelFile)
    {
        defined('DOCTRINE_PATH') || define('DOCTRINE_PATH', LIBRARY_PATH . '/Doctrine1');
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($excelFile);
        $worksheet = $objPHPExcel->getActiveSheet();
        $excelData = array();
        $shopsCounter = 0;
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $excelData[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
            }
            self::assignShopValuesToVariables($excelData[$cell->getRow()][$cell->getColumn()]);
            if (
                (!empty($shopName)
                    && $shopName != 'Shop Name | Text | Required |update if exist otherwise new entry'
                )
                && (
                    !empty($shopNavigationUrl) && $shopNavigationUrl != 'Navigational URL | URL| Required|update if exist otherwise new entry'
                )
                && (
                    !empty($shopTitle)
                    && $shopTitle != 'Title | Text | Required'
                )
                && (
                    !empty($shopSubTitle)
                    && $shopSubTitle != 'Subtitle | Text | Required'
                )
                && (
                    !empty($shopEditor)
                    && $shopEditor != 'Editor Name | Text | Required'
                )
                && (
                    !empty($shopCategory)
                    && $shopCategory != 'Category Name| Required|Multiple possibilities seperate with  , | Text'
                )
                && (
                    !empty($shopRefURL)
                    && $shopRefURL != 'Ref Url | URL | Required'
                )
            ) {
                $shopId = KC\Repository\Shop::getShopIdByShopName($shopName);
                if (!empty($shopId)) {
                    $shopList = \Zend_Registry::get('emLocale')
                        ->getRepository('KC\Entity\Shop')
                        ->find($shopId);
                    $shopList->created_at = $shopList->created_at;
                } else {
                    $shopList = new \Kc\Entity\Shop();
                    $shopList->created_at = new \DateTime('now');
                }

                $shopList->name = $shopName;
                $shopList->permaLink = $shopNavigationUrl;

                if ($overwriteTitle!='') {
                    $shopList->overriteTitle = $overwriteTitle;
                }
                if ($metaDescription!='') {
                    $shopList->metaDescription = $metaDescription;
                }
                if ($allowUserGeneratedContent!='') {
                    $shopList->usergenratedcontent = $allowUserGeneratedContent = 0 ? 0 : 1;
                }
                if ($allowDiscussions!='') {
                    $shopList->discussions = $allowDiscussions = 0 ? 0 : 1;
                }
                if ($shopTitle!='') {
                    $shopList->title = $shopTitle;
                }
                if ($shopSubTitle!='') {
                    $shopList->subTitle = $shopSubTitle;
                }
                if ($shopNotes!='') {
                    $shopList->notes = $shopNotes;
                }
                $shopList->contentManagerName = $shopEditor;
                $userRepo = \Zend_Registry::get('emUser')->getRepository('KC\Entity\User\User');
                $contentManagerName = $userRepo->findOneBy(array('firstName' => $editor));
                if ($contentManagerName) {
                    $shopList->contentManagerId = $contentManagerName->id;
                }
                if ($affiliateNetwork!='') {
                    $affilateNetworkTable = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\AffliateNetwork');
                    $affNetwork = $affilateNetworkTable->findOneBy(array('name' => $affiliateNetwork));
                    if ($affNetwork) {
                        $shopList->affliateNetworkId = $affNetwork->id;
                    }
                }
                $shopList->refUrl = $shopRefURL;
                if ($actualURL != '') {
                    $shopList->actualUrl = $actualURL;
                }
                if ($moneyShop != '') {
                    $shopList->affliateProgram = $moneyShop = 0 ? false : true;
                }
                if (!empty($shopOnline)) {
                    if ($shopOnline == 1) {
                        $shopList->status = 1;
                        $shopList->offlineSicne = null;
                    } else {
                        $shopList->status = 0;
                        $shopList->offlineSicne = new \DateTime('now');
                    }
                } else {
                    $shopList->status = 1;
                }
                if ($shopText != "") {
                    $shopList->shopText = $shopText;
                }
                $shopList->created_at = new \DateTime('now');
                $shopList->deleted = 0;
                $shopList->updated_at = new \DateTime('now');
                $shopsCounter++;
                $entityManagerLocale->persist($shopList);
                $entityManagerLocale->flush();
                $splittedCategories  = explode(',', $shopCategory);
                if (!empty($shopList->id)) {
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    $query = $queryBuilder->delete('KC\Entity\RefShopCategory', 'rf')
                        ->where("rf.shopId=" . $shopList->id)
                        ->getQuery()->execute();
                }
                foreach ($splittedCategories as $key => $categories) {
                    $refShopCategory = new \KC\Entity\RefShopCategory();
                    $refShopCategory->created_at = new \DateTime('now');
                    $refShopCategory->updated_at = new \DateTime('now');
                    $refShopCategory->category = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopList->id);
                    $refShopCategory->shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Category', $categories);
                    \Zend_Registry::get('emLocale')->persist($refShopCategory);
                    \Zend_Registry::get('emLocale')->flush();
                }
                $routeRepo = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\RoutePermalink');
                $routePermalink = $routeRepo->findOneBy(array('permalink' => $shopNavigationUrl));
                if ($routePermalink) {
                    $routePermalink->permalink = FrontEnd_Helper_viewHelper::sanitize($shopNavigationUrl);
                    $routePermalink->type = 'SHP';
                    $routePermalink->exactlink = 'store/storedetail/id/'.$shopList->id;
                    $routePermalink->created_at = $routePermalink->created_at;
                    $routePermalink->updated_at = new \DateTime('now');
                } else {
                    $routePermalink = new \KC\Repository\RoutePermalink();
                    $routePermalink->permalink = FrontEnd_Helper_viewHelper::sanitize($shopNavigationUrl);
                    $routePermalink->type = 'SHP';
                    $routePermalink->exactlink = 'store/storedetail/id/'.$shopList->id;
                    $routePermalink->created_at = new \DateTime('now');
                    $routePermalink->updated_at = new \DateTime('now');
                }
                \Zend_Registry::get('emLocale')->persist($routePermalink);
                \Zend_Registry::get('emLocale')->flush();
                if ($similarShops != '') {
                    $similarstoreordArray = explode(',', $similarShops);
                    $position = 1;
                    if (!empty($shopList->id)) {
                        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                        $query = $queryBuilder->delete('KC\Entity\RefShopRelatedshop', 'rsrs')
                            ->where("rsrs.shop=" . $shopList->id)
                            ->getQuery()->execute();
                    }
                    foreach ($similarstoreordArray as $shop) {
                        if ($shop!='') {
                            $relateshopObj = new \KC\Entity\RefShopRelatedshop();
                            $relateshopObj->shop = \Zend_Registry::get('emLocale')
                                ->getRepository('KC\Entity\Shop')
                                ->find($shopList->id);
                            $relateshopObj->relatedshopId = $shop;
                            $relateshopObj->position = $position;
                            $relateshopObj->created_at = new \DateTime('now');
                            $relateshopObj->updated_at = new \DateTime('now');
                            \Zend_Registry::get('emLocale')->persist($relateshopObj);
                            \Zend_Registry::get('emLocale')->flush();
                            ++$position;
                        }
                    }
                }
            }
            unlink($excelFile);
        }
        return $shopsCounter;
    }

    protected static function assignShopValuesToVariables($excelData)
    {
        echo "<pre>";print_r($excelData);die;
        $shopName = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['A']);
        $shopNavigationUrl = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['B']);
        $moneyShop = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['C']);
        $accountManagerName  = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['D']);
        $affiliateNetwork = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['E']);
        $shopOnline = FrontEnd_Helper_viewHelper::sanitize($excelData[$cell->getRow()]['F']);
        $overwriteTitle = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['G']);
        $metaDescription = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['H']);
        $allowUserGeneratedContent = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['I']);
        $allowDiscussions = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['J']);
        $shopTitle = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['K']);
        $shopSubTitle = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['L']);
        $shopNotes = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['M']);
        $shopEditor = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['N']);
        $shopCategory = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['O']);
        $similarShops = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['P']);
        $shopRefURL = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['Q']);
        $actualURL = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['R']);
        $shopText = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['S']);
        $displaySignupOptions = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['T']);
        $displaySimilarShops = FrontEnd_Helper_viewHelper::sanitize($data[$cell->getRow()]['U']);

        return array(
            'shopName'=>$shopName,
            'shopNavigationUrl'=>$shopNavigationUrl,
            'moneyShop'=>$moneyShop,
            'accountManagerName'=>$accountManagerName,
            'affiliateNetwork'=>$affiliateNetwork,
            'shopOnline'=>$shopOnline,
            'overwriteTitle'=>$overwriteTitle,
            'metaDescription'=>$metaDescription,
            'allowUserGeneratedContent'=>$allowUserGeneratedContent,
            'allowDiscussions'=>$allowDiscussions,
            'shopTitle'=>$shopTitle,
            'shopSubTitle'=>$shopSubTitle,
            'shopNotes'=>$shopNotes,
            'shopEditor'=>$shopEditor,
            'shopCategory'=>$shopCategory,
            'similarShops'=>$similarShops,
            'shopRefURL'=>$shopRefURL,
            'actualURL'=>$actualURL,
            'shopText'=>$shopText,
            'displaySignupOptions'=>$displaySignupOptions,
            'displaySimilarShops'=>$displaySimilarShops
        );
    }
}