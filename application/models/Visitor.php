<?php
class Visitor extends BaseVisitor
{
    //const USER_SET_STATUS = 0;

    const INVALID_NEW_PASSWORD_STATUS = "-2";
    const INVALID_OLD_PASSWORD_STATUS = "-1";
    const SUCCESS = "200";

    public $currentLocale = null ;
    #############################################################
    ######### REFACTRED CODE ####################################
    #############################################################
    public static function checkDuplicateUser($email, $visitorId = null)
    {
        $emailAddress =  FrontEnd_Helper_viewHelper::sanitize($email);
        $visitorId =  FrontEnd_Helper_viewHelper::sanitize($visitorId);
        if ($visitorId!=null) {
            $visitorInformation  = Doctrine_Core::getTable("Visitor")->find($visitorId)->toArray();
        } else {
            $visitorInformation  = Doctrine_Core::getTable("Visitor")->findBy('email', $emailAddress)->toArray();
        }
        return count($visitorInformation);
    }
    /**
     * Fuction ngetFavoriteShopsForUser.
     *
     * This function checks the favorite shop of user on userid basis.
     *
     * @param integer $visitorId
     * @param integer $shopId
     */
    public static function getFavoriteShopsForUser($visitorId, $shopId)
    {
        $favouriteShopsStatus = false;
        if($shopId==0) {
            $favoriteShops = Doctrine_Query::create()->select("fv.id as id")
            ->from("FavoriteShop fv")
            ->where('fv.visitorId='.$visitorId)
            ->andWhere('fv.shopId='.$shopId)
            ->fetchArray();
            if (!empty($favoriteShops)) {
                $favouriteShopsStatus = true;
            } 
        }
        return $favouriteShopsStatus;
    }

    public function updateLoginTime($visitorId)
    {
        $visitor = Doctrine_Core::getTable("Visitor")->find($visitorId);
        if ($visitor->currentLogIn=='0000-00-00 00:00:00') {
            $user->currentLogIn = date('Y-m-d H:i:s');
        }
        $visitor->lastLogIn = $visitor->currentLogIn;
        $visitor->currentLogIn = date('Y-m-d H:i:s');
        $visitor->active = 1 ;
        $visitor->save();
    }

    public static function addVisitor($visitorInformation)
    {
        $visitor = new Visitor();
        $visitor->firstName = FrontEnd_Helper_viewHelper::sanitize($visitorInformation['firstName']);
        $visitor->lastName = FrontEnd_Helper_viewHelper::sanitize($visitorInformation['lastName']);
        $visitor->gender = FrontEnd_Helper_viewHelper::sanitize($visitorInformation['gender'] == 'M' ? 0 : 1);
        $visitor->email = FrontEnd_Helper_viewHelper::sanitize($visitorInformation['emailAddress']);
        $visitor->dateOfBirth =
            (
                $visitorInformation['dateOfBirthYear'].'-'
                .$visitorInformation['dateOfBirthMonth'].'-'
                .$visitorInformation['dateOfBirthDay']
            );
        $visitor->postalCode =
            FrontEnd_Helper_viewHelper::sanitize(
                isset($visitorInformation['postCode'])
                ? $visitorInformation['postCode']
                : ''
            );
        $visitor->password = FrontEnd_Helper_viewHelper::sanitize(md5($visitorInformation['password']));
        $visitor->weeklyNewsLetter = '1';
        $visitor->save();
        return $visitor->id;
    }

    public static function updatePasswordRequest($visitorId, $flag)
    {
         $visitor = Doctrine_Query::create()->update('Visitor')
         ->set('changepasswordrequest', $flag)
         ->where('id='. FrontEnd_Helper_viewHelper::sanitize($visitorId))
         ->execute();
         return;
    }

    public static function updateVisitorPassword($visitorId, $password)
    {
        $visitorId = FrontEnd_Helper_viewHelper::sanitize($visitorId);
        $visitor = Doctrine_Core::getTable("Visitor")->find($visitorId);
        if ($visitor) {
            $visitor->password = FrontEnd_Helper_viewHelper::sanitize(md5($password));
            $visitor->pwd = FrontEnd_Helper_viewHelper::sanitize($password);
            $visitor->save();
            return true;
        } else {
            return false;
        }
    }

