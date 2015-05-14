<?php
class Page extends BasePage
{
    #####################################################
    ############ REFECTORED CODE ########################
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
        $pageDetails = Doctrine_Query::create()
            ->select('p.*, img.id, img.path, img.name')
            ->from('Page p')
            ->leftJoin('p.logo img')
            ->where("p.permaLink='".FrontEnd_Helper_viewHelper::sanitize($permalink)."'")
            ->andWhere('p.publish=1')
            ->andWhere('p.pagelock=0')
            ->andWhere('p.deleted=0')
            ->fetchOne();
        return $pageDetails;
    }

    public static function getSpecialListPages()
    {
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $specialListPages = Doctrine_Query::create()
            ->select('p.*,i.*')
            ->from('Page p')
            ->leftJoin('p.logo i')
            ->where("pageType = ?", 'offer')
            ->andWhere('p.deleted=0')
            ->fetchArray();
        return $specialListPages;
    }
    public static function getPageDetailsInError($page)
    {
        $currentDate = date('Y-m-d H:i:s');
        $pageDetails = Doctrine_Query::create()->from('Page p')
         ->where("p.permaLink='".FrontEnd_Helper_viewHelper::sanitize($page)."'")
        ->leftJoin("p.widget w")
        ->andWhere("p.publishDate <='".$currentDate."'")
        ->andWhere('p.deleted=0')->fetchOne();
        return $pageDetails;

    }

    public static function getDefaultPageProperties($permalink)
    {
        $pageProperties = Doctrine_Query::create()
            ->select('p.*')
            ->from('Page p')
            ->where("permaLink = '". FrontEnd_Helper_viewHelper::sanitize($permalink) ."'")
            ->andWhere('p.deleted=0')
            ->fetchArray();
        return $pageProperties;
    }

    public static function getPageDetailFromPermalink($permalink)
    {
        $pageDetail = Doctrine_Query::create()
        ->select('p.content, p.pagetitle')
        ->from('Page p')
        ->where('p.permalink="'.FrontEnd_Helper_viewHelper::sanitize($permalink).'"')
        ->andWhere('p.deleted=0')
        ->fetchArray();
        return $pageDetail;
    }

    public static function updatePageAttributeId()
    {
        for ($i = 1; $i <= 3; $i++) {
            $updatePagesAttributeId = Doctrine_Query::create()->update('Page')
                ->set('pageAttributeId', $i);
            if ($i == 1) {
                $updatePagesAttributeId->where('permalink="info/contact"');
            } else if ($i == 2) {
                $updatePagesAttributeId->where('permalink="info/faq"');
            } else if ($i == 3) {
                $updatePagesAttributeId->where('permalink!="info/faq"');
                $updatePagesAttributeId->andWhere('permalink!="info/contact"');
            }
            $updatePagesAttributeId->execute();
        }
        return true;
    }

    public static function replaceToPlusPage()
    {
        $updatePage = Doctrine_Query::create()
            ->update('Page p')
            ->set('p.permaLink', "'plus'")
            ->where('p.id=66')
            ->execute();
        return true;
    }

    public static function addSpecialPagesOffersCount($spcialPageId, $offersCount)
    {
        $updatePage = Doctrine_Query::create()
            ->update('Page p')
            ->set('p.offersCount', $offersCount)
            ->where('p.id='.$spcialPageId)
            ->execute();
        return true;
    }

    public static function getPageHomeImageByPermalink($permalink)
    {
        $pageHomeImage = Doctrine_Query::create()
            ->select('p.id,homepageimage.*')
            ->from('Page p')
            ->leftJoin("p.homepageimage homepageimage")
            ->where('p.permalink="'.$permalink.'"')
            ->andWhere('p.deleted = 0')
            ->fetchOne();
        $imagePath = '';
        if (!empty($pageHomeImage->homepageimage)) {
            $imagePath = PUBLIC_PATH_CDN.$pageHomeImage->homepageimage->path
                .$pageHomeImage->homepageimage->name;
        }
        return $imagePath;
    }

    ######################################################
    ############ END REFACTORED CODE #####################
    ######################################################

    /**
     * get default page
     * @author kkumar
     * @version 1.0
     */

    public function DefaultPagesList()
    {
        $q = Doctrine_Query::create()
            ->select('id,pagetitle')
            ->from('Page p')
            ->where('p.pagetype="default"')
            ->andWhere('p.deleted = 0')
            ->andWhere('p.publish = 1')
            ->orderBy('p.pagetitle ASC')
            ->fetchArray();
        return $q;
    }

    /**
     * get offer pages
     * @author kkumar
     * @version 1.0
     */

    public function getPagesOffer()
    {
        $q = Doctrine_Query::create()
            ->select('id,pagetitle')->from('Page p')
            ->where('p.showpage=1')
            ->andWhere('p.publish=1')
            ->andWhere('p.pagelock=0')
            ->andWhere('p.deleted=0')
            ->fetchArray();
        return $q;
    }
    /**
     * get all pages
     * @author kkumar
     * @version 1.0
     */
    public function getPages($params)
    {
        $srhPage  =     (isset($params["searchText"]) && trim($params["searchText"])!='undefined') ? $params["searchText"] : '';
        $conn2 = BackEnd_Helper_viewHelper::addConnection();

        if (Auth_StaffAdapter::hasIdentity()) {
            $roleId =   Zend_Auth::getInstance()->getIdentity()->roleId;
        }

        BackEnd_Helper_viewHelper::closeConnection($conn2);

        $pageList = Doctrine_Query::create()
            ->select('p.pageTitle,p.pageType,p.permaLink,p.created_at,p.contentManagerName')
            ->from('Page p')
            ->where('p.deleted=0')
            ->andWhere("p.pagetitle LIKE ?", "$srhPage%");
      if($roleId>2){
        $pageList->andWhere("p.pageLock = 0");
      }

      if(trim($params["searchType"])!='undefined'){
        $pageList->andWhere("p.pagetype = '".$params['searchType']."'");
      }
        $result =   DataTable_Helper::generateDataTableResponse($pageList,
                $params,
                array("__identifier" => 'p.pageTitle','p.pageTitle','p.pageType','p.permaLink','p.created_at','p.contentManagerName'),
                array(),
                array());
        return $result;

    }

    /**
     * get trashed pages
     * @author kkumar
     * @version 1.0
     */
    public function gettrashedPages($params)
    {
        $srhPage    =   (isset($params["searchText"]) && trim($params["searchText"])!='undefined') ? $params["searchText"] : '';
        $pageList = Doctrine_Query::create()
        ->select('p.pageTitle,p.created_at,p.updated_at,p.contentManagerName')
        ->from('Page p')
        ->where('p.deleted=1')
        ->andWhere("p.pagetitle LIKE ?", "$srhPage%");

        $result =   DataTable_Helper::generateDataTableResponse($pageList,
                $params,
                array("__identifier" => 'p.pageTitle','p.created_at','p.updated_at','p.contentManagerName'),
                array(),
                array());
        return $result;

    }



    /**
     * get page detail where is money saving
     * @author rkumar
     * @version 1.0
     */

    public function checkFotterpages($tempid)
    {
        $q = Doctrine_Query::create()
        ->select()->from('Page p')
        ->where('p.pageAttributeId="'.$tempid.'"')
        ->andWhere('p.deleted=0')
        ->fetchArray();

        return $q;
    }


    /**
     * save page detail
     * @author kkumar
     * @version 1.0
     */

    public function savePage($params)
    {
        $this->pageType='default';
        $this->maxOffers  = 0;
        $this->oderOffers = 0; 
        if (isset($params['selectedpageType'])){

              $this->pageType='offer';
              if(trim($params['maxOffer'])!=''){
                $this->maxOffers = BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxOffer']);
              }
              $this->oderOffers = 'desc';
              if(isset($params['offersOrderchk'])){
              $this->oderOffers = 'asc';
              }
             if(isset($params['timeCostraintchk'])){
                $this->enableTimeConstraint=1;
                $this->timenumberOfDays = BackEnd_Helper_viewHelper::stripSlashesFromString($params['numberofDays']);
                $this->timeType = BackEnd_Helper_viewHelper::stripSlashesFromString($params['postwithin']);
            }
            if(isset($params['wordCostraintchk'])){
               $this->enableWordConstraint=1;
               $this->wordTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['wordConstraintTxt']);
            }
            if(isset($params['awardCostraintchk'])){
               $this->awardConstratint=1;
               $this->awardType = BackEnd_Helper_viewHelper::stripSlashesFromString($params['awardConstraintDropdown']);
            }
             if(isset($params['clickCostraintchk'])){
                $this->enableClickConstraint=1;
                $this->numberOfClicks = BackEnd_Helper_viewHelper::stripSlashesFromString($params['clickConstraintTxt']);
            }
             if(isset($params['coupconCoderegularchk'])){
                $this->couponRegular =BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCoderegularchk']);
             }
             if(isset($params['coupconCodeeditorchk'])){
                $this->couponEditorPick = BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeditorchk']);
             }
             if(isset($params['coupconCodeeclusivechk'])){
                $this->couponExclusive=BackEnd_Helper_viewHelper::stripSlashesFromString ($params['coupconCodeeclusivechk']);
             }
             if(isset($params['saleregularchk'])){
                $this->saleRegular= BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleregularchk']);
             }
             if(isset($params['saleeditorchk'])){
                 $this->saleEditorPick =BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeditorchk']);
             }
             if(isset($params['saleeclusivechk'])){
                $this->saleExclusive=BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeclusivechk']);
             }
             if(isset($params['printableregularchk'])){
                $this->printableRegular =BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableregularchk']);
             }
             if(isset($params['printableeditorchk'])){
                $this->printableEditorPick = BackEnd_Helper_viewHelper::stripSlashesFromString ($params['printableeditorchk']);
             }
             if(isset($params['printableexclusivechk'])){
                $this->printableExclusive = BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableexclusivechk']);
             }

             $this->showPage =BackEnd_Helper_viewHelper::stripSlashesFromString($params['showPage']);

        }

        $this->publish   = 1;
        $this->timeOrder   = 0;
        if($params['savePagebtn']=='draft'){
            $this->publish   = 0;
        }


        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

            $result = self::uploadImage('logoFile');
            $this->logoId = 0;
            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']);
                $this->logo->ext = $ext;
                $this->logo->path = $result['path'];
                $this->logo->name = $result['fileName'];
            } else {
                return false;
            }
        }

        if (isset($_FILES['headerFile']['name']) && $_FILES['headerFile']['name'] != '') {
            $result = self::uploadImage('headerFile');
            $this->pageHeaderImageId = 0;
            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']);
                $this->pageheaderimage->ext = $ext;
                $this->pageheaderimage->path = $result['path'];
                $this->pageheaderimage->name = $result['fileName'];
            } else {
                return false;
            }
        }


        if (isset($_FILES['homepageFile']['name']) && $_FILES['homepageFile']['name'] != '') {
            $result = self::uploadImage('homepageFile');
            $this->pageHomeImageId = 0;
            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                    $result['fileName']
                );
                $this->homepageimage->ext = $ext;
                $this->homepageimage->path = $result['path'];
                $this->homepageimage->name = $result['fileName'];
            } else {
                return false;
            }
        }

        if(isset($params['publishDate']) && $params['publishDate']!=''){
        $this->publishDate = date('Y-m-d',strtotime($params['publishDate'])).' '.date('H:i:s',strtotime($params['publishTimehh'])) ;
        }
        $this->pageTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageTitle']);
        $this->permaLink = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagepermalink']);
        $this->metaTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaTitle']);
        $this->metaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaDesc']);
        $this->customHeader = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageCustomHeader']);
        $this->content = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $this->pageLock = 0;
        if (isset($params['lockPageStatuschk'])){
            $this->pageLock = 1;
        }
        
        isset($params['showSitemapStatuscheck']) && $params['showSitemapStatuscheck'] == 1 ? $this->showsitemap = 1 : $this->showsitemap = 0;
        isset($params['showMobileMenuStatuscheck']) && $params['showMobileMenuStatuscheck'] == 1 ? $this->showinmobilemenu = 1 : $this->showinmobilemenu = 0;

        if(trim($params['pageTemplate'])!=''){
        $this->pageAttributeId = $params['pageTemplate'];
        }
        $conn2 = BackEnd_Helper_viewHelper::addConnection();
        $this->contentManagerId = Auth_StaffAdapter::getIdentity()->id;
        $this->contentManagerName = Auth_StaffAdapter::getIdentity()->firstName . " " . Auth_StaffAdapter::getIdentity()->lastName;
        BackEnd_Helper_viewHelper::closeConnection($conn2);
        $selectedWidgets = explode(',',$params['selectedWigetForPage']);

        foreach($selectedWidgets as $widget){
            if(trim($widget)!=''){
             $this->refPageWidget[]->widgetId = $widget;
            }
        }

        if($params['pageTemplate'] == 13){

            for($a=0;$a<count($params['artcatgs']);$a++){

                $this->moneysaving[]->categoryid = $params['artcatgs'][$a];
            }
        }


        try {
        //call cache function
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            $pagePermalinkParam =
                FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['pagepermalink']);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_'.$pagePermalinkParam.'_data');

        
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$this->id.'_image');

            $key = 'all_widget' . $params['pageTemplate'] . "_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            //$getPage = Doctrine_Core::getTable ( 'Page' )->findOneBy ( "permalink", $params['pagepermalink'] );
          
            $page = $this->save();
            $pageId =  $this->id;

            $permalink = $this->permaLink ;

            #update varnish for this page
            if(isset($permalink)) {
                // Add urls to refresh in Varnish
                $varnishObj = new Varnish();
                $varnishObj->addUrl( HTTP_PATH_FRONTEND . $permalink);
            }

            $route = new RoutePermalink();
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
                        //$route->permalink = "plus/".$params['pagepermalink'];
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

            $route->save();



            $i=0;
            foreach($selectedWidgets as $widget){
                $q = Doctrine_Query::create()->update('refPageWidget')
                ->set('stauts', 1)
                ->set('position', $i)
                ->where('pageId = '.$pageId .'')
                ->andWhere('widgetId = '.$widget .'')
                ->execute();
                $i++;
            }




            return true;
        }catch (Exception $e) { return false;
        }
    }

    /**
     * get page detail
     * @author kkumar
     * @version 1.0
     */

    public function getPageDetail($pageId)
    {
        $pageDetails = Doctrine_Query::create()
        ->select('p.*,w.*,logo.*, pageheaderimage.*, homepageimage.*, artcatg.pageid,artcatg.categoryid,')
        ->from('Page p')
        ->leftJoin('p.widget w')
        ->leftJoin("p.logo logo")
        ->leftJoin("p.pageheaderimage pageheaderimage")
        ->leftJoin("p.homepageimage homepageimage")
        ->leftJoin("p.moneysaving artcatg")
        ->where('p.id='.$pageId.'')
        ->fetchArray();
        return $pageDetails;
    }


    /**
     * update page detail
     * @author kkumar
     * @version 1.0
     */


    public function updatePage($params)
    {
        $this->slug=$this->slug;
        $this->pageType='default';
        if (isset($params['selectedpageType'])){

            $this->pageType='offer';
            $this->maxOffers ='';
            if(trim($params['maxOffer'])!=''){
                $this->maxOffers = BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxOffer']);
            }
            //echo $params['offersOrderchk']; die;
            $this->oderOffers = 0;
            if(isset($params['offersOrderchk'])){
                $this->oderOffers = $params['offersOrderchk'];
            }
            $this->enableTimeConstraint=0;
            $this->timenumberOfDays = 0;
            $this->timeType = 0;

            if(isset($params['timeCostraintchk'])){
                $this->enableTimeConstraint=1;
                $this->timenumberOfDays = $params['numberofDays'];
                $this->timeType =BackEnd_Helper_viewHelper::stripSlashesFromString ($params['postwithin']);
            }

            $this->enableWordConstraint=0;
            $this->wordTitle = '';

            if(isset($params['wordCostraintchk'])){
                $this->enableWordConstraint=1;
                $this->wordTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['wordConstraintTxt']);
            }
            $this->awardConstratint=0;
            $this->awardType = 0;
            if(isset($params['awardCostraintchk'])){
                $this->awardConstratint=1;
                $this->awardType = BackEnd_Helper_viewHelper::stripSlashesFromString($params['awardConstraintDropdown']);
            }

            $this->enableClickConstraint=0;
            $this->numberOfClicks = 0;

            if(isset($params['clickCostraintchk'])){
                $this->enableClickConstraint=1;
                $this->numberOfClicks =BackEnd_Helper_viewHelper::stripSlashesFromString($params['clickConstraintTxt']);
            }

            $this->couponRegular = 0;
            if(isset($params['coupconCoderegularchk'])){
                $this->couponRegular = BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCoderegularchk']);
            }
            $this->couponEditorPick = 0;
            if(isset($params['coupconCodeeditorchk'])){
                $this->couponEditorPick = BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeditorchk']);
            }
            $this->couponExclusive= 0;
            if(isset($params['coupconCodeeclusivechk'])){
                $this->couponExclusive= BackEnd_Helper_viewHelper::stripSlashesFromString($params['coupconCodeeclusivechk']);
            }
            $this->saleRegular= 0;
            if(isset($params['saleregularchk'])){
                $this->saleRegular= BackEnd_Helper_viewHelper::stripSlashesFromString( $params['saleregularchk']);
            }
            $this->saleEditorPick = 0;
            if(isset($params['saleeditorchk'])){
                $this->saleEditorPick = BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeditorchk']);
            }
            $this->saleExclusive=0;
            if(isset($params['saleeclusivechk'])){
                $this->saleExclusive=BackEnd_Helper_viewHelper::stripSlashesFromString($params['saleeclusivechk']);
            }
            $this->printableRegular = 0;
            if(isset($params['printableregularchk'])){
                $this->printableRegular = BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableregularchk']);
            }
            $this->printableEditorPick = 0;
            if(isset($params['printableeditorchk'])){
                $this->printableEditorPick = BackEnd_Helper_viewHelper::stripSlashesFromString( $params['printableeditorchk']);
            }
            $this->printableExclusive = 0;
            if(isset($params['printableexclusivechk'])){
                $this->printableExclusive = BackEnd_Helper_viewHelper::stripSlashesFromString($params['printableexclusivechk']);
            }

            $this->showPage = BackEnd_Helper_viewHelper::stripSlashesFromString($params['showPage']);

        }

        $this->publish  = 1;
        if($params['savePagebtn']=='draft'){

            $this->publish  = 0;
        }

        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

            $result = self::uploadImage('logoFile');



            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']);
                $this->logo->ext = $ext;
                $this->logo->path = $result['path'];
                $this->logo->name = $result['fileName'];
            }else {
                return false;
            }
        }

        if (isset($_FILES['headerFile']['name']) && $_FILES['headerFile']['name'] != '') {

            $result = self::uploadImage('headerFile');



            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']);
                $this->pageheaderimage->ext = $ext;
                $this->pageheaderimage->path = $result['path'];
                $this->pageheaderimage->name = $result['fileName'];
            }else {
                return false;
            }
        }

        if (isset($_FILES['homepageFile']['name']) && $_FILES['homepageFile']['name'] != '') {
            $result = self::uploadImage('homepageFile');
            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension($result['fileName']);
                $this->homepageimage->ext = $ext;
                $this->homepageimage->path = $result['path'];
                $this->homepageimage->name = $result['fileName'];
            } else {
                return false;
            }
        }

        if(isset($params['publishDate']) && $params['publishDate']!=''){
            $this->publishDate = date('Y-m-d',strtotime($params['publishDate'])).' '.date('H:i:s',strtotime($params['publishTimehh'])) ;
        }


        $this->pageTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageTitle']);

        $this->permaLink = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagepermalink']);
        $this->metaTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaTitle']);
        $this->metaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pagemetaDesc']);
        $this->customHeader = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageCustomHeader']);
        $this->content = BackEnd_Helper_viewHelper::stripSlashesFromString($params['pageDesc']);
        $this->pageLock = 0;
        if (isset($params['lockPageStatuschk'])){
            $this->pageLock = 1;
        }
        
        isset($params['showSitemapStatuscheck']) && $params['showSitemapStatuscheck'] == 1 ? $this->showsitemap = 1 : $this->showsitemap = 0;
        isset($params['showMobileMenuStatuscheck']) && $params['showMobileMenuStatuscheck'] == 1 ? $this->showinmobilemenu = 1 : $this->showinmobilemenu = 0;
        if(trim($params['pageTemplate'])!=''){
            $this->pageAttributeId = $params['pageTemplate'];
        }else{
            $this->pageAttributeId = NULL;
        }
        if (isset($params['pageAuthor']) && $params['pageAuthor']!=''){
        $this->contentManagerId = $params['pageAuthor'];
        $this->contentManagerName = $params['selectedpageAuthorName'];
        }
        $selectedWidgets = explode(',',$params['selectedWigetForPage']);
        $this->refPageWidget->delete();


        foreach($selectedWidgets as $widget){
            if(trim($widget)!=''){
              $this->refPageWidget[]->widgetId = $widget;
            }
        }
        $pageid = @$params['pageId'];
        MoneySaving :: delartCategories($params['pageId']);
        //$getPage = Doctrine_Core::getTable ( 'Page' )->find($this->id );
        $getPage = Doctrine_Query::create()->select()->from('Page')->where('id = '.$this->id )->fetchArray();
        if($params['pageTemplate'] == 13){
            for($a=0;$a<count($params['artcatgs']);$a++){

                    $monwysavingobj = new MoneySaving();
                    $monwysavingobj->pageid = $params['pageId'];
                    $monwysavingobj->categoryid = $params['artcatgs'][$a];
                    $monwysavingobj->save();
                    /*** $this->moneysaving[]->categoryid = $params['artcatgs'][$a]; *** This is value asave example , It may be wrong***/
            }
        }


        if(!empty($getPage[0]['permaLink'])){

            $getRouteLink = Doctrine_Query::create()->select()->from('RoutePermalink')->where("permalink = '".$getPage[0]['permaLink']."'")->andWhere('type = "PG"')->fetchArray();

            //$updateRouteLink = Doctrine_Core::getTable('RoutePermalink')->findOneBy('permalink', $getPage[0]['permaLink'] );
        }else{
            $updateRouteLink = new RoutePermalink();

        }

        //print_r($getRouteLink); die;
        try{
        //call cache function
            $slug = $this->pageAttributeId;
            $pagedatakey ="all_". "pagedata".$slug ."_list";
            $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pagedatakey);
            //key not exist in cache
            if(!$flag) {
                FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($pagedatakey);
            }
            $pageKey ="all_moneysavingpage".$this->id."_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($pageKey);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');


            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$this->id.'_image');
            $pagePermalinkParam =
                FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['pagepermalink']);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_'.$pagePermalinkParam.'_data');

            $key = 'all_widget' . $params['pageTemplate'] . "_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $page = $this->save();
            $pageId =  $this->id;


            $permalink = $this->permaLink ;

            #update varnish for this page
            if (isset($permalink)) {
            // Add urls to refresh in Varnish
                $varnishObj = new Varnish();
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink);
                if (!$permalink=='plus') {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink .'/2');
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $permalink.'/3');
                }
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_categorieen'));
            }


            if(!empty($getRouteLink)){
                //print_r($getRouteLink); die;
                $updateRouteLink = Doctrine_Query::create()->update('RoutePermalink')
                                                           ->set('permalink', "'".$params['pagepermalink']."'")
                                                           ->set('type',"'PG'")
                                                           ->set('exactlink', "'".$params['pagepermalink']."'");

                //$updateRouteLink->permalink = $params['pagepermalink'];
                //$updateRouteLink->type = 'PG';
                //$updateRouteLink->exactlink = $params['pagepermalink'];

                switch ($params['pageTemplate']) {
                    case 4:
                        $exactLink = 'index/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'index/index/attachedpage/'.$this->id;
                    break;
                    case 5:
                        $exactLink = 'offer/popularoffer/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'offer/popularoffer/attachedpage/'.$this->id;
                    break;
                    case 6:
                        $exactLink = 'offer/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'offer/index/attachedpage/'.$this->id;
                    break;
                    case 7:
                        $exactLink = 'store/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'store/index/attachedpage/'.$this->id;
                    break;
                    case 8:
                        $exactLink = 'store/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'store/index/attachedpage/'.$this->id;
                    break;
                    case 9:
                        $exactLink = 'category/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'category/index/attachedpage/'.$this->id;
                    break;
                    case 10:
                        $exactLink = 'category/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'category/index/attachedpage/'.$this->id;
                    break;
                    case 13:

                        $exactLink = 'plus/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('permalink', "'".$params['pagepermalink']."'");
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'moneysavingguide/index/attachedpage/'.$this->id;
                    break;
                    case 14:
                        $exactLink = 'about/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'about/index/attachedpage/'.$this->id;
                    break;
                    case 17:
                        $exactLink = 'login/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                    break;
                    case 18:
                        $exactLink = 'login/forgotpassword/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                    break;
                    case 19:
                        $exactLink = 'freesignup/index/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                    break;
                    case 29:
                        $exactLink = 'login/memberwelcome/attachedpage/'.$this->id;
                        $updateRouteLink->set('exactlink',"'".$exactLink."'");
                        //$updateRouteLink->exactlink = 'login/memberwelcome/attachedpage/'.$this->id;
                    break;

                }

                $updateRouteLink->where('type = "PG"')->andWhere('permalink = "'.$getPage[0]['permaLink'].'"')->execute();
                //echo "<pre>"; print_r($updateRouteLink); die;
            }


            $i=0;
            foreach($selectedWidgets as $widget){
                $q = Doctrine_Query::create()->update('refPageWidget')
                ->set('stauts', 1)
                ->set('position', $i)
                ->where('pageId = '.$pageId .'')
                ->andWhere('widgetId = '.$widget .'')
                ->execute();
                $i++;
            }


            return true;
        }catch (Exception $e){
            return false;
        }



    }


    /**
     * export page list
     * @author jsingh
     * @version 1.0
     */

