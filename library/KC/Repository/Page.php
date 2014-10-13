<?php
namespace KC\Repository;
class Page Extends \KC\Entity\Page
{
    #####################################################
    ############ REFECTORED CODE ########################
    public static function getPageDetailsInError($permalink)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.pagewidget', 'pagewidget')
            ->setParameter(1, $permalink)
            ->where('page.permalink = ?1')
            ->setParameter(2, $currentDate)
            ->andWhere('page.publishdate <= ?2')
            ->setParameter(3, 0)
            ->andWhere('page.deleted = ?3');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getPageDetailsFromUrl($pagePermalink)
    {
        $pageDetails = self::getPageDetailsByPermalink($pagePermalink);
        if (!empty($pageDetails)) {
            return $pageDetails;
        } else {
            throw new Zend_Controller_Action_Exception('', 404);
        }
    }

    public static function getPageDetailsByPermalink($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page, img.id, img.path, img.name')
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.logo', 'img')
            ->setParameter(1, $entityManagerUser->expr()->literal($permalink))
            ->where('page.permalink = ?1')
            ->setParameter(2, 1)
            ->andWhere('page.publish = ?2')
            ->setParameter(3, 0)
            ->andWhere('page.pagelock = ?3')
            ->setParameter(4, 0)
            ->andWhere('page.pagelock = ?4');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getSpecialListPages()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page, img')
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.logo', 'img')
            ->setParameter(1, 'offer')
            ->where('page.pagetype = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $specialListPages = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialListPages;
    }
    

    public static function getDefaultPageProperties($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, $entityManagerUser->expr()->literal($permalink))
            ->where('page.permalink = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        
        $pageProperties = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageProperties;
    }

    public static function getPageDetailFromPermalink($permalink)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.content, page.pagetitle')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, $entityManagerUser->expr()->literal($permalink))
            ->where('page.permalink = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2');
        $pageDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetail;
    }