    #############################################################
    ######### END REFACTRED CODE ################################
    #############################################################
    /**
     *  validate email address
     * @param string $email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ;
    }


    /**
     * function use for get the favoriteshop access according visitorId
     * from session
     * @param integer $visitorId
     * @return array $ar
     * @author mkaur
     * @version 1.0
     */
    public static function getFavorite($visitorId)
    {
        $data = Doctrine_Query::create()->select("fv.id as id,s.name as name,s.id as id,l.*")->
        from("FavoriteShop fv")->leftJoin("fv.shops s")
        ->leftJoin('s.logo l')
        ->where('fv.visitorId='. FrontEnd_Helper_viewHelper::sanitize( $visitorId))
        ->fetchArray();

        $newArray = array();
        foreach ($data as $fav){
            $newArray[$fav['id']] = $fav['name'];
        }
        return $data;
    }
    /**
     * updateVisitor
     *
     * update visitor in database
     *
     * @param array $params
     * @param boolean $isUpdatedByAdmin set true if profile is being updated from admin pannel
     * @author mkaur modifed by Surinderpal Singh
     */
    public static function updateVisitor($params, $isUpdatedByAdmin = false)
    {
        //echo "<pre>";
        //print_r($params); die;
        $toSetActive = false ;
        # set property value for profile in case update from admin pannel
        if($isUpdatedByAdmin) {
            $id =  $params['id'] ;
            $dob = $params['date_year'].'-'.$params['date_month'].'-'.$params['date_day'];

            if(isset($params['postalCode'])){
                $postalCode = $params['postalCode'];
            }
            $toSetActive = true ;
        }else {

            $id = Auth_VisitorAdapter::getIdentity()->id  ;
            $dob = $params['birthYear'].'-'.$params['birthMonth'].'-'.$params['birthDay'];

            if(isset($params['postCode'])){
                $postalCode = $params['postCode'];
            }

        }

        $visitor = Doctrine_Core::getTable("Visitor")->find($id);
        $visitor->firstName = FrontEnd_Helper_viewHelper::sanitize( $params['firstName'] );
        $visitor->lastName = FrontEnd_Helper_viewHelper::sanitize( $params['lastName'] );
        $visitor->dateOfBirth = FrontEnd_Helper_viewHelper::sanitize( $dob );


        if(! $isUpdatedByAdmin) {
            if(self::validateEmail($params['emailAddress'])) {
                $visitor->email = FrontEnd_Helper_viewHelper::sanitize( $params['emailAddress'] );
            } else {
                return false ;
            }

        }
        # check postal code
        if(isset($postalCode)){
            $visitor->postalCode = FrontEnd_Helper_viewHelper::sanitize($postalCode );
        }
        if($toSetActive) {
            $visitor->active = FrontEnd_Helper_viewHelper::sanitize($params['active'] );
        }


        if(isset($params['weekly'])){
            $visitor->weeklyNewsLetter = FrontEnd_Helper_viewHelper::sanitize(($params['weekly'] == 'on' || $params['weekly'] == 1) ? 1 : 0);
        }else{
            $visitor->weeklyNewsLetter = 0;
        }
        if(isset($params['travel'])){
            $visitor->travelNewsLetter = FrontEnd_Helper_viewHelper::sanitize($params['travel']);
        }else{

            $visitor->travelNewsLetter = 0;
        }
        if(isset($params['fashion'])){
        $visitor->fashionNewsLetter = FrontEnd_Helper_viewHelper::sanitize($params['fashion']);
        }else{
            $visitor->fashionNewsLetter = 0;
        }
        if(isset($params['code'])){
            $visitor->codeAlert = FrontEnd_Helper_viewHelper::sanitize($params['code']);
        }else{
            $visitor->codeAlert = 0;
        }

        Doctrine_Core::getTable("VisitorKeyword")->findBy('visitorId', $visitor->id)->delete();

        # check keyword in  requets array $params
        if(!empty($params['visitorKeywords']) && count($params['visitorKeywords']) > 0 ) {
                # set visitor  keywords
                foreach ($params['visitorKeywords'] as $keyword) {
                    $visitor->keywords[]->keyword = FrontEnd_Helper_viewHelper::sanitize( $keyword );
                }
        }
        if($params['gender']=='female'){
            $visitor->gender = 1;
        }

        if($params['gender']=='male'){
            $visitor->gender = 0;
        }

        $visitor->save();

        return array("ret" => $visitor->id ,
                     "status" => self::SUCCESS );
    }
    /**
     * Search top five visitors and shows in autocomplete
     * @author mkaur
     * @version 1.0
     */
    public static function searchKeyword($for,$keyword)
    {
        $keyword =  FrontEnd_Helper_viewHelper::sanitize( $keyword );

        $data = Doctrine_Query::create()->select('v.firstName as firstName')
            ->from("Visitor v")
            ->where("v.firstName LIKE ?", "$keyword%")
            //->orWhere("v.email LIKE ?", "$keyword%")
            ->orderBy("v.firstName ASC")
            ->andWhere("v.deleted=".$for)
            ->limit(5)
        ->fetchArray();
        return $data;
    }
    /**
     * Search top five visitors and shows in autocomplete
     * @author kraj
     * @version 1.0
     */
    public static function searchEmails($for,$keyword)
    {
        $keyword =  FrontEnd_Helper_viewHelper::sanitize( $keyword );

        $data = Doctrine_Query::create()->select('v.email as email')
        ->from("Visitor v")
        ->orWhere("v.email LIKE ?", "$keyword%")
        ->orderBy("v.email ASC")
        ->andWhere("v.deleted=".$for)
        ->limit(5)
        ->fetchArray();
        return $data;
    }
    /**
     * Return list of the visitors according to search text and role
     * @param array $params if 0 then returns visitors and 1 for trash visitors
     * @return array Json
     * @author mkaur
     * @version 1.0
     */
    public static function VisitorList($params)
    {
        // print_r($params);die;
        $srh = isset($params["searchtext"]) ? $params["searchtext"] : '';
        $email = isset($params["email"]) ? $params["email"] : '';
        $data = Doctrine_Query::create()
            ->select ('v.*')
            ->from ("Visitor v")
            ->leftJoin('v.visitorimage p')
            ->where("v.deleted=".$params['for']);

            if(isset($params["searchtext"]) && $params["searchtext"] != 'null' && $params["searchtext"]!='undefined'){

                $data->andWhere("v.firstName LIKE " .  "'$srh%'");
            }
            if(isset($params["email"]) && $params["email"] != 'null' &&  $params["email"]!='undefined'){

                $data->andWhere("v.email LIKE " . "'$email%'");
            }
            $data->orderBy("v.id DESC");
            //echo $data; die;
            $list = DataTable_Helper::generateDataTableResponse($data,
                        $params,array("__identifier" => "v.id, p.id",'v.id','v.firstName','v.lastName','v.email','v.active','v.weeklyNewsLetter', 'v.created_at'),
                        array(),array());

        //echo "<pre>";
        //print_r($list);
        //die("hello");
        return $list;

    }

