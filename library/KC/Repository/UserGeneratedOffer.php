<?php
namespace KC\Repository;

class UserGeneratedOffer extends \KC\Entity\Offer
{
    public static function getofferList($parameters)
    {
        $userRole           = \Auth_StaffAdapter::getIdentity()->users->id;
        $searchOffer        = $parameters["offerText"]!='undefined' ? $parameters["offerText"] : '';
        $searchShop         = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCouponType   = $parameters["couponType"]!='undefined' ? $parameters["couponType"] : '';
        $deletedStatus      = $parameters['flag'];
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $getOffersQuery = $entityManagerUser
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->setParameter(1, $deletedStatus)
            ->where('o.deleted = ?1')
            ->setParameter(2, 1)
            ->andWhere('o.userGenerated = ?2');
        if ($userRole=='4') {
            $getOffersQuery->setParameter(3, 'DE');
            $getOffersQuery->andWhere('o.Visability =?3');
        }
        if ($searchOffer != '') {
            $getOffersQuery->andWhere("o.title LIKE '%$searchOffer%'");
        }
        if ($searchShop!='') {
            $getOffersQuery->andWhere("s.name LIKE '%$searchShop%'");
        }
        if ($searchCouponType!='') {
            $getOffersQuery->andWhere('o.discountType ='."'".$searchCouponType."'");
        }

        $request  = \DataTable_Helper::createSearchRequest(
            $parameters,
            array('o.title','s.name','o.discountType','o.refURL','o.couponcode','o.startDate',
                'o.endDate', 'o.totalViewcount','o.authorName'
            )
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($getOffersQuery)
            ->add('text', 'o.title')
            ->add('text', 's.name as shopname')
            ->add('text', 'o.discountType')
            ->add('text', 'o.refURL')
            ->add('text', 'o.couponCode')
            ->add('number', 'o.startDate')
            ->add('number', 'o.endDate')
            ->add('number', 'o.totalViewcount')
            ->add('text', 'o.authorName');
           
        $offersList = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $offersList = \DataTable_Helper::getResponse($offersList, $request);
        return $offersList;
    }

    public static function moveToTrash($id)
    {
        if ($id) {
            $entityManager  = \Zend_Registry::get('emLocale');
            $u = $entityManager->find('KC\Entity\Offer', $id);
            $u->deleted = 1;
            $key = '6_topOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_latestUpdates'  .$u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            $entityManager->persist($u);
            $entityManager->flush();
        } else {
            $id = null;
        }
        return $id;
    }

    public static function deleteOffer($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $entityManager  = \Zend_Registry::get('emLocale');
            $u = $entityManager->find('KC\Entity\Offer', $id);
            $key = '6_topOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_latestUpdates'  .$u->shopOffers->id. '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            $del = $queryBuilder->delete('KC\Entity\Offer', 'o')
                ->where("o.id=" . $id)
                ->getQuery()
                ->execute();
        } else {
            $id = null;
        }
        return $id;
    }