    public static function updatePageAttributeId()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Page', 'page');
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
        $query = $queryBuilder->update('KC\Entity\Page', 'page')
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
        $query = $queryBuilder->update('KC\Entity\Page', 'page')
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
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, 'default')
            ->where('page.pageType = ?1')
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
        $query = $entityManagerUser->select('page.id, page.pagetitle')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, 1)
            ->where('page.showpage = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setParameter(3, 1)
            ->andWhere('page.publish = ?3')
            ->setParameter(4, 0)
            ->andWhere('page.pagelock = ?4')
            ->orderBy('page.pagetitle ASC');
        $pagesOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pagesOffer;
    }

    public function getPages($params)
    {
        $srhPage = (isset($params["searchText"]) && trim($params["searchText"])!='undefined')
            ? $params["searchText"] : '';
        $conn2 = BackEnd_Helper_viewHelper::addConnection();
        if (Auth_StaffAdapter::hasIdentity()) {
            $roleId =   Zend_Auth::getInstance()->getIdentity()->roleId;
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'page.pagetype, page.pagetitle, page.pagelock, page.created_at,
            page.publish, page.contentManagerName'
        )
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, 0)
            ->where('page.deleted = ?1')
            ->setParameter(2, $srhPage.'%')
            ->andWhere($entityManagerUser->expr()->like('page.pagetitle , ?2'));
        if ($roleId>2) {
            $query->setParameter(3, 0);
            $query->andWhere('page.pagelock = ?3');
        }
        if (trim($params["searchType"])!='undefined') {
            $query->setParameter(4, $params['searchType']);
            $query->andWhere('page.pagetype = ?4');
        }
        $result = DataTable_Helper::generateDataTableResponse(
            $query,
            $params,
            array("__identifier" => 'page.pagetitle','page.pagetitle','page.pagetype','page.pagelock','page.created_at',
                'page.publish','page.contentManagerName'),
            array(),
            array()
        );
        return $result;
    }

    public function gettrashedPages($params)
    {
        $srhPage = (isset($params["searchText"]) && trim($params["searchText"])!='undefined')
            ? $params["searchText"] : '';

        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.pagetitle, page.created_at, page.updated_at, page.contentManagerName')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, 1)
            ->where('page.deleted = ?1')
            ->setParameter(2, $srhPage.'%')
            ->andWhere($entityManagerUser->expr()->like('page.pagetitle , ?2'));
        $result =  DataTable_Helper::generateDataTableResponse(
            $pageList,
            $params,
            array("__identifier" => 'page.pagetitle','page.created_at','page.updated_at','page.contentManagerName'),
            array(),
            array()
        );
        return $result;
    }

    public function checkFooterpages($tempid)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
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
            'page, pagewigdet, logo, pageheaderimage,
            artcatg.pageid,artcatg.categoryid'
        )
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.logo', 'logo')
            ->leftJoin('page.pageheaderimage', 'pageheaderimage')
            ->leftJoin('page.pagewidget', 'pagewidget')
            ->leftJoin("page.moneysaving artcatg")
            ->setParameter(1, $pageId)
            ->where('page.id = ?1');
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function exportpagelist()
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, 0)
            ->where('page.deleted = ?1')
            ->orderBy('page.id DESC');
        $pageList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageList;
    }

    public static function restorePage($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->update('KC\Entity\Page', 'page')
            ->set('page.deleted', 0)
            ->setParameter(1, $id)
            ->where('page.id = ?1')
            ->getQuery();
            $query->execute();
            $u = $queryBuilder->find('KC\Entity\Page', $id);
            $query = $queryBuilder->update('KC\Entity\RoutePermalink', 'routePermalink')
            ->set('routePermalink.deleted', 0)
            ->setParameter(1, $u->permalink)
            ->where('routePermalink.permalink = ?1')
            ->getQuery();
            $query->execute();
        } else {
            $id = null;
        }
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
        return $id;
    }

    public static function deletepage($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Page', 'page')
            ->set('page.deleted', 2)
            ->setParameter(1, $id)
            ->where('page.id = ?1')
            ->getQuery();
        $query->execute();
        $u = $queryBuilder->find('KC\Entity\Page', $id);
        $query = $queryBuilder->delete('KC\Entity\RoutePermalink', 'routePermalink')
            ->setParameter(1, $u->permalink)
            ->where('routePermalink.permalink = ?1')
            ->getQuery();
        $query->execute();
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
        return 1;
    }

    public static function moveToTrash($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('page')
                ->from('KC\Entity\Page', 'page')
                ->setParameter(1, $id)
                ->where('page.id = ?1');
            $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $query = $queryBuilder->delete('KC\Entity\Page', 'page')
                ->setParameter(1, $id)
                ->where('page.id = ?1')
                ->getQuery();
            $query->execute();

            $query = $queryBuilder->select('routePermalink')
                ->from('KC\Entity\RoutePermalink', 'routePermalink')
                ->setParameter(1, $pageDetails->permalink)
                ->where('routePermalink.permalink = ?1');
            $routePermalinkDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!empty($routePermalinkDetails)) {
                $query = $queryBuilder->delete('KC\Entity\RoutePermalink', 'routePermalink')
                    ->setParameter(1, $pageDetails->permalink)
                    ->where('routePermalink.permalink = ?1')
                    ->getQuery();
                $query->execute();
            }
        } else {
            $id = null;
        }
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
        return $id;
    }

    public static function searchToFivePage($keyword, $type)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.pagetitle')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, $type)
            ->where('page.deleted = ?1')
            ->setParameter(2, $keyword.'%')
            ->andWhere($entityManagerUser->expr()->like('page.pagetitle , ?2'));
        $role =  Zend_Auth::getInstance()->getIdentity()->roleId;
        if ($role=='4' || $role=='3') {
            $query->setParameter(3, 0)->andWhere('page.pagelock = ?3');
        }
        $query->orderBy("page.pagetitle","ASC")->setMaxResults(5);
        $pageDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageDetails;
    }

    public static function getDefaultPage($id)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
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
            ->from('KC\Entity\Page', 'page')
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
        $query = $queryBuilder->update('KC\Entity\Page', 'page')
            ->set('page.logoid', 0)
            ->setParameter(1, $params['pageId'])
            ->where('page.id = ?1')
            ->getQuery();
        $query->execute();
        return 1;
    }

    public static function getPageDetailFromSlug($slug)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page.content, page.pagetitle')
            ->from('KC\Entity\Page', 'page')
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
            ->from('KC\Entity\Page', 'page')
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
            ->from('KC\Entity\Page', 'page')
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
            'page.id, page.permalink, page.pagetitle,
            page.metatitle, page.metadescription'
        )
            ->from('KC\Entity\Page', 'page')
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
            ->from('KC\Entity\Page', 'page')
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
            'page.id, page.permalink, page.pagetitle, page.pagetype, page.metadescription, logo.path, logo.name'
        )
            ->from('KC\Entity\Page', 'page')
            ->leftJoin('page.logo logo')
            ->setParameter(1, 'offer')
            ->where('page.pagetype = ?1')
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
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, 1)
            ->where('page.publish = ?1')
            ->setParameter(2, 0)
            ->andWhere('page.deleted = ?2')
            ->setParameter(3, 1)
            ->andWhere('page.showsitemap = ?3')
            ->orderBy('page.pagetitle ASC');
        $pageIdsAndPermalinks = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $pageIdsAndPermalinks;
    }

    public function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir(UPLOAD_IMG_PATH, 0776, true);
        }
        $uploadPath = UPLOAD_IMG_PATH . "page/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
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
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 132, 95, $path);
        $path = ROOT_PATH . $uploadPath . "thum_page_small" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 60, 40, $path);
        $path = ROOT_PATH . $uploadPath . "thum_page_large_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 150, 100, $path);
        $path = ROOT_PATH . $uploadPath . "thum_extra_large_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 170, 127, $path);

        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage(
                $files[$file],
                $newName,
                200,
                150,
                $path
            );
        }
        $adapter->addFilter(
            new Zend_Filter_File_Rename(
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
        $this->pagetype ='default';
        $this->maxOffers  = 0;
        $this->oderOffers = 0;
        if (isset($params['selectedpageType'])) {
              $this->pagetype ='offer';
            if (trim($params['maxOffer'])!='') {
                  $this->maxOffers = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxOffer']);
            }
            $this->oderOffers = 'desc';
            if (isset($params['offersOrderchk'])) {
                $this->oderOffers = 'asc';
            }
            if (isset($params['timeCostraintchk'])) {
                $this->enabletimeconstraint=1;
                $this->timenumberofdays = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['numberofDays']);
                $this->timetype = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['postwithin']);
            }
            if (isset($params['wordCostraintchk'])) {
                $this->enablewordconstraint = 1;
                $this->wordtitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['wordConstraintTxt']);
            }
            if (isset($params['awardCostraintchk'])) {
                $this->awardconstratint = 1;
                $this->awardtype =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['awardConstraintDropdown']);
            }
            if (isset($params['clickCostraintchk'])) {
                $this->enableclickconstraint = 1;
                $this->numberofclicks =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['clickConstraintTxt']);
            }
            if (isset($params['coupconCoderegularchk'])) {
                $this->couponregular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCoderegularchk']);
            }
            if (isset($params['coupconCodeeditorchk'])) {
                $this->couponeditorpick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeditorchk']);
            }
            if (isset($params['coupconCodeeclusivechk'])) {
                $this->couponexclusive =
                    BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeclusivechk']);
            }
            if (isset($params['saleregularchk'])) {
                $this->saleregular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleregularchk']);
            }
            if (isset($params['saleeditorchk'])) {
                $this->saleeditorpick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeditorchk']);
            }
            if (isset($params['saleeclusivechk'])) {
                $this->saleexclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeclusivechk']);
            }
            if (isset($params['printableregularchk'])) {
                $this->printableregular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableregularchk']);
            }
            if (isset($params['printableeditorchk'])) {
                $this->printableeditorpick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableeditorchk']);
            }
            if (isset($params['printableexclusivechk'])) {
                $this->printableexclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableexclusivechk']);
            }
            $this->showpage = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['showPage']);
        }
        $this->publish = 1;
        $this->timeorder = 0;
        if ($params['savePagebtn']=='draft') {
            $this->publish = 0;
        }
        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {
            $result = self::uploadImage('logoFile');
            $this->logoid = 0;
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $this->logo->ext = $ext;
                $this->logo->path = $result['path'];
                $this->logo->name = $result['fileName'];
            } else {
                return false;
            }
        }
        if (isset($_FILES['headerFile']['name']) && $_FILES['headerFile']['name'] != '') {
            $result = self::uploadImage('headerFile');
            $this->pageheaderimageid = 0;
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $this->pageheaderimage->ext = $ext;
                $this->pageheaderimage->path = $result['path'];
                $this->pageheaderimage->name = $result['fileName'];
            } else {
                return false;
            }
        }
        if (isset($params['publishDate']) && $params['publishDate']!='') {
            $this->publishdate = date('Y-m-d', strtotime($params['publishDate']))
                .' '.date('H:i:s', strtotime($params['publishTimehh']));
        }
        $this->pagetitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageTitle']);
        $this->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagepermalink']);
        $this->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaTitle']);
        $this->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaDesc']);
        $this->customheader = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageCustomHeader']);
        $this->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $this->created_at = new \DateTime('now');
        $this->updated_at = new \DateTime('now');
        $this->deleted = 0;
        $this->pagelock = 0;
        if (isset($params['lockPageStatuschk'])) {
            $this->pagelock = 1;
        }
        isset($params['showSitemapStatuscheck']) ? $this->showsitemap = 1 : $this->showsitemap = 0;
        if (trim($params['pageTemplate'])!='') {
            $this->pageattributeid = $params['pageTemplate'];
        }
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();
        $this->contentManagerId = Auth_StaffAdapter::getIdentity()->id;
        $this->contentManagerName = Auth_StaffAdapter::getIdentity()->firstName
        . " " . Auth_StaffAdapter::getIdentity()->lastName;
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $selectedWidgets = explode(',', $params['selectedWigetForPage']);
        foreach ($selectedWidgets as $widget) {
            if (trim($widget)!='') {
                $this->pagewidget[]->widgetId = $widget;
            }
        }
        if ($params['pageTemplate'] == 13) {
            for ($a=0; $a<count($params['artcatgs']); $a++) {
                $this->moneysaving[]->categoryid = $params['artcatgs'][$a];
            }
        }
        try {
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            $pagePermalinkParam =
                FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['pagepermalink']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_'.$pagePermalinkParam.'_data');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$this->id.'_image');
            $key = 'all_widget' . $params['pageTemplate'] . "_list";
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($this);
            $pageId =  $this->id;
            $permalink = $this->permalink;
            $entityManagerLocale->flush();
            if (isset($permalink)) {
                $varnishObj = new Varnish();
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink);
            }
            $route = new KC\Repository\RoutePermalink();
            $route->permalink = $params['pagepermalink'];
            $route->type = 'PG';
            $route->exactlink = $params['pagepermalink'];

            switch ($params['pageTemplate']) {
                case 4:
                    $route->exactlink = 'index/index/attachedpage/'.$this->id;
                    break;
                case 5:
                    $route->exactlink = 'offer/popularoffer/attachedpage/'.$this->id;
                    break;
                case 6:
                    $route->exactlink = 'offer/index/attachedpage/'.$this->id;
                    break;
                case 5:
                    $route->exactlink = 'offer/popularoffer';
                    break;
                case 7:
                    $route->exactlink = 'store/index/attachedpage/'.$this->id;
                    break;
                case 8:
                    $route->exactlink = 'store/index/attachedpage/'.$this->id;
                    break;
                case 9:
                    $route->exactlink = 'category/index/attachedpage/'.$this->id;
                    break;
                case 10:
                    $route->exactlink = 'category/index/attachedpage/'.$this->id;
                    break;
                case 13:
                    $route->exactlink = 'plus/index/attachedpage/'.$this->id;
                    break;
                case 14:
                    $route->exactlink = 'about/index/attachedpage/'.$this->id;
                    break;
                case 17:
                    $route->exactlink = 'login/index/attachedpage/'.$this->id;
                    break;
                case 18:
                    $route->exactlink = 'login/forgotpassword/attachedpage/'.$this->id;
                    break;
                case 19:
                    $route->exactlink = 'freesignup/index/attachedpage/'.$this->id;
                    break;
                case 29:
                    $route->exactlink = 'login/memberwelcome/attachedpage/'.$this->id;
                    break;
            }
            $entityManagerLocale->persist($route);
            $entityManagerLocale->flush();
            $i=0;
            foreach ($selectedWidgets as $widget) {

                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->update('KC\Entity\refPageWidget', 'refPageWidget')
                    ->set('refPageWidget.stauts', 1)
                    ->set('refPageWidget.position', $i)
                    ->setParameter(1, $pageId)
                    ->where('refPageWidget.pageId = ?1')
                    ->setParameter(2, $widget)
                    ->where('refPageWidget.widgetId = ?2')
                    ->getQuery();
                $query->execute();
                $i++;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePage($params)
    {
        $this->slug =  $params['slug'];
        $this->pagetype='default';
        if (isset($params['selectedpageType'])) {
            $this->pagetype='offer';
            $this->maxOffers ='';
            if (trim($params['maxOffer'])!='') {
                $this->maxOffers = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxOffer']);
            }
            $this->oderOffers = 0;
            if (isset($params['offersOrderchk'])) {
                $this->oderOffers = $params['offersOrderchk'];
            }
            $this->enabletimeconstraint=0;
            $this->timenumberofdays = 0;
            $this->timetype = 0;

            if (isset($params['timeCostraintchk'])) {
                $this->enabletimeconstraint=1;
                $this->timenumberofdays = $params['numberofDays'];
                $this->timetype = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['postwithin']);
            }

            $this->enablewordconstraint=0;
            $this->wordtitle = '';

            if (isset($params['wordCostraintchk'])) {
                $this->enablewordconstraint=1;
                $this->wordtitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['wordConstraintTxt']);
            }
            $this->awardconstratint=0;
            $this->awardtype = 0;
            if (isset($params['awardCostraintchk'])) {
                $this->awardconstratint=1;
                $this->awardtype = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['awardConstraintDropdown']);
            }

            $this->enableclickconstraint=0;
            $this->numberofclicks = '';

            if (isset($params['clickCostraintchk'])) {
                $this->enableclickconstraint = 1;
                $this->numberofclicks =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['clickConstraintTxt']);
            }

            $this->couponregular = 0;
            if (isset($params['coupconCoderegularchk'])) {
                $this->couponregular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCoderegularchk']);
            }
            $this->couponeditorpick = 0;
            if (isset($params['coupconCodeeditorchk'])) {
                $this->couponeditorpick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeditorchk']);
            }
            $this->couponexclusive= 0;
            if (isset($params['coupconCodeeclusivechk'])) {
                $this->couponexclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeclusivechk']);
            }
            $this->saleregular = 0;
            if (isset($params['saleregularchk'])) {
                $this->saleregular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleregularchk']);
            }
            $this->saleeditorpick = 0;
            if (isset($params['saleeditorchk'])) {
                $this->saleeditorpick = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeditorchk']);
            }
            $this->saleexclusive=0;
            if (isset($params['saleeclusivechk'])) {
                $this->saleexclusive =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeclusivechk']);
            }
            $this->printableregular = 0;
            if (isset($params['printableregularchk'])) {
                $this->printableregular =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableregularchk']);
            }
            $this->printableeditorpick = 0;
            if (isset($params['printableeditorchk'])) {
                $this->printableeditorpick =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableeditorchk']);
            }
            $this->printableexclusive = 0;
            if (isset($params['printableexclusivechk'])) {
                $this->printableexclusive =
                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableexclusivechk']);
            }
            $this->showPage = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['showPage']);
        }
        $this->publish  = 1;
        if ($params['savePagebtn']=='draft') {
            $this->publish  = 0;
        }
        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {
            $result = self::uploadImage('logoFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $this->logo->ext = $ext;
                $this->logo->path = $result['path'];
                $this->logo->name = $result['fileName'];
            } else {
                return false;
            }
        }

        if (isset($_FILES['headerFile']['name']) && $_FILES['headerFile']['name'] != '') {
            $result = self::uploadImage('headerFile');
            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $this->pageheaderimage->ext = $ext;
                $this->pageheaderimage->path = $result['path'];
                $this->pageheaderimage->name = $result['fileName'];
            } else {
                return false;
            }
        }

        if (isset($params['publishDate']) && $params['publishDate']!='') {
            $this->publishDate = date('Y-m-d', strtotime($params['publishDate']))
            .' '.date(
                'H:i:s',
                strtotime($params['publishTimehh'])
            );
        }
        $this->pagetitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageTitle']);
        $this->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagepermalink']);
        $this->metatitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaTitle']);
        $this->metadescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaDesc']);
        $this->customheader = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageCustomHeader']);
        $this->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $this->pagelock = 0;
        if (isset($params['lockPageStatuschk'])) {
            $this->pagelock = 1;
        }
        isset($params['showSitemapStatuscheck']) && $params['showSitemapStatuscheck'] == 1
            ? $this->showsitemap = 1 : $this->showsitemap = 0;

        if (trim($params['pageTemplate'])!='') {
            $this->pageattributeid = $params['pageTemplate'];
        } else {
            $this->pageattributeid = NULL;
        }
        if (isset($params['pageAuthor']) && $params['pageAuthor']!='') {
            $this->contentManagerId = $params['pageAuthor'];
            $this->contentManagerName = $params['selectedpageAuthorName'];
        }
        $selectedWidgets = explode(',', $params['selectedWigetForPage']);
        $this->pageWidget->delete();
        foreach ($selectedWidgets as $widget) {
            if (trim($widget)!='') {
                $this->pageWidget[]->widgetId = $widget;
            }
        }
        $pageid = $params['pageId'];
        KC\Repository\MoneySaving::delartCategories($params['pageId']);
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('page')
            ->from('KC\Entity\Page', 'page')
            ->setParameter(1, $this->id)
            ->where('page.id = ?1');
        $getPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if ($params['pageTemplate'] == 13) {
            for ($a=0; $a<count($params['artcatgs']); $a++) {
                    $monwysavingobj = new MoneySaving();
                    $monwysavingobj->pageid = $params['pageId'];
                    $monwysavingobj->categoryid = $params['artcatgs'][$a];
                    $monwysavingobj->save();
            }
        }
        if (!empty($getPage[0]['permaLink'])) {
            $query = $entityManagerUser->select('page')
                ->from('KC\Entity\RoutePermalink', 'routePermalink')
                ->setParameter(1, $getPage[0]['permaLink'])
                ->where('routePermalink.permalink = ?1')
                ->setParameter(2, 'PG')
                ->andWhere('routePermalink.type = ?2');
            $getRouteLink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $updateRouteLink = new KC\Repository\RoutePermalink();
        }
        try {
            $slug = $this->pageattributeid;
            $pagedatakey ="all_". "pagedata".$slug ."_list";
            $flag =  \FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pagedatakey);
            if (!$flag) {
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($pagedatakey);
            }
            $pageKey ="all_moneysavingpage".$this->id."_list";
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($pageKey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$this->id.'_image');
            $pagePermalinkParam =
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['pagepermalink']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_'.$pagePermalinkParam.'_data');
            $key = 'all_widget' . $params['pageTemplate'] . "_list";
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($this);
            $pageId =  $this->id;
            $permalink = $this->permalink;
            $entityManagerLocale->flush();
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

                $query = $entityManagerUser->update('KC\Entity\RoutePermalink', 'routePermalink')
                    ->set('routePermalink.permalink', "'".$params['pagepermalink']."'")
                    ->set('routePermalink.type', 'PG')
                    ->set('routePermalink.exactlink', "'".$params['pagepermalink']."'");
                switch ($params['pageTemplate']) {
                    case 4:
                        $exactLink = 'index/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 5:
                        $exactLink = 'offer/popularoffer/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 6:
                        $exactLink = 'offer/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 7:
                        $exactLink = 'store/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 8:
                        $exactLink = 'store/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 9:
                        $exactLink = 'category/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 10:
                        $exactLink = 'category/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 13:

                        $exactLink = 'plus/index/attachedpage/'.$this->id;
                        $query->set('permalink', "'".$params['pagepermalink']."'");
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 14:
                        $exactLink = 'about/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 17:
                        $exactLink = 'login/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 18:
                        $exactLink = 'login/forgotpassword/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 19:
                        $exactLink = 'freesignup/index/attachedpage/'.$this->id;
                        $query->set('exactlink', "'".$exactLink."'");
                        break;
                    case 29:
                        $exactLink = 'login/memberwelcome/attachedpage/'.$this->id;
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
            $i=0;
            foreach ($selectedWidgets as $widget) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->update('KC\Entity\refPageWidget', 'refPageWidget')
                    ->set('refPageWidget.stauts', 1)
                    ->set('refPageWidget.position', $i)
                    ->setParameter(1, $pageId)
                    ->where('refPageWidget.pageId = ?1')
                    ->setParameter(2, $widget)
                    ->where('refPageWidget.widgetId = ?2')
                    ->getQuery();
                $query->execute();
                $i++;
            }
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}