    /**
     * Fetches one record according to id to show in editVisitor form
     * @param $id
     * @author mkaur
     */
    public static function editVisitor($id)
    {
        $data = Doctrine_Query::create()->select("v.*,fs.id,k.keyword")
        ->from('Visitor v')
        ->leftJoin("v.favoritevisitorshops fs")
        ->leftJoin("v.keywords k")
        ->where("v.id = ?" , $id)
        ->fetchOne(null , Doctrine::HYDRATE_ARRAY);
        return $data;
    }
    /**
     * check password in exist in database or not
     * @param string $passwordToBeVerified
     * @author kraj
     * @version 1.0
     */
    public function validatePassword($passwordToBeVerified)
    {
        $req = Zend_Controller_Front::getInstance()->getRequest();
        $lang  = $req->getParam('lang' , false) ;

        # set propertry to current lcoale during login in case of flipit
        if($lang) {
            $this->currentLocale = $lang ;
        }

        //echo $this->password;
        //echo $passwordToBeVerified;
        if ($this->password == $passwordToBeVerified) {

            return true;
        }
        return false;

    }

    /**
     * delete number of records of favorite shops
     * @param $params
     * @author mkaur
     */
    public static function delelteFav($params)
    {
    //print_r($params['shops']);die;
        if($params['shops']){
            for($i=0;$i<count(@$params['shops']);$i++) {
                if ($params['visitorId']) {
                    $u = Doctrine_Core::getTable("FavoriteShop")->find($params['visitorId']);
                     $del = Doctrine_Query::create()->delete()
                    ->from('FavoriteShop fv')
                    ->where("fv.shopId=".@$params['shops'][$i])
                    ->andWhere("fv.visitorId=".$params['visitorId'])
                    ->execute();
                }
            }
            return $params['shops'];
        } else {return null;}
    }