    public static function restoreOffer($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $entityManager  = \Zend_Registry::get('emLocale');
            $u = $entityManager->find('KC\Entity\Offer', $id);
            $key = '6_topOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_latestUpdates'  .$u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            $O = $queryBuilder
                ->update('KC\Entity\Offer', 'o')
                ->set('o.deleted', '0')
                ->where('o.id=' . $id)
                ->getQuery();
            $O->execute();
        } else {
            $id = null;
        }
        return $id;
    }

    public static function makeToOffline($id, $offvalue)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $entityManager  = \Zend_Registry::get('emLocale');
            $u = $entityManager->find('KC\Entity\Offer', $id);
            $key = '6_topOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_latestUpdates'  .$u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u->shopOffers->id . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            $O = $queryBuilder
                ->update('KC\Entity\Offer', 'o')
                ->set('o.offline', $offvalue)
                ->where('o.id=' . $id)
                ->getQuery();
            $O->execute();
        } else {
            $id = null;
        }
        return $id;
    }

    public static function searchToFiveOffer($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('o.title as title')
            ->from("KC\Entity\Offer", "o")
            ->where($queryBuilder->expr()->eq('o.deleted', $queryBuilder->expr()->literal($flag)))
            ->andWhere('o.offline = 0')
            ->andWhere($queryBuilder->expr()->like('o.title', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere($queryBuilder->expr()->eq('o.userGenerated', 1))
            ->orderBy("o.title", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchToFiveShop($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('o.id,s.name as name')
            ->from("KC\Entity\Offer", "o")
            ->leftJoin('o.shopOffers', 's')
            ->where($queryBuilder->expr()->eq('o.deleted', $queryBuilder->expr()->literal($flag)))
            ->andWhere('s.status = 1')
            ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere($queryBuilder->expr()->eq('o.userGenerated', 1))
            ->orderBy("s.id", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public function saveOffer($params)
    {
        $offer = new \KC\Entity\Offer();
        if (isset($params['defaultoffercheckbox'])) {
            $offer->Visability = 'DE';
            if ($params['selctedshop']!='') {
                $offer->shopOffers = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $params['selctedshop']);
            }
        } else {
            $offer->Visability = 'MEM';
        }

        if (isset($params['couponCodeCheckbox'])) {
            $offer->discountType = 'CD';
            $offer->couponCode = $params['couponCode'];
        } else if (isset($params['couponCodeCheckbox'])) {
            $offer->discountType = 'SL';
        } else {
            $offer->discountType = 'PA';

            if (isset($params['uploadoffercheck'])) {
                $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  \BackEnd_Helper_viewHelper::getImageExtension($fileName);
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, $fileName, $matches);
                if (@$matches[1]) {
                    $offerImage  = new \KC\Entity\Image();
                    $offerImage->ext = $ext;
                    $offerImage->path = 'images/upload/offer/';
                    $offerImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($fileName);
                    $offerImage->deleted = 0;
                    $offerImage->created_at = new \DateTime('now');
                    $offerImage->updated_at = new \DateTime('now');
                    \Zend_Registry::get('emLocale')->persist($offerImage);
                    \Zend_Registry::get('emLocale')->flush();
                    $offer->logo = \Zend_Registry::get('emLocale')->find('KC\Entity\Image', $offerImage->id);
                }
            } else {
                $offer->refOfferUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }

        $offer->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);
        if (isset($params['deepLinkStatus'])) {
            $offer->refURL =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        }

        $offer->startDate = date('Y-m-d', strtotime($params['offerStartDate']))
        .' '.date(
            'H:i',
            strtotime($params['offerstartTime'])
        );
        $offer->endDate = date('Y-m-d', strtotime($params['offerEndDate']))
        .' '.date(
            'H:i',
            strtotime($params['offerendTime'])
        );

        if (isset($params['extendedoffercheckbox'])) {
            $offer->extendedOffer = $params['extendedoffercheckbox'];
            $offer->extendedTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferTitle']);
            $offer->extendedUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferRefurl']);
            $offer->extendedMetaDescription =
                \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferMetadesc']);
            $offer->extendedFullDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponInfo']);
        }

        $offer->exclusiveCode=$offer->editorPicks=0;
        if (isset($params['exclusivecheckbox'])) {
            $offer->exclusiveCode=1;
        }

        if (isset($params['editorpickcheckbox'])) {
            $offer->editorPicks=1;
        }

        $offer->deleted = 0;
        $offer->created_at = new \DateTime('now');
        $offer->updated_at = new \DateTime('now');

        \Zend_Registry::get('emLocale')->persist($offer);
        \Zend_Registry::get('emLocale')->flush();

        foreach ($params['termsAndcondition'] as $terms) {
            if (trim($terms)!='') {
                $offerTerms  = new \KC\Entity\TermAndCondition();
                $offerTerms->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($terms);
                $offerTerms->deleted = 0;
                $offerTerms->termandcondition = $entityManagerUser->find('KC\Entity\Offer', $offer->id);
                $offerTerms->created_at = new \DateTime('now');
                $offerTerms->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($offerTerms);
                \Zend_Registry::get('emLocale')->flush();
            }
        }

        if (isset($params['couponCodeCheckbox'])) {
            if (isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {
                    $offer->refOfferCategory[]->categoryId = $categories;
                    $offerCategories  = new \KC\Entity\RefOfferCategory();
                    $offerCategories->created_at = new \DateTime('now');
                    $offerCategories->updated_at = new \DateTime('now');
                    $offerCategories->offer = \Zend_Registry::get('emLocale')->find('KC\Entity\Category', $categories);
                    $offerCategories->category = \Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $offer->id);
                    \Zend_Registry::get('emLocale')->persist($offerCategories);
                    \Zend_Registry::get('emLocale')->flush();
                }
            }
        }

        if (isset($params['attachedpages'])) {
            foreach ($params['attachedpages'] as $pageId) {
                $offerPage  = new \KC\Entity\RefOfferPage();
                $offerPage->created_at = new \DateTime('now');
                $offerPage->updated_at = new \DateTime('now');
                $offerPage->offers = $entityManagerUser->find('KC\Entity\Page', $pageId);
                $offerPage->refoffers = $entityManagerUser->find('KC\Entity\Offer', $offer->id);
                \Zend_Registry::get('emLocale')->persist($offerPage);
                \Zend_Registry::get('emLocale')->flush();
            }
        }
        $key = '6_topOffers'  . intval($params['selctedshop']) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_latestUpdates'  . intval($params['selctedshop']) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_expiredOffers'  . intval($params['selctedshop']) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
    }

    public function updateOffer($params)
    {
        $offer = \Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $params['id']);
        if (isset($params['yesoffercheckbox'])) {
            $offer->approved = 1;
            $offer->shopOffers = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $params['selctedshop']);
        } else {
            $offer->approved = 0;
        }

        if (isset($params['couponCodeCheckbox'])) {
            $offer->discountType = 'CD';
            $offer->couponCode = $params['couponCode'];
        } else if (isset($params['saleCheckbox'])) {
            $offer->discountType = 'SL';
        } else {
            $offer->discountType = 'PA';

            if (isset($params['uploadoffercheck'])) {
                $offer->refOfferUrl = '';
                echo $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  \BackEnd_Helper_viewHelper::getImageExtension($fileName);
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, $fileName, $matches);
                if (@$matches[1]) {
                    $offerImage  = \Zend_Registry::get('emLocale')->find('KC\Entity\Image', $offer->logo->id);
                    $offerImage->ext = $ext;
                    $offerImage->path = 'images/upload/offer/';
                    $offerImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($fileName);
                    $offerImage->deleted = 0;
                    $offerImage->created_at = $offerImage->created_at;
                    $offerImage->updated_at = new \DateTime('now');
                    \Zend_Registry::get('emLocale')->persist($offerImage);
                    \Zend_Registry::get('emLocale')->flush();
                    $offer->logo = \Zend_Registry::get('emLocale')->find('KC\Entity\Image', $offerImage->id);
                }
            } else {
                $offer->refOfferUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }

        if (isset($params['addofferTitle'])) {
            $offer->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);
        }

        if (isset($params['deepLinkStatus'])) {
            $offer->refURL =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        } else {
            $offer->refURL =  '';
        }

        if (isset($params['description'])) {
            $offer->extendedFullDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['description']);
        }

        $offer->startDate = date('Y-m-d', strtotime($params['offerStartDate']))
        .' '.date(
            'H:i',
            strtotime($params['offerstartTime'])
        );
        $offer->endDate = date('Y-m-d', strtotime($params['offerEndDate']))
        .' '.date(
            'H:i',
            strtotime($params['offerendTime'])
        );

        $offer->deleted = 0;
        $offer->created_at = $offer->created_at;
        $offer->updated_at = new \DateTime('now');

        \Zend_Registry::get('emLocale')->persist($offer);
        \Zend_Registry::get('emLocale')->flush();

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\TermAndCondition', 'tc')
            ->setParameter(1, $offer->id)
            ->where('tc.termandcondition = ?1')
            ->getQuery();
        $query->execute();

        foreach ($params['termsAndcondition'] as $terms) {
            if (trim($terms)!='') {
                $offerTerms  = new \KC\Entity\TermAndCondition();
                $offerTerms->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($terms);
                $offerTerms->deleted = 0;
                $offerTerms->termandcondition = $entityManagerUser->find('KC\Entity\Offer', $offer->id);
                $offerTerms->created_at = new \DateTime('now');
                $offerTerms->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($offerTerms);
                \Zend_Registry::get('emLocale')->flush();
            }
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\RefOfferCategory', 'roc')
            ->setParameter(1, $offer->id)
            ->where('roc.category = ?1')
            ->getQuery();
        $query->execute();

        if (isset($params['couponCodeCheckbox'])) {
            if (isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {
                    $offer->refOfferCategory[]->categoryId = $categories;
                    $offerCategories  = new \KC\Entity\RefOfferCategory();
                    $offerCategories->created_at = new \DateTime('now');
                    $offerCategories->updated_at = new \DateTime('now');
                    $offerCategories->offer = \Zend_Registry::get('emLocale')->find('KC\Entity\Category', $categories);
                    $offerCategories->category = \Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $offer->id);
                    \Zend_Registry::get('emLocale')->persist($offerCategories);
                    \Zend_Registry::get('emLocale')->flush();
                }
            }
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\RefOfferPage', 'rp')
            ->setParameter(1, $offer->id)
            ->where('rp.refoffers = ?1')
            ->getQuery();
        $query->execute();

        if (isset($params['attachedpages'])) {
            foreach ($params['attachedpages'] as $pageId) {
                $offerPage  = new \KC\Entity\RefOfferPage();
                $offerPage->created_at = new \DateTime('now');
                $offerPage->updated_at = new \DateTime('now');
                $offerPage->offers = $entityManagerUser->find('KC\Entity\Page', $pageId);
                $offerPage->refoffers = $entityManagerUser->find('KC\Entity\Offer', $offer->id);
                \Zend_Registry::get('emLocale')->persist($offerPage);
                \Zend_Registry::get('emLocale')->flush();
            }
        }
        $key = '6_topOffers'  . intval($params['selctedshop']) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_latestUpdates'  . intval($params['selctedshop']) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_expiredOffers'  .intval($params['selctedshop']) . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
    }

    public static function exportofferList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offerList = $queryBuilder
            ->select('o,s.name as shopname,s.accountManagerName as acName')
            ->from("KC\Entity\Offer", "o")
            ->leftJoin('o.shopOffers', 's')
            ->where("o.deleted=0")
            ->orderBy("o.id", "DESC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerList;
    }

    public function getOfferDetail($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopDetail = $queryBuilder
            ->select('o,s.name,s.notes,s.accountManagerName,a.name as affname,p.id,tc,cat.id,img')
            ->from("KC\Entity\Offer", "o")
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->leftJoin('o.offers', 'p')
            ->leftJoin('o.offertermandcondition', 'tc')
            ->leftJoin('o.categoryoffres', 'cat')
            ->leftJoin('o.logo', 'img')
            ->andWhere($queryBuilder->expr()->eq('o.id', $offerId))
            ->andWhere($queryBuilder->expr()->eq('o.userGenerated', 1))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopDetail;
    }

    public function uploadFile($imgName)
    {
        $uploadPath = "images/upload/offer/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $imgName;

        if ($img) {
            @unlink($user_path . $img);
            @unlink($user_path . "thum_" . $img);
            @unlink($user_path . "thum_large" . $img);
        }

        if (!file_exists($user_path)) {
            mkdir($user_path);
        }

        $adapter->setDestination(ROOT_PATH . $uploadPath);
        $adapter->addValidator('Extension', false, 'jpg,jpeg,png,gif,pdf');
        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) {
            $ext =  \BackEnd_Helper_viewHelper::getImageExtension($info['name']);
            $name = $adapter->getFileName($file, false);
            $name = $adapter->getFileName($file);
            $orgName = time() . "_" . $info['name'];
            $fname = $user_path . $orgName;

            if ($ext!='pdf') {
                $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;
                \BackEnd_Helper_viewHelper::resizeImage(
                    $_FILES["uploadoffer"],
                    $orgName,
                    126,
                    90,
                    $path
                );

                $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
                \BackEnd_Helper_viewHelper::resizeImage(
                    $_FILES["uploadoffer"],
                    $orgName,
                    132,
                    95,
                    $path
                );
            }
            $adapter->addFilter(
                new \Zend_Filter_File_Rename(
                    array('target' => $fname,
                    'overwrite' => true)
                ),
                null,
                $file
            );

            $adapter->receive($file);
            $status = "";
            $data = "";
            $msg = "";
            if ($adapter->isValid($file) == 1) {
                $data = $orgName;
            }
            return $data;
        }
    }

    public function addOffer($params)
    {
        $entityManager  = \Zend_Registry::get('emLocale');
        $offer = new \KC\Entity\Offer();
        $title = $params['offer_name'];
        $offer->Visability = 'DE';
        $offer->discountType = 'CD';
        $offer->extendedFullDescription = $params['offer_desc'];
        $offer->shopOffers = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $params['shopId']);
        $offer->couponCode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offer_code']);
        $offer->userGenerated = true;
        $offer->authorId = \Auth_VisitorAdapter::getIdentity()->id;
        $offer->authorName = \Auth_VisitorAdapter::getIdentity()->firstName;
        $entityManager->persist($offer);
        $entityManager->flush();
    }
}