public static function exportpagelist()
{
        $pageList = Doctrine_Query::create()->select('F.*')
        ->from("Page  F")
        ->where("F.deleted=0")
        ->orderBy("F.id DESC")
        ->fetchArray();
    return $pageList;

    }
    /**
     * restore shop by id
     * @param $id
     * @author jsingh
     * @version 1.0
     */
    public static function restorePage($id)
    {
        if ($id) {
            //update status of record by id(deleted=0)
            $O = Doctrine_Query::create()->update('Page')->set('deleted', '0')
            ->where('id=' . $id);
            $O->execute();

            $u = Doctrine_Core::getTable("Page")->find($id);

            $r = Doctrine_Query::create()->update('RoutePermalink')->set('deleted', '0')
            ->where('permalink= "'.$u->permaLink.'"');
            $r->execute();


        } else {
            $id = null;
        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');
       

        return $id;
    }

    /**
     * delete page permanently
     * @author jsingh
     * @version 1.0
     */

    public static function deletepage($id)
    {
        $O = Doctrine_Query::create()->update('Page')->set('deleted', '2')
        ->where('id=' . $id);
        $O->execute();

        $u = Doctrine_Core::getTable("Page")->find($id);

        $r = Doctrine_Query::create()->delete('RoutePermalink')
        ->where('permalink= "'.$u->permaLink.'"');
        $r->execute();

        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');

        return 1;
    }
    /**
     * Move record in trash.
     * @param integer $id
     * @version 1.0
     * @author  jsingh
     * @return integer $id
     */
    public static function moveToTrash($id)
    {
        if ($id) {
            //find record by id
            $u = Doctrine_Core::getTable("Page")->find($id);
            $u->delete();

            $r = Doctrine_Core::getTable("RoutePermalink")->findOneBy('permalink', $u->permaLink);
            if(!empty($r)) {
                $r->delete();
            }

        } else {

            $id = null;
        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_page_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('page_header'.$id.'_image');

        return $id;

    }


    /**
     * get to five page
     * @param string $keyword
     * @return array $data
     * @author jsingh
     * @version 1.0
     */
    public static function searchToFivePage($keyword,$type)
    {
        $qry = Doctrine_Query::create()->select('p.pagetitle as pagetitle ')
        ->from("Page p")->where('p.deleted='.$type.'')
        ->andWhere("p.pagetitle LIKE ?", "$keyword%");

        $role =  Zend_Auth::getInstance()->getIdentity()->roleId;
        if($role=='4' || $role=='3') {
            $qry->andWhere("p.pageLock='0'");

        }

        $data = $qry->orderBy("p.pagetitle ASC")->limit(5)->fetchArray();
        return $data;
    }

/**-------------------start front end-------------------------*/

    /**
     * Fetches one record according to id to show in F.A.Q. form
     * @param $id
     * @author mkaur
     */
    public static function getDefaultPage($id)
    {
        $data = Doctrine_Query::create()
        ->select("p.*")
        ->from('Page p')
        ->where("p.id = ?" , $id)
        ->andWhere('p.deleted=0')
        ->fetchOne(null , Doctrine::HYDRATE_ARRAY);
        return $data;
    }

    /**
     * Fetches all list of records of F.A.Q. pages
     * @author mkaur
     */
    public static function getPageList($id)
    {
        $data = Doctrine_Query::create()
        ->select("p.*")
        ->from('Page p')
        ->where('p.deleted=0')
        ->andWhere('p.pageAttributeId='.$id)
        ->fetchArray();
        return $data;
    }


    /**
     * upload image
     * @param $_FILES[index]  $file
     */
    public function uploadImage($file)
    {
        if (!file_exists(UPLOAD_IMG_PATH))
            mkdir(UPLOAD_IMG_PATH,776, true);

        // generate upload path for images related to shop
        $uploadPath = UPLOAD_IMG_PATH . "page/";
        $adapter = new Zend_File_Transfer_Adapter_Http();

        // generate real path for upload path
        $rootPath = ROOT_PATH . $uploadPath;

        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath))
            mkdir($rootPath,776, true);

        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        // get file name
        $name = $adapter->getFileName($file, false);

        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;

        // generates complete path of image
        $cp = $rootPath . $newName;


        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName ,132, 95, $path);


        $path = ROOT_PATH . $uploadPath . "thum_page_small" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] ,$newName ,60, 40, $path);

        $path = ROOT_PATH . $uploadPath . "thum_page_large_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] ,$newName ,150, 100, $path);

        $path = ROOT_PATH . $uploadPath . "thum_extra_large_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] ,$newName ,170, 127, $path);

        /**
         *   generating thumnails for upload logo if file in shop logo
         */
        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;

            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName,
                    200, 150, $path);
        }

        // apply filter to rename file name and set target
        $adapter
        ->addFilter(
                new Zend_Filter_File_Rename(
                        array('target' => $cp, 'overwrite' => true)),
                null, $file);

        // recieve file for upload
        $adapter->receive($file);

        // check is file is valid then

        if ($adapter->isValid($file)) {

            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);

        } else {

            return array("status" => "-1",
                    "msg" => "Please upload the valid file");

        }

    }

    public function deletePageImage($params)
    {
        $O = Doctrine_Query::create()
        ->update('Page')
        ->set('logoId', '0')
        ->where('id=' . $params['pageId']);
        $O->execute();
        return 1;
        //print_r($params);
    }

    /**
     * get page detail from slug name
     * @author Raman
     * @version 1.0
     */

    public static function getPageDetailFromSlug($slug)
    {
        $q = Doctrine_Query::create()
        ->select('p.content, p.pagetitle')
        ->from('Page p')
        ->where('p.slug="'.$slug.'"')
        ->andWhere('p.deleted=0')
        ->fetchArray();
        return $q;
    }

    /**
     *  Author Er.kundal
     *  Get footer pages
     *  Version: 1.0
     */
    public function getFooterpages()
    {

        $data = Doctrine_Query::create()
        ->select()
        ->from('page p')
        ->where('p.pageAttributeId=15' )
        ->andWhere('p.deleted=0')
        ->limit(10)->fetchArray();

        return $data;
    }

    public function getPageAttributes($slug)
    {
        $q = Doctrine_Query::create()
        ->select('p.id,a.*')->from('Page p')
        ->leftJoin('p.pageattribute a')
        ->where('p.slug="'.$slug.'"')
        ->andWhere('p.deleted=0')
        ->fetchArray();
        return $q;
    }

    /**
     *  Author Raman
     *  Get page details on id basis
     *  Version: 1.0
     */


    /**
     * get page attrubute from page table by id
     *
     * @param integre $id
     * @return Ambigous <mixed, boolean, Doctrine_Record, Doctrine_Collection, PDOStatement, Doctrine_Adapter_Statement, Doctrine_Connection_Statement, unknown, number>
     * @author kraj
     * @version 1.0
     */
    public static function getPageFromPageAttrInOffer($id)
    {
        $data = Doctrine_Query::create()->select('p.id,p.permaLink,p.pageTitle,p.metaTitle,p.metaDescription')
        ->from('Page p')
        ->where("pageAttributeId = ?", $id)
        ->andWhere('p.deleted=0')
        ->orderBy('id DESC')
        ->fetchOne();
        return $data;
    }
    /**
     * get page attrubute from page table by id
     *
     * @param integre $id
     * @return Ambigous <mixed, boolean, Doctrine_Record, Doctrine_Collection, PDOStatement, Doctrine_Adapter_Statement, Doctrine_Connection_Statement, unknown, number>
     * @author kraj
     * @version 1.0
     */
    public static function getPageFromPageAttributeInOfferPop($id)
    {
        $data = Doctrine_Query::create()->select('p.id,p.permaLink')
        ->from('Page p')
        ->where("pageAttributeId = ?", $id)
        ->andWhere('p.deleted=0')
        ->orderBy('id DESC')
        ->fetchOne();
        return $data;
    }

    /**
     *  Author blal
     *  Get offer list pages
    */

    public static function getOfferListPage()
    {
        $data = Doctrine_Query::create()
                ->select('p.id,p.pageType,p.pageTitle,p.permaLink,p.metaDescription,i.path,i.name')
                ->from('Page p')
                ->leftJoin('p.logo i')
                ->where("pageType = ?", 'offer')
                ->andWhere('p.deleted=0')
                ->limit(9)
                ->fetchArray();
        return $data;
    }


/**
 * get default page
 * @author kkumar
 * @version 1.0
 */

    public static function pagesPermalinksList()
    {
        $pageIdsAndPermalinks = Doctrine_Query::create()
        ->select('id, permaLink')
        ->from('Page p')
        ->where('p.deleted = 0')
        ->andWhere('p.publish = 1')
        ->andWhere('p.showsitemap = 1')
        ->orderBy('p.pagetitle ASC')
        ->fetchArray();
        return $pageIdsAndPermalinks;
    }

}