    public static function getUserDetail($visitorId)
    {
        $data = Doctrine_Query::create()->select("v.*,i.*")->
        from("Visitor v")
        ->where('v.id='.$visitorId)->leftJoin('v.visitorimage i')
        ->andWhere('v.deleted=0')
        ->fetchArray();
        return $data;
    }
    public static function getuserpwddetail($uemail)
    {
        $data = Doctrine_Query::create()->select("v.*")->
        from("Visitor v")
        ->where('v.email='."'$uemail'")
        ->fetchArray();
        return $data;
    }
    public static function updatefrontVisitor($params,$userid)
    {
        // working pending here
        $visitor = Doctrine_Core::getTable("Visitor")->find($userid);
        $visitor->firstName = $params['fname'];
        $visitor->lastName = $params['lname'];
        $visitor->username = $params['uname'];
        $visitor->gender = $params['gender'];
        $visitor->interested =implode(",",$params["intereseted"]);
        if($params['datepicker']!=''){
            $ndate=explode("-",$params['datepicker']);
            $visitor->dateOfBirth = $ndate[2].'-'.$ndate[1].'-'.$ndate[0];
        }
        $visitor->postalCode = $params['postcode'];
        if(isset($params['repwd']) && $params['repwd']!=''){
            $visitor->password = md5($params['repwd']);
        }
        if (isset($_FILES['file1'])) {


            $result = self::uploadImage('file1');


            if ($result['status'] == '200') {

                //$prevImage = $seenIn->logo->name ;
                $viewHelper = new BackEnd_Helper_viewHelper();

                $ext = $viewHelper->getImageExtension(
                        $result['fileName']);

                $seenIn->logo->ext = $ext;
                $seenIn->logo->path = $result['path'];
                $seenIn->logo->name = $result['fileName'];
            }
        }
        $visitor->save();

    }
    public static function update_profileimage($userid,$profile_img)
    {
        $visitor = Doctrine_Core::getTable("Visitor")->find($userid);
        $visitor->profile_img = FrontEnd_Helper_viewHelper::sanitize($profile_img);
        $visitor->save();

    }
    public static function deleteprev_profileimage($userid)
    {
        $data = Doctrine_Query::create()->select("v.*")->
        from("Visitor v")
        ->where('v.id='."'$userid'")
        ->fetchArray();
        if($data[0]['profile_img']!="" && $data[0]['profile_img']!="no.jpg"){
        $img_dir=$_SERVER['DOCUMENT_ROOT']."/public/images/upload/profile/".$data[0]['profile_img'];
        $thumbimg_dir=$_SERVER['DOCUMENT_ROOT']."/public/images/upload/profile/thumb/".$data[0]['profile_img'];
        @unlink($img_dir);
        @unlink($thumbimg_dir);
        }
    }
    public static function get_profileimage($userid)
    {
        $data = Doctrine_Query::create()->select("v.*")->
        from("Visitor v")
        ->where('v.id='."'$userid'")
        ->fetchArray();
        return $data[0]['profile_img'];
    }
    public static function calculate_percentage($id)
    {
        $data = Doctrine_Query::create()->select("v.*")->
        from("Visitor v")
        ->where('v.id='."'$id'")
        ->fetchArray();

        //echo "<pre>"; print_r($data); die;
        $percentage=0;
        if($data[0]["username"]!=""){
            $percentage=$percentage+10;
        }
        if($data[0]["email"]!=""){
            $percentage=$percentage+10;
        }
        if($data[0]["firstName"]!=""){
            $percentage=$percentage+5;
        }
        if($data[0]["lastName"]!=""){
            $percentage=$percentage+5;
        }
        if($data[0]["gender"]!=""){
            $percentage=$percentage+10;
        }
        if($data[0]["dateOfBirth"]!=""){
            $percentage=$percentage+10;
        }
        if($data[0]["postalCode"]!=""){
            $percentage=$percentage+10;
        }
        return $percentage;
    }
    /**
     * upload image
     * @param $_FILES[index]  $file
     */


/*---------------start front end----------------*/

/**
 * get record of favorite shop's offer
 * @return array
 * @author mkaur updated by blal
 */
 public static function getfavoriteOffer()
 {
    $userid = Auth_VisitorAdapter::getIdentity()->id;
    $date = date('Y-m-d H:i:s');
    $data = Doctrine_Query::create()
    ->select('fv.id as fvid,fv.shopId as shopId,s.refUrl,s.actualUrl,fv.visitorId as visitorId,s.name as name,s.logoid as slogoId,s.permalink as permalink,o.*,l.path,l.name,l.id,terms.content,vot.id,vot.vote')
    ->from('Offer o')
    ->leftJoin('o.shop s')
    ->leftJoin('s.favoriteshops fv')
    ->leftJoin('s.logo l')
    ->leftJoin('o.termandcondition terms')
    ->leftJoin('o.vote vot')
    ->where('fv.visitorId='. $userid)
    ->andWhere('s.deleted=0')
    ->andWhere('o.endDate > "'.$date.'"')
    ->andWhere('o.startDate <= "'.$date.'"')
    ->andWhere('o.discountType="CD"')
    ->andWhere('o.Visability!="MEM"')
    ->andWhere('o.userGenerated=0')
    ->orderBy('o.exclusiveCode DESC')
    ->addOrderBy('o.startdate DESC')
    ->fetchArray();
    //echo $data->getSqlQuery(); die;

    /*$newData = 0;
    $newData = array();
    foreach($data as $result){
        foreach($result['shop'] as $res){
            if($res['offer']!=null){
                foreach($res['offer'] as $offer){
                    $offer['shop']['id'] =  $result['shopId'];
                    $offer['shop']['name'] = $result['sname'];
                    $offer['shop']['logo'] = $res['logo'];
                    $offer['shop']['refUrl'] = $res['refUrl'];
                    $offer['shop']['actualUrl'] = $res['actualUrl'];
                    $offer['shop']['favoriteshops'] = array(array('id'=>$result['fvid'],
                            'visitorId'=>$result['visitorId'],
                            'shopId'=>$result['shopId']));
                    $offer['shop']['permalink'] = $res['perma'];
                    $newData[] = $offer;
                }
            }
        }
    }*/
    return $data;
}

/**
 * function use for get the favoriteshop access according visitorId
 * from session
 * @param integer $visitorId
 * @return array $ar
 * @author mkaur
 * @version 1.0
 */
public static function getFavoriteShops($visitorId)
{
    $data = Doctrine_Query::create()->select("fv.id as id,s.name as name,s.permaLink,s.id as id,l.*")->
    from("FavoriteShop fv")->leftJoin("fv.shops s")
    ->leftJoin('s.logo l')
    ->where('fv.visitorId='.$visitorId)
    ->fetchArray();

    return $data;
}

/**
 * Return count of total accounts created..
 * @param array $params if 0 then returns visitors and 1 for trash visitors
 * @return array Json
 * @author sunny patial
 * @version 1.0
 */
public static function Visitortotal_acc()
{
    //print_r($params);die;
    $data = Doctrine_Query::create ()
    ->select (count ('v.*'))
    ->from ("Visitor v")
    ->where('v.active_codeid=0')
    ->orderBy("v.id DESC");
    $numberofrecords=count($data);
    return $numberofrecords;

}
/*public static function getFvoffer() {
    $data = Doctrine_Query::create()
    ->select('s.id,s.name,terms.content,o.id,o.title, o.visability, o.couponcode, o.refofferurl, o.startdate, o.enddate, o.exclusivecode, o.editorpicks,o.extendedoffer,o.discount, o.authorId, o.authorName, o.shopid, o.offerlogoid, o.userGenerated, o.approved,img.id, img.path, img.name,*.fv')
    ->from('Offer o')
    ->leftJoin('o.shop s')
    ->leftJoin('s.logo img')
    ->leftJoin('fv.FavoriteShop')
    ->where('o.deleted = 0' )->andWhere('fv.visitorId=1')
    ->orderBy('o.id DESC')
    ->fetchArray();
    return $data;
}*/

    /**
     * get No of Subscribers in last 7 days for dashboard
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getAmountSubscribersLastWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        $nowDate = $date;

        $data = Doctrine_Query::create ()
            ->select("count(*) as amountsubs")
            ->from ("Visitor v")
            //->where('v.active_codeid=0')
            ->where('v.created_at BETWEEN "'.$past7Days.'" AND "'.$date.'"')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $data;
    }


    /**
     * get total No of Subscribers
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getTotalAmountSubscribers()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $data = Doctrine_Query::create ()
            ->select("count(*) as amountsubs")
            ->from ("Visitor v")
            ->where('v.active_codeid=0')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
            return $data;
    }

    /**
     * getVisitorsToSendNewsletter
     *
     * return all visitors to whom newsletters to be sent
     * @author sp singh
     */
    public function getVisitorsToSendNewsletter($recordPerPage = 1000)
    {

        return   Doctrine_Query::create()->select('v.email,v.password,v.firstName,v.lastName,k.keyword')
                ->from('Visitor v')
                ->leftJoin("v.keywords k")
                ->orderBy("k.keyword")
                ->where('status = 1')
                ->andWhere('active = 1')
                ->andWhere('weeklyNewsLetter = 1')
                ->fetchArray();
    }
}
