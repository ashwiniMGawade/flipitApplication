<?php
namespace KC\Repository;
class Page extends \Core\Domain\Entity\Page
{
    #####################################################
    ############ REFECTORED CODE ########################
    public static function getPageDetailsInError($permalink)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale->select('page, attr, pagewidget, himg')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->leftJoin('page.pagewidget', 'pagewidget')
            ->leftJoin('page.page', 'attr')
            ->leftJoin('page.pageHeaderImageId', 'himg')
            ->where('page.permalink = ' . $entityManagerLocale->expr()->literal(\FrontEnd_Helper_viewHelper::sanitize($permalink)))
            ->andWhere('page.publishDate <=' . $entityManagerLocale->expr()->literal($currentDate))
            ->andWhere('page.deleted = 0');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getPageDetailsFromUrl($pagePermalink)
    {
        $pageDetails = self::getPageDetailsByPermalink($pagePermalink);
        if (!empty($pageDetails)) {
            return $pageDetails;
        } else {
            throw new \Zend_Controller_Action_Exception('', 404);
        }
    }

    public static function getPageHomeImageByPermalink($permalink)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('homepageimage.path, homepageimage.name')
            ->from('\Core\Domain\Entity\Page', 'p')
            ->leftJoin("p.homepageimage", "homepageimage")
            ->where('p.permalink ='."'".$permalink."'")
            ->andWhere('p.deleted = 0');
        $pageHomeImage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $imagePath = '';
        if (!empty($pageHomeImage[0]['path']) && !empty($pageHomeImage[0]['name'])) {
            $imagePath = PUBLIC_PATH_CDN.$pageHomeImage[0]['path'].$pageHomeImage[0]['name'];
        }
        return $imagePath;
    }

    public static function getPageDetailsByPermalink($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page, img, himg')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->leftJoin('page.logo', 'img')
            ->leftJoin('page.pageHeaderImageId', 'himg')
            ->where('page.permalink ='."'".\FrontEnd_Helper_viewHelper::sanitize($permalink)."'")
            ->andWhere('page.publish = 1')
            ->andWhere('page.pageLock = 0')
            ->andWhere('page.deleted = 0');
        $pageDetails = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getSpecialListPages()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page, img')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->leftJoin('page.logo', 'img')
            ->where('page INSTANCE OF \Core\Domain\Entity\OfferListPage')
            ->andWhere('page.deleted = 0');
        $specialListPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialListPages;
    }
    

    public static function getDefaultPageProperties($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $entityManagerUser->expr()->literal(\FrontEnd_Helper_viewHelper::sanitize($permalink)))
            ->where('page.permalink = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        
        $pageProperties = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageProperties;
    }

    public static function getPageDetailFromPermalink($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.content, page.pageTitle')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $entityManagerUser->expr()->literal(\FrontEnd_Helper_viewHelper::sanitize($permalink)))
            ->where('page.permalink = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetail;
    }

    public static function updatePageAttributeId()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('\Core\Domain\Entity\Page', 'page');
        for ($i = 1; $i <= 3; $i++) {
                $query->set('page.pageattributeid', $i)->getQuery();
            if ($i == 1) {
                $query->setParameter(1, 'info/contact')->where('permalink = ?1')->getQuery();
            } else if ($i == 2) {
                $query->setParameter(1, 'info/faq')->where('permalink = ?1')->getQuery();
            } else if ($i == 3) {
                $query->setParameter(1, 'info/contact')
                ->where('permalink = ?1')
                ->setParameter(2, 'info/faq')
                ->where('permalink = ?2')
                ->getQuery();
            }
            $query->execute();
        }
        return true;
    }

    public static function replaceToPlusPage()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('\Core\Domain\Entity\Page', 'page')
            ->set('page.permalink', 'plus')
            ->setParameter(1, 66)
            ->where('page.id = ?1')
            ->getQuery();
            $query->execute();
        return true;
    }

    public static function addSpecialPagesOffersCount($spcialPageId, $offersCount)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('\Core\Domain\Entity\Page', 'page')
            ->set('page.offersCount', $offersCount)
            ->setParameter(1, $spcialPageId)
            ->where('page.id = ?1')
            ->getQuery();
            $query->execute();
        return true;
    }
    ######################################################
    ############ END REFACTORED CODE #####################
    ######################################################

    public function defaultPagesList()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.id, page.pageTitle as pagetitle')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->where('page INSTANCE OF \Core\Domain\Entity\OfferListPage')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setParameter(3, 1)
            ->andWhere('page.publish = ?3')
            ->orderBy('page.pageTitle', 'ASC');
        $defaultPagesList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $defaultPagesList;
    }

    public function getPagesOffer()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.id, page.pageTitle')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, 1)
            ->where('page.showPage = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setParameter(3, 1)
            ->andWhere('page.publish = ?3')
            ->setParameter(4, 0)
            ->andWhere('page.pageLock = ?4')
            ->orderBy('page.pageTitle', 'ASC');
        $pagesOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pagesOffer;
    }

    public function getPages($params)
    {
        $srhPage = (isset($params["searchText"]) && trim($params["searchText"])!='undefined')
            ? $params["searchText"] : '';
        $entityManagerUser  = \Zend_Registry::get('emUser');
        
        if (\Auth_StaffAdapter::hasIdentity()) {
            $roleId =   \Zend_Auth::getInstance()->getIdentity()->users->id;
        }

        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, 0)
            ->where('page.deleted = ?1');
        if ($srhPage != '') {
            $query->andWhere("page.pageTitle LIKE '$srhPage%'");
        }
        if ($roleId>2) {
            $query->setParameter(3, 0);
            $query->andWhere('page.pageLock = ?3');
        }

        if (trim($params['searchType'])!= 'undefined') {
            if ($params['searchType'] == 'offer') {
                $query->andWhere('page INSTANCE OF \Core\Domain\Entity\OfferListPage');
            } else if ($params['searchType'] == 'default') {
                $query->andWhere('page INSTANCE OF \Core\Domain\Entity\DefaultPage');
            }
        }
        
        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array('page.pageTitle','page.created_at',
                'page.publish','page.contentManagerName'
            )
        );

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(
            \Zend_Registry::get('emLocale'),
            $request
        );
        $builder
            ->setQueryBuilder($query)
            ->add('text', 'page.pageTitle')
            ->add('text', 'page.pageTitle')
            ->add('text', 'page.permalink')
            ->add('number', 'page.created_at')
            ->add('text', 'page.contentManagerName');
        $pageList = $builder->getTable()->getResponseArray();
        return $pageList;
    }

    public function gettrashedPages($params)
    {
        $srhPage = (isset($params["searchText"]) && trim($params["searchText"])!='undefined')
            ? $params["searchText"] : '';

        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $result = $entityManagerUser->select('page.id, page.pageTitle, page.created_at, page.updated_at, page.contentManagerName')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, 1)
            ->where('page.deleted = ?1')
            ->andWhere(
                $entityManagerUser->expr()->like('page.pageTitle', $entityManagerUser->expr()->literal($srhPage.'%'))
            )
            ->getQuery()->execute();
        return $result;
    }

    public function checkFooterpages($tempid)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $tempid)
            ->where('page.pageattributeid = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $footerPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $footerPages;
    }

    public function getPageDetail($pageId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'p, w.title as widgetTtitle, w.id as widgetId, logo, pageHeaderImage, homepageimage, pa'
        )
            ->from('\Core\Domain\Entity\Page', 'p')
            ->leftJoin('p.logo', 'logo')
            ->leftJoin('p.pageHeaderImageId', 'pageHeaderImage')
            ->leftJoin("p.homepageimage", "homepageimage")
            ->leftJoin('p.pagewidget', 'pagewidget')
            ->leftJoin('pagewidget.page', 'w')
            ->leftJoin('p.page', 'pa')
            ->setParameter(1, $pageId)
            ->where('p.id = ?1');
        $pageDetails = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function exportpagelist()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, 0)
            ->where('page.deleted = ?1')
            ->orderBy('page.id','DESC');
        $pageList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageList;
    }

    public static function restorePage($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $updatePage = $queryBuilder->update('\Core\Domain\Entity\Page', 'page')
            ->set('page.deleted', 0)
            ->set('page.updated_at', $queryBuilder->expr()->literal(date('Y-m-d H:i:s')))
            ->setParameter(1, $id)
            ->where('page.id = ?1')
            ->getQuery();
            $updatePage->execute();

            $u = \Zend_Registry::get('emLocale')->find('\Core\Domain\Entity\Page', $id);

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $updateRoutePermalink = $queryBuilder->update('\Core\Domain\Entity\RoutePermalink', 'routePermalink')
            ->set('routePermalink.deleted', 0)
            ->where('routePermalink.permalink = ?1')
            ->setParameter(1, $u->permalink)
            ->getQuery();
            $updateRoutePermalink->execute();
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
        return $id;
    }

    public static function moveToTrash($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('\Core\Domain\Entity\Page', 'page')
            ->set('page.deleted', 1)
            ->set('page.updated_at', $queryBuilder->expr()->literal(date('Y-m-d H:i:s')))
            ->setParameter(1, $id)
            ->where('page.id = ?1')
            ->getQuery();
        $query->execute();

        $u = \Zend_Registry::get('emLocale')->find('\Core\Domain\Entity\Page', $id);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('\Core\Domain\Entity\RoutePermalink', 'routePermalink')
            ->set('routePermalink.deleted', 1)
            ->setParameter(1, $u->permalink)
            ->where('routePermalink.permalink = ?1')
            ->getQuery();
        $query->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
        return 1;
    }

    public static function deletepage($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\RefPageWidget', 'rpw')
                ->setParameter(1, $id)
                ->where('rpw.widget = ?1')
                ->getQuery();
            $query->execute();
           
            $query = $queryBuilder->select('page')
                ->from('\Core\Domain\Entity\Page', 'page')
                ->setParameter(1, $id)
                ->where('page.id = ?1');
            $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $query = $queryBuilder->delete('\Core\Domain\Entity\RoutePermalink', 'routePermalink')
                ->setParameter(1, $pageDetails[0]['permalink'])
                ->where('routePermalink.permalink = ?1')
                ->getQuery();
            $query->execute();

            $query = $queryBuilder->delete('\Core\Domain\Entity\Page', 'page')
                ->setParameter(1, $id)
                ->where('page.id = ?1')
                ->getQuery();
            $query->execute();
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
        return $id;
    }

    public static function searchToFivePage($keyword, $type)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.pageTitle')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $type)
            ->where('page.deleted = ?1')
            ->andWhere("page.pageTitle LIKE '$keyword%'");
        $role =  \Zend_Auth::getInstance()->getIdentity()->users->id;
        if ($role=='4' || $role=='3') {
            $query->setParameter(3, 0)->andWhere('page.pageLock = ?3');
        }
        $query->orderBy("page.pageTitle", "ASC")->setMaxResults(5);
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getDefaultPage($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $id)
            ->where('page.id = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $defaultPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $defaultPage;
    }

    public static function getPageList($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $id)
            ->where('page.pageattributeid = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageList;
    }

    public function deletePageImage($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('\Core\Domain\Entity\Page', 'page')
            ->set('page.logoId', 0)
            ->setParameter(1, $params['pageId'])
            ->where('page.id = ?1')
            ->getQuery();
        $query->execute();
        return 1;
    }

    public static function getPageDetailFromSlug($slug)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.content, page.pageTitle')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $slug)
            ->where('page.slug = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageDetailFromSlug = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetailFromSlug;
    }

    public function getFooterpages()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, 15)
            ->where('page.pageattributeid = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setMaxResults(10);
        $footerPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $footerPages;
    }

    public function getPageAttributes($slug)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.id, attribute')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->leftJoin('page.pageattribute attribute')
            ->setParameter(1, $slug)
            ->where('page.slug = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageAttributes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageAttributes;
    }

    public static function getPageFromPageAttrInOffer($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'page.id, page.permalink, page.pageTitle,
            page.metatitle, page.metadescription'
        )
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $id)
            ->where('page.pageattributeid = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->orderBy('page.id DESC');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getPageFromPageAttributeInOfferPop($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'page.id, page.permalink'
        )
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $id)
            ->where('page.pageattributeid = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->orderBy('page.id DESC');
        $pageDetailsFromPageAttribute = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetailsFromPageAttribute;
    }

    public static function getOfferListPage()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'page.id, page.permalink, page.pageTitle, page.pageType, page.metadescription, logo.path, logo.name'
        )
            ->from('\Core\Domain\Entity\Page', 'page')
            ->leftJoin('page.logo logo')
            ->setParameter(1, 'page INSTANCE OF \Core\Domain\Entity\OfferListPage')
            ->where('page.pageType = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setMaxResults(9);
        $offerListPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerListPage;
    }

    public static function pagesPermalinksList()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'page.id, page.permalink'
        )
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, 1)
            ->where('page.publish = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setParameter(3, 1)
            ->andWhere('page.showsitemap = ?3')
            ->orderBy('page.pageTitle', 'ASC');
        $pageIdsAndPermalinks = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageIdsAndPermalinks;
    }

    public function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH, 0776, true);
        }
        $uploadPath = UPLOAD_IMG_PATH . "page/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $files = $adapter->getFileInfo($file);

        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0776, true);
        }
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        $name = $adapter->getFileName($file, false);
        $newName = time() . "_" . $name;
        $cp = $rootPath . $newName;
        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 132, 95, $path);
        $path = ROOT_PATH . $uploadPath . "thum_page_small" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 60, 40, $path);
        $path = ROOT_PATH . $uploadPath . "thum_page_large_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 150, 100, $path);
        $path = ROOT_PATH . $uploadPath . "thum_extra_large_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 170, 127, $path);

        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage(
                $files[$file],
                $newName,
                200,
                150,
                $path
            );
        }
        $adapter->addFilter(
            new \Zend_Filter_File_Rename(
                array(
                    'target' => $cp,
                    'overwrite' => true
                )
            ),
            null,
            $file
        );
        $adapter->receive($file);

        if ($adapter->isValid($file)) {
            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);
        } else {
            return array("status" => "-1",
                    "msg" => "Please upload the valid file");
        }
    }

    public function savePage($params)
    {
        if (isset($params['selectedpageType'])) {
            $savePage = new \Core\Domain\Entity\OfferListPage();
        } else {
            $savePage = new \Core\Domain\Entity\DefaultPage();
        }
        
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $savePage->maxOffers  = 0;
        $savePage->oderOffers = 0;
        if (isset($params['selectedpageType'])) {
            if (trim($params['maxOffer'])!='') {
                  $savePage->maxOffers = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxOffer']);
            }
            $savePage->oderOffers = 'desc';
            if (isset($params['offersOrderchk'])) {
                $savePage->oderOffers = 'asc';
            }
            if (isset($params['timeCostraintchk'])) {
                $savePage->enableTimeConstraint=1;
                $savePage->timenumberOfDays = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['numberofDays']);
                $savePage->timeType = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['postwithin']);
            }
            if (isset($params['wordCostraintchk'])) {
                $savePage->enableWordConstraint = 1;
                $savePage->wordTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['wordConstraintTxt']);
            }
            if (isset($params['awardCostraintchk'])) {
                $savePage->awardConstratint = 1;
                $savePage->awardType =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['awardConstraintDropdown']);
            }
            if (isset($params['clickCostraintchk'])) {
                $savePage->enableClickConstraint = 1;
                $savePage->numberOfClicks =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['clickConstraintTxt']);
            }
            if (isset($params['coupconCoderegularchk'])) {
                $savePage->couponRegular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCoderegularchk']);
            }
            if (isset($params['coupconCodeeditorchk'])) {
                $savePage->couponEditorPick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeditorchk']);
            }
            if (isset($params['coupconCodeeclusivechk'])) {
                $savePage->couponExclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeclusivechk']);
            }
            if (isset($params['saleregularchk'])) {
                $savePage->saleRegular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleregularchk']);
            }
            if (isset($params['saleeditorchk'])) {
                $savePage->saleEditorPick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeditorchk']);
            }
            if (isset($params['saleeclusivechk'])) {
                $savePage->saleExclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeclusivechk']);
            }
            if (isset($params['printableregularchk'])) {
                $savePage->printableRegular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableregularchk']);
            }
            if (isset($params['printableeditorchk'])) {
                $savePage->printableEditorPick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableeditorchk']);
            }
            if (isset($params['printableexclusivechk'])) {
                $savePage->printableExclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableexclusivechk']);
            }
            $savePage->showPage = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['showPage']);
        }
        $savePage->publish = 1;
        $savePage->timeOrder = 0;
        if ($params['savePagebtn']=='draft') {
            $savePage->publish = 0;
        }
        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {
            $result = self::uploadImage('logoFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $pageImage  = new \Core\Domain\Entity\Logo();
                $pageImage->ext = $ext;
                $pageImage->path = $result['path'];
                $pageImage->name = $result['fileName'];
                $pageImage->deleted = 0;
                $pageImage->created_at = new \DateTime('now');
                $pageImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pageImage);
                $entityManagerLocale->flush();
                $savePage->logo = $entityManagerLocale->find('\Core\Domain\Entity\Logo', $pageImage->getId());
            } else {
                return false;
            }
        }
        if (isset($_FILES['headerFile']['name']) && $_FILES['headerFile']['name'] != '') {
            $result = self::uploadImage('headerFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $pageImage  = new \Core\Domain\Entity\Logo();
                $pageImage->ext = $ext;
                $pageImage->path = $result['path'];
                $pageImage->name = $result['fileName'];
                $pageImage->deleted = 0;
                $pageImage->created_at = new \DateTime('now');
                $pageImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pageImage);
                $entityManagerLocale->flush();
                $savePage->pageHeaderImageId = $entityManagerLocale->find('\Core\Domain\Entity\Logo', $pageImage->getId());
            } else {
                return false;
            }
        }

        if (isset($_FILES['homepageFile']['name']) && $_FILES['homepageFile']['name'] != '') {
            $result = self::uploadImage('homepageFile');
            $savePage->homepageimage = $savePage->homepageimage;
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $homepageimage  = new \Core\Domain\Entity\Logo();
                $homepageimage->ext = $ext;
                $homepageimage->path = $result['path'];
                $homepageimage->name = $result['fileName'];
                $homepageimage->deleted = 0;
                $homepageimage->created_at = new \DateTime('now');
                $homepageimage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($homepageimage);
                $entityManagerLocale->flush();
                $savePage->homepageimage = $entityManagerLocale->find('\Core\Domain\Entity\Logo', $homepageimage->getId());
            } else {
                return false;
            }
        }

        if (isset($params['publishDate']) && $params['publishDate']!='') {
            $publishDate= date('Y-m-d', strtotime($params['publishDate']))
                .' '.date('H:i:s', strtotime($params['publishTimehh']));
            $savePage->publishDate = new \DateTime($publishDate);
        }
        $savePage->pageTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageTitle']);
        $savePage->subtitle = \FrontEnd_Helper_viewHelper::sanitize(
            \BackEnd_Helper_viewHelper::stripSlashesFromString($params['subtitle'])
        );
        $savePage->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagepermalink']);
        $savePage->metaTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaTitle']);
        $savePage->metaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaDesc']);
        $savePage->customHeader = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageCustomHeader']);
        $savePage->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $savePage->created_at = new \DateTime('now');
        $savePage->updated_at = new \DateTime('now');
        $savePage->deleted = 0;
        $savePage->pageLock = 0;
        if (isset($params['lockPageStatuschk'])) {
            $savePage->pageLock = 1;
        }
        
        isset($params['showSitemapStatuscheck']) && $params['showSitemapStatuscheck'] == 1
        ? $savePage->showsitemap = 1 : $savePage->showsitemap = 0;
        isset($params['showMobileMenuStatuscheck']) && $params['showMobileMenuStatuscheck'] == 1
        ? $savePage->showinmobilemenu = 1 : $savePage->showinmobilemenu = 0;

        if (trim($params['pageTemplate'])!='') {
            $savePage->page = $entityManagerLocale->find('\Core\Domain\Entity\PageAttribute', $params['pageTemplate']);
        }
       
        $savePage->contentManagerId = \Auth_StaffAdapter::getIdentity()->id;
        $savePage->contentManagerName = \Auth_StaffAdapter::getIdentity()->firstName
        . " " . \Auth_StaffAdapter::getIdentity()->lastName;
        try {
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            $pagePermalinkParam =
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['pagepermalink']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_'.$pagePermalinkParam.'_data');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$savePage->getId().'_image');
            $key = 'all_widget' . $params['pageTemplate'] . "_list";
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $entityManagerLocale->persist($savePage);
            $entityManagerLocale->flush();

            $selectedWidgets = explode(',', $params['selectedWigetForPage']);
            $i=0;
            foreach ($selectedWidgets as $widget) {
                if (trim($widget)!='') {
                    $pageWidget  = new \Core\Domain\Entity\RefPageWidget();
                    $pageWidget->created_at = new \DateTime('now');
                    $pageWidget->updated_at = new \DateTime('now');
                    $pageWidget->stauts = 1;
                    $pageWidget->position = $i;
                    $pageWidget->page = $entityManagerLocale->find('\Core\Domain\Entity\Widget', $widget);
                    $pageWidget->widget = $entityManagerLocale->find('\Core\Domain\Entity\Page', $savePage->getId());
                    $entityManagerLocale->persist($pageWidget);
                    $entityManagerLocale->flush();
                }
                $i++;
            }

            $pageId =  $savePage->getId();
            $permalink = $pagePermalinkParam;
            if (isset($permalink)) {
                $varnishObj = new \KC\Repository\Varnish();
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink);
            }
            $route = new \Core\Domain\Entity\RoutePermalink();
            $route->permalink = $params['pagepermalink'];
            $route->type = 'PG';
            $route->exactlink = $params['pagepermalink'];
            $route->created_at = new \DateTime('now');
            $route->updated_at = new \DateTime('now');
            $route->deleted = 0;

            switch ($params['pageTemplate']) {
                case 4:
                    $route->exactlink = 'index/index/attachedpage/'.$savePage->getId();
                    break;
                case 5:
                    $route->exactlink = 'offer/popularoffer/attachedpage/'.$savePage->getId();
                    break;
                case 6:
                    $route->exactlink = 'offer/index/attachedpage/'.$savePage->getId();
                    break;
                case 5:
                    $route->exactlink = 'offer/popularoffer';
                    break;
                case 7:
                    $route->exactlink = 'store/index/attachedpage/'.$savePage->getId();
                    break;
                case 8:
                    $route->exactlink = 'store/index/attachedpage/'.$savePage->getId();
                    break;
                case 9:
                    $route->exactlink = 'category/index/attachedpage/'.$savePage->getId();
                    break;
                case 10:
                    $route->exactlink = 'category/index/attachedpage/'.$savePage->getId();
                    break;
                case 13:
                    $route->exactlink = 'plus/index/attachedpage/'.$savePage->getId();
                    break;
                case 14:
                    $route->exactlink = 'about/index/attachedpage/'.$savePage->getId();
                    break;
                case 17:
                    $route->exactlink = 'login/index/attachedpage/'.$savePage->getId();
                    break;
                case 18:
                    $route->exactlink = 'login/forgotpassword/attachedpage/'.$savePage->getId();
                    break;
                case 19:
                    $route->exactlink = 'freesignup/index/attachedpage/'.$savePage->getId();
                    break;
                case 29:
                    $route->exactlink = 'login/memberwelcome/attachedpage/'.$savePage->getId();
                    break;
            }

            $entityManagerLocale->persist($route);
            $entityManagerLocale->flush();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePage($params)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');

        if (isset($params['selectedpageType'])) {
            $repo = $entityManagerLocale->getRepository('\Core\Domain\Entity\OfferListPage');
        } else {
            $repo = $entityManagerLocale->getRepository('\Core\Domain\Entity\DefaultPage');
        }
        $updatePage = $repo->find($params['pageId']);
        $updatePage->slug =  $params['slug'];
        if (isset($params['selectedpageType'])) {
            $updatePage->maxOffers ='';
            if (trim($params['maxOffer'])!='') {
                $updatePage->maxOffers = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxOffer']);
            }
            $updatePage->oderOffers = 0;
            if (isset($params['offersOrderchk'])) {
                $updatePage->oderOffers = $params['offersOrderchk'];
            }
            $updatePage->enableTimeConstraint=0;
            $updatePage->timenumberOfDays = 0;
            $updatePage->timeType = 0;

            if (isset($params['timeCostraintchk'])) {
                $updatePage->enableTimeConstraint=1;
                $updatePage->timenumberOfDays = $params['numberofDays'];
                $updatePage->timeType = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['postwithin']);
            }

            $updatePage->enableWordConstraint=0;
            $updatePage->wordTitle = '';

            if (isset($params['wordCostraintchk'])) {
                $updatePage->enableWordConstraint=1;
                $updatePage->wordTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['wordConstraintTxt']);
            }
            $updatePage->awardConstratint=0;
            $updatePage->awardType = 0;
            if (isset($params['awardCostraintchk'])) {
                $updatePage->awardConstratint=1;
                $updatePage->awardType = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['awardConstraintDropdown']);
            }

            $updatePage->enableClickConstraint=0;
            $updatePage->numberOfClicks = '0';

            if (isset($params['clickCostraintchk'])) {
                $updatePage->enableClickConstraint = 1;
                $updatePage->numberOfClicks =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['clickConstraintTxt']);
            }

            $updatePage->couponRegular = 0;
            if (isset($params['coupconCoderegularchk'])) {
                $updatePage->couponRegular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCoderegularchk']);
            }
            $updatePage->couponEditorPick = 0;
            if (isset($params['coupconCodeeditorchk'])) {
                $updatePage->couponEditorPick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeditorchk']);
            }
            $updatePage->couponExclusive= 0;
            if (isset($params['coupconCodeeclusivechk'])) {
                $updatePage->couponExclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeclusivechk']);
            }
            $updatePage->saleRegular = 0;
            if (isset($params['saleregularchk'])) {
                $updatePage->saleRegular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleregularchk']);
            }
            $updatePage->saleEditorPick = 0;
            if (isset($params['saleeditorchk'])) {
                $updatePage->saleEditorPick = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeditorchk']);
            }
            $updatePage->saleExclusive=0;
            if (isset($params['saleeclusivechk'])) {
                $updatePage->saleExclusive =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeclusivechk']);
            }
            $updatePage->printableRegular = 0;
            if (isset($params['printableregularchk'])) {
                $updatePage->printableRegular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableregularchk']);
            }
            $updatePage->printableEditorPick = 0;
            if (isset($params['printableeditorchk'])) {
                $updatePage->printableEditorPick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableeditorchk']);
            }
            $updatePage->printableExclusive = 0;
            if (isset($params['printableexclusivechk'])) {
                $updatePage->printableExclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableexclusivechk']);
            }
            $updatePage->showPage = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['showPage']);
        }
        $updatePage->publish  = 1;
        if ($params['savePagebtn']=='draft') {
            $updatePage->publish  = 0;
        }
        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {
            $result = self::uploadImage('logoFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $pageImage  = new \Core\Domain\Entity\Logo();
                $pageImage->ext = $ext;
                $pageImage->path = $result['path'];
                $pageImage->name = $result['fileName'];
                $pageImage->deleted = 0;
                $pageImage->created_at = new \DateTime('now');
                $pageImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pageImage);
                $entityManagerLocale->flush();
                $updatePage->logo = $entityManagerLocale->find('\Core\Domain\Entity\Logo', $pageImage->getId());
            } else {
                return false;
            }
        }

        if (isset($_FILES['headerFile']['name']) && $_FILES['headerFile']['name'] != '') {
            $result = self::uploadImage('headerFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $pageImage  = new \Core\Domain\Entity\Logo();
                $pageImage->ext = $ext;
                $pageImage->path = $result['path'];
                $pageImage->name = $result['fileName'];
                $pageImage->deleted = 0;
                $pageImage->created_at = new \DateTime('now');
                $pageImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pageImage);
                $entityManagerLocale->flush();
                $updatePage->pageHeaderImageId =  $entityManagerLocale->find('\Core\Domain\Entity\Logo', $pageImage->getId());
            } else {
                return false;
            }
        }

        if (isset($_FILES['homepageFile']['name']) && $_FILES['homepageFile']['name'] != '') {
            $result = self::uploadImage('homepageFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $homepageimage  = new \Core\Domain\Entity\Logo();
                $homepageimage->ext = $ext;
                $homepageimage->path = $result['path'];
                $homepageimage->name = $result['fileName'];
                $homepageimage->deleted = 0;
                $homepageimage->created_at = new \DateTime('now');
                $homepageimage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($homepageimage);
                $entityManagerLocale->flush();
                $updatePage->homepageimage = $entityManagerLocale->find('\Core\Domain\Entity\Logo', $homepageimage->getId());
            } else {
                return false;
            }
        }

        if (isset($params['publishDate']) && $params['publishDate']!='') {
            $publishDate = date('Y-m-d', strtotime($params['publishDate']))
            .' '.date(
                'H:i:s',
                strtotime($params['publishTimehh'])
            );
            $updatePage->publishDate = new \DateTime($publishDate);
        }
        $updatePage->pageTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageTitle']);
        $updatePage->subtitle = \FrontEnd_Helper_viewHelper::sanitize(
            \BackEnd_Helper_viewHelper::stripSlashesFromString($params['subtitle'])
        );
        $updatePage->permaLink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagepermalink']);
        $updatePage->metaTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaTitle']);
        $updatePage->metaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaDesc']);
        $updatePage->customHeader = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageCustomHeader']);
        $updatePage->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $updatePage->pageLock = 0;
        if (isset($params['lockPageStatuschk'])) {
            $updatePage->pageLock = 1;
        }
        isset($params['showSitemapStatuscheck']) && $params['showSitemapStatuscheck'] == 1
            ? $updatePage->showsitemap = 1 : $updatePage->showsitemap = 0;

        if (trim($params['pageTemplate'])!='') {
            $updatePage->page = $entityManagerLocale->find('\Core\Domain\Entity\PageAttribute', $params['pageTemplate']);
        } else {
            $updatePage->page = NULL;
        }
        if (isset($params['pageAuthor']) && $params['pageAuthor']!='') {
            $updatePage->contentManagerId = $params['pageAuthor'];
            $updatePage->contentManagerName = $params['selectedpageAuthorName'];
        }
        
        $pageid = $params['pageId'];
        \KC\Repository\MoneySaving::delartCategories($params['pageId']);

        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('\Core\Domain\Entity\Page', 'page')
            ->setParameter(1, $pageid)
            ->where('page.id = ?1');
        $getPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($getPage[0]['permaLink'])) {
            $query = $entityManagerUser->select('page')
                ->from('\Core\Domain\Entity\RoutePermalink', 'routePermalink')
                ->setParameter(1, $getPage[0]['permaLink'])
                ->where('routePermalink.permalink = ?1')
                ->setParameter(2, 'PG')
                ->andWhere('routePermalink.type = ?2');
            $getRouteLink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $updateRouteLink = new \KC\Repository\RoutePermalink();
        }
        try {
            $slug = $params['pageTemplate'];
            $pagedatakey ="all_". "pagedata".$slug ."_list";
            $flag =  \FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pagedatakey);
            if (!$flag) {
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($pagedatakey);
            }
            $pageKey ="all_moneysavingpage". $pageid."_list";
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($pageKey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$params['pageId'].'_image');
            $pagePermalinkParam =
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['pagepermalink']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_'.$pagePermalinkParam.'_data');
            $key = 'all_widget' . $params['pageTemplate'] . "_list";
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            if (isset($params['selectedpageType'])) {
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage' . $params['pageId'] . '_offers');
            }

            $entityManagerLocale->persist($updatePage);
            $entityManagerLocale->flush();

            $pageId =  $params['pageId'];
            $permalink = $params['pagepermalink'];

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\RefPageWidget', 'rpw')
            ->setParameter(1, $pageId)
            ->where('rpw.widget = ?1')
            ->getQuery();
            $query->execute();

            $selectedWidgets = explode(',', $params['selectedWigetForPage']);
            $i=0;
            foreach ($selectedWidgets as $widget) {
                if (trim($widget)!='') {
                    $pageWidget  = new \Core\Domain\Entity\RefPageWidget();
                    $pageWidget->created_at = new \DateTime('now');
                    $pageWidget->updated_at = new \DateTime('now');
                    $pageWidget->stauts = 1;
                    $pageWidget->position = $i;
                    $pageWidget->page = $entityManagerLocale->find('\Core\Domain\Entity\Widget', $widget);
                    $pageWidget->widget = $entityManagerLocale->find('\Core\Domain\Entity\Page', $pageId);
                    $entityManagerLocale->persist($pageWidget);
                    $entityManagerLocale->flush();
                }
                $i++;
            }
            
            #update varnish for this page
            if (isset($permalink)) {
                $varnishObj = new Varnish();
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink);
                if (!$permalink=='plus') {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink .'/2');
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink.'/3');
                }
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_categorieen'));
            }
            if (!empty($getRouteLink)) {

                $query = $entityManagerUser->update('\Core\Domain\Entity\RoutePermalink', 'routePermalink')
                    ->set('routePermalink.permalink', "'".$params['pagepermalink']."'")
                    ->set('routePermalink.type', 'PG')
                    ->set('routePermalink.exactlink', "'".$params['pagepermalink']."'");
                switch ($params['pageTemplate']) {
                    case 4:
                        $exactLink = 'index/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 5:
                        $exactLink = 'offer/popularoffer/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 6:
                        $exactLink = 'offer/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 7:
                        $exactLink = 'store/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 8:
                        $exactLink = 'store/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 9:
                        $exactLink = 'category/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 10:
                        $exactLink = 'category/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 13:

                        $exactLink = 'plus/index/attachedpage/'.$params['pageId'];
                        $query->set('permalink', "'".$params['pagepermalink']."'");
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 14:
                        $exactLink = 'about/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 17:
                        $exactLink = 'login/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 18:
                        $exactLink = 'login/forgotpassword/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 19:
                        $exactLink = 'freesignup/index/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 29:
                        $exactLink = 'login/memberwelcome/attachedpage/'.$params['pageId'];
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                }
                $query->setParameter(1, 'PG')
                    ->where('routePermalink.type = ?1')
                    ->setParameter(2, $getPage[0]['permaLink'])
                    ->andWhere('routePermalink.permalink = ?2')
                    ->getQuery();
                $query->execute();
            }
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}
