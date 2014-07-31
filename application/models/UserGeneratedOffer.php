<?php

/**
 * Offer
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class UserGeneratedOffer extends BaseOffer
{
    /**
     * getofferList(deleted and non deleted by flag) fetches all record from database
     * also search according to keyword if present.
     * @param array $params
     * @return array $offerList
     * @version 1.0
     * @author
     */
    public static function getofferList($params)
    {
        $role =  Zend_Auth::getInstance()->getIdentity()->roleId;

        $srhOffer 	= 	$params["offerText"]!='undefined' ? $params["offerText"] : '';
        $srhShop 	=   $params["shopText"]!='undefined' ? $params["shopText"] : '';
        $type 		=   $params["couponType"]!='undefined' ? $params["couponType"] : '';
        //get offer deleted or other by flag flag (1 or 0)
        $flag = $params['flag'];
        //echo 'O ' . $srhOffer ;
        //echo 'S ' . $srhShop ;
        //echo 'T ' . $type ;

        $offerList = Doctrine_Query::create()
        ->select('o.*,s.name as shopname,s.accountManagerName as acName')
        ->from("UserGeneratedOffer o")->leftJoin('o.shop s')
        ->where("o.deleted="."'$flag'")
        ->andWhere("o.userGenerated = '1'");

        //condition for editor
        if($role=='4') {
            $offerList->andWhere("o.Visability='DE'");

        }
        if($srhShop=='' && $type=='' && $srhOffer!='') {
            //search only offer from database
            $offerList->andWhere("o.title LIKE '$srhOffer%'");


        } else {

            if($srhShop=='' && $type!='') {
                //search only offer by coupon type from database
                $offerList->andWhere("o.discountType="."'$type'");

            }  else if($srhShop!='' && $type!='' && $srhShop!='') {
                //search only offer by coupon type and search shop from database
                $offerList->where("o.discountType="."'$type'")
                ->andWhere("s.name LIKE  '$srhShop%'");

            } else if($srhShop!=''){

                //search only offer by shop from database
                $offerList->andWhere("s.name LIKE  '$srhShop%'");
            }
        }
        $offerList->orderBy("o.id DESC");
        //echo $offerList->getSqlQuery();die;
        $result	=	DataTable_Helper::generateDataTableResponse($offerList,
                $params,
                array("__identifier" => 'o.id','o.id','o.title','o.approved','o.discountType', 'shopname','o.startDate','o.endDate','o.couponCode','acName','o.extendedOffer'),
                array(),
                array());

        return $result;

    }

    /**
     * Move record in trash.
     * @param integer $id
     * @version 1.0
     * @author  kraj
     * @return integer $id
     */
    public static function moveToTrash($id)
    {
        if ($id) {
            //find record by id
            $u = Doctrine_Core::getTable("UserGeneratedOffer")->find($id);

            $key = 'all_offersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_latestUpdatesInStore'  .$u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_expiredOffersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_relatedShopInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mspopularKey);


            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeNewOffers_list');

            $u->delete();

        } else {

            $id = null;
        }
        //call cache function
        return $id;

    }
    /**
     * Permanent delete record from database.
     * @param integer $id
     * @version 1.0
     * @author kraj
     * @return integer $id
     */
    public static function deleteOffer($id)
    {
        if ($id) {
            //find record by id and change status (deleted=1)
            $u = Doctrine_Core::getTable("Offer")->find($id);

            $key = 'all_offersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_latestUpdatesInStore'  .$u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_expiredOffersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_relatedShopInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mspopularKey);


            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeNewOffers_list');

            $del = Doctrine_Query::create()->delete()
            ->from('Offer o')
            ->where("o.id=" . $id)
            ->execute();

        } else {
            $id = null;
        }
        return $id;

    }
    /**
     * restore offer from trash to list
     * @param integer $id
     * @version 1.0
     * @author kraj
     */
    public static function restoreOffer($id)
    {
        if ($id) {

            $u = Doctrine_Core::getTable("UserGeneratedOffer")->find($id);

            $key = 'all_offersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_latestUpdatesInStore'  .$u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_expiredOffersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_relatedShopInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mspopularKey);


            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeNewOffers_list');


            //update status of record by id(deleted=0)
            $O = Doctrine_Query::create()->update('Offer')
            ->set('deleted', '0')->where('id=' . $id);
            $O->execute();

        } else {
            $id = null;
        }

        return $id;
    }
    /**
     * restore offer from trash to list
     * @param integer $id
     * @version 1.0
     * @author Raman
     */
    public static function makeToOffline($id, $offvalue)
    {
        if ($id) {

            $u = Doctrine_Core::getTable("UserGeneratedOffer")->find($id);

            $key = 'all_offersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_latestUpdatesInStore'  .$u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_expiredOffersInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'all_relatedShopInStore'  . $u->shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mspopularKey);


            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeNewOffers_list');

            //update record of offer offline = 1
            $O = Doctrine_Query::create()->update('UserGeneratedOffer')
            ->set('offline', $offvalue)->where('id=' . $id);

            $O->execute();

        } else {
            $id = null;
        }

        return $id;
    }

    /**
     * Search to five offer
     * @param string $keyword
     * @param boolean $flag
     * @version 1.0
     * @return array $data
     * @author kraj
     */
    public static function searchToFiveOffer($keyword,$flag)
    {
        $data = Doctrine_Query::create()
        ->select('o.title as title')
        ->from("UserGeneratedOffer o")
        ->where('o.deleted=' . "'$flag'")
        ->andWhere('o.offline = 0')
        ->andWhere("o.title LIKE ?", "$keyword%")
        ->andWhere("o.userGenerated = '1'")
        ->orderBy("o.title ASC")->limit(5)->fetchArray();

        return $data;
    }
    /**
     * Search top five shop
     * @param string $keyword
     * @param boolean $flag
     * @return array $data
     * @author kraj
     */
    public static function searchToFiveShop($keyword,$flag)
    {
        $data = Doctrine_Query::create()
        ->select('o.id,s.name as name')
        ->from("UserGeneratedOffer o")->leftJoin('o.shop s')
        ->where('o.deleted=' . "'$flag'")
        ->andWhere('s.status = 1')
        ->andWhere("s.name LIKE '".$keyword."%'" )
        ->andWhere("o.userGenerated = '1'")
        ->orderBy("s.id ASC")->limit(5)->fetchArray();
        //->orderBy("s.id ASC")->limit(5)->getSqlQuery();

        //print_r($data);
        return $data;
    }

    /**
     * Save offer in the database
     * @param $shopDetail
     * @author kkumar
     */

    public function saveOffer($params)
    {
        // check the offer type
        if(isset($params['defaultoffercheckbox'])){     //offer type is default
            $this->Visability = 'DE';
            if($params['selctedshop']!=''){
            $this->shopId = $params['selctedshop'];
            }
        }else{                                            // offer type member only
            $this->Visability = 'MEM';
        }
        // check the discountype
        if(isset($params['couponCodeCheckbox'])){             // discount type coupon
            $this->discountType = 'CD';
            $this->couponCode = $params['couponCode'];
            if(isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {

                    $this->refOfferCategory[]->categoryId = $categories ;

                }
            }
        }else if(isset($params['couponCodeCheckbox'])){       // discount type sale
            $this->discountType = 'SL';
        }else{                                                // discount type printable
            $this->discountType = 'PA';
            //check printable document
            if(isset($params['uploadoffercheck'])){                          // upload offer

                $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  BackEnd_Helper_viewHelper::getImageExtension($fileName);
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                 preg_match($pattern, $fileName , $matches );
                if(@$matches[1]) {

                   $this->logo->ext = $ext;
                   $this->logo->path ='images/upload/offer';
                   $this->logo->name = BackEnd_Helper_viewHelper::stripSlashesFromString($fileName);
               }
            }else{                                                   // add offer refUrl
                $this->refOfferUrl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }


        $this->title = BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);
        if(isset($params['deepLinkStatus'])){
        $this->refURL =  BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        }
        foreach ($params['termsAndcondition'] as $terms) {
            if(trim($terms)!=''){
            $this->termandcondition[]->content = BackEnd_Helper_viewHelper::stripSlashesFromString($terms) ;
            }

        }

        $this->startDate = date('Y-m-d',strtotime($params['offerStartDate'])).' '.date('H:i:s',strtotime($params['offerstartTime'])) ;
        $this->endDate = date('Y-m-d',strtotime($params['offerEndDate'])).' '.date('H:i:s',strtotime($params['offerendTime'])) ;


        if(isset($params['attachedpages'])){
            foreach ($params['attachedpages'] as $pageId) {

                $this->refOfferPage[]->pageId = $pageId ;

            }
        }

        if(isset($params['extendedoffercheckbox'])){                  // check if offer is extended
          $this->extendedOffer = $params['extendedoffercheckbox'];
          $this->extendedTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferTitle']);
          $this->extendedUrl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferRefurl']);
          $this->extendedMetaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferMetadesc']);
          $this->extendedFullDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponInfo']);
        }

        $this->exclusiveCode=$this->editorPicks=0;
        if(isset($params['exclusivecheckbox'])){
            $this->exclusiveCode=1;
        }

        if(isset($params['editorpickcheckbox'])){
            $this->editorPicks=1;
        }

        $this->save();

        //call cache function
        $key = 'all_offersInStore'  . intval($params['selctedshop']) . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        $key = 'all_latestUpdatesInStore'  . intval($params['selctedshop']) . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        $key = 'all_expiredOffersInStore'  . intval($params['selctedshop']) . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        $key = 'all_relatedShopInStore'  . intval($params['selctedshop']) . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        $mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mspopularKey);

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeNewOffers_list');
        ;
    }



    /**
     * Update offer in the detail
     * @param $shopDetail
     * @author kkumar
     */

    public function updateOffer($params)
    {
        //echo "<pre>";
        //print_r($params);
        //die;
        // check the offer type
        if(isset($params['yesoffercheckbox'])){      //offer approval is yes
            $this->approved = 1;
            //if($params['selctedshop']!=''){
                $this->shopId = $params['selctedshop'];
            //}
        }else{
            $this->approved = 0;
        }
        // check the discountype
        if(isset($params['couponCodeCheckbox'])){             // discount type coupon
            $this->discountType = 'CD';
            $this->couponCode = $params['couponCode'];
            $this->refOfferCategory->delete();
            if(isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {

                    $this->refOfferCategory[]->categoryId = $categories;

                }
            }
        }else if(isset($params['saleCheckbox'])){       // discount type sale
            $this->discountType = 'SL';
        }else{                                          // discount type printable
            $this->discountType = 'PA';
            //check printable document
            if(isset($params['uploadoffercheck'])){  // upload offer

                $this->refOfferUrl = '';

                echo $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  BackEnd_Helper_viewHelper::getImageExtension($fileName);
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, $fileName , $matches );
                if(@$matches[1]) {

                    $this->logo->ext = $ext;
                    $this->logo->path ='images/upload/offer';
                    $this->logo->name = BackEnd_Helper_viewHelper::stripSlashesFromString($fileName);
                }

            }else{                                                     // add offer refUrl
                $this->refOfferUrl = BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }

        if(isset($params['addofferTitle'])){
            $this->title = BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);
        }

        if(isset($params['deepLinkStatus'])){
            $this->refURL =  BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        }else{
            $this->refURL =  '';
        }


        if(isset($params['description'])){

            $this->extendedFullDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($params['description']);
        }
        //echo $this->extendedFullDescription;die;
        $this->startDate = date('Y-m-d',strtotime($params['offerStartDate'])).' '.date('H:i:s',strtotime($params['offerstartTime'])) ;
        $this->endDate = date('Y-m-d',strtotime($params['offerEndDate'])).' '.date('H:i:s',strtotime($params['offerendTime'])) ;


        $this->refOfferPage->delete();
        if(isset($params['attachedpages'])){
            foreach ($params['attachedpages'] as $pageId) {

                $this->refOfferPage[]->pageId = $pageId;

            }
        }


    $this->save();
    $key = 'all_offersInStore'  . intval($params['selctedshop']) . '_list';
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

    $key = 'all_latestUpdatesInStore'  . intval($params['selctedshop']) . '_list';
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

    $key = 'all_expiredOffersInStore'  .intval($params['selctedshop']) . '_list';
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

    $key = 'all_relatedShopInStore'  . intval($params['selctedshop']) . '_list';
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

    $mspopularKey ="all_mspagepopularCodeAtTheMoment_list";
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($mspopularKey);

    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newoffer_list');
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeNewOffers_list');

    }



    /**
     * get list of offer for export
     * @author kraj
     * @return array $offerList
     * @version 1.0
     */
    public static function exportofferList()
    {
        $offerList = Doctrine_Query::create()
        ->select('o.*,s.name as shopname,s.accountManagerName as acName')
        ->from("Offer o")->leftJoin('o.shop s')
        ->where("o.deleted=0")
        ->orderBy("o.id DESC")->fetchArray();
        return $offerList;

    }

    public function getOfferDetail($offerId)
    {
        $shopDetail = Doctrine_Query::create()
        ->select('o.*,s.name,s.notes,s.accountManagerName,a.name as affname,p.id,tc.*,cat.id,img.*')
        ->from("Offer o")
        ->leftJoin('o.shop s')
        ->leftJoin('s.affliatenetwork a')
        ->leftJoin('o.page p')
        ->leftJoin('o.termandcondition tc')
        ->leftJoin('o.category cat')
        ->leftJoin('o.logo img')
        ->andWhere("o.id =$offerId")->andWhere("o.userGenerated = '1'")->fetchArray();
        return $shopDetail;
    }



    public function uploadFile($imgName)
    {
        $uploadPath = "images/upload/offer/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $imgName;



        //unlink image file from folder if exist
        if ($img) {
            @unlink($user_path . $img);
            @unlink($user_path . "thum_" . $img);
            @unlink($user_path . "thum_large" . $img);
        }
        if (!file_exists($user_path))
            mkdir($user_path);
        $adapter->setDestination(ROOT_PATH . $uploadPath);
        $adapter->addValidator('Extension', false, 'jpg,jpeg,png,gif,pdf');
        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) {
             $ext =  BackEnd_Helper_viewHelper::getImageExtension($info['name']);
            $name = $adapter->getFileName($file, false);
            $name = $adapter->getFileName($file);
            $orgName = time() . "_" . $info['name'];
            $fname = $user_path . $orgName;
            //call function resize image
            if($ext!='pdf') {
                $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;
                BackEnd_Helper_viewHelper::resizeImage($_FILES["uploadoffer"], $orgName,
                        126, 90, $path);

                //call function resize image
                $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
                BackEnd_Helper_viewHelper::resizeImage($_FILES["uploadoffer"], $orgName,
                        132, 95, $path);
            }
            $adapter->addFilter(
                    new Zend_Filter_File_Rename(
                            array('target' => $fname,
                                    'overwrite' => true)), null, $file);

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
  /**
   * addOffer
   *
   * Add userGenerate offer
   *
   * @param array $params
   * @author kraj
   * @version 1.0
   */
  public function addOffer($params)
  {
    $title = $params['offer_name'];
    $this->Visability = 'DE';
    $this->discountType = 'CD';
    $this->extendedFullDescription = $params['offer_desc'];
    $this->shopId = $params['shopId'];
    //$this->title = $title;
    $this->couponCode = BackEnd_Helper_viewHelper::stripSlashesFromString($params['offer_code']);
    $this->userGenerated = true;
    $this->authorId = Auth_VisitorAdapter::getIdentity()->id;
    $this->authorName = Auth_VisitorAdapter::getIdentity()->firstName;
    $this->save();
  }





}
