<?php
namespace KC\Repository;

class Visitor extends \KC\Entity\Visitor
{
    const SUCCESS = "200";
    public $currentLocale = null;
    #############################################################
    ######### REFACTRED CODE ####################################
    #############################################################
    public static function checkDuplicateUser($email, $visitorId = null)
    {

        $emailAddress = \FrontEnd_Helper_viewHelper::sanitize($email);
        $visitorId = \FrontEnd_Helper_viewHelper::sanitize($visitorId);
        if ($visitorId!=null) {
            $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('v')
                ->from('\KC\Entity\Visitor', 'v')
                ->where('v.id ='. $visitorId);
            $visitorInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('v')
                ->from('KC\Entity\Visitor', 'v')
                ->where('v.email ='. $queryBuilder->expr()->literal($emailAddress));
            $visitorInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return empty($visitorInformation) ? 0 : 1;
    }

    public static function getFavoriteShopsForUser($visitorId, $shopId)
    {
        $favouriteShopsStatus = false;
        if ($shopId!=0) {
            $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select("fv.id")
                ->from("\KC\Entity\FavoriteShop", "fv")
                ->where('fv.visitor='.$visitorId)
                ->andWhere('fv.shop='.$shopId);
            $favoriteShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!empty($favoriteShops)) {
                $favouriteShopsStatus = true;
            }
        } else if ($visitorId!=0) {
            $favouriteShopsStatus = true;
        }
        return $favouriteShopsStatus;
    }

    public function updateLoginTime($visitorId)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $visitor = $entityManagerLocale->find("\KC\Entity\Visitor", $visitorId);

        if ($visitor->currentLogIn=='0000-00-00 00:00:00') {
            $visitor->currentLogIn = new \DateTime('now');
        }
        $visitor->lastLogIn = $visitor->currentLogIn;
        $visitor->currentLogIn = new \DateTime('now');
        $visitor->active = 1;
        $visitor->active_codeid = '';
        $entityManagerLocale->persist($visitor);
        $entityManagerLocale->flush();
    }

    public static function addVisitor($visitorInformation, $profileUpdate = '')
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $visitorId = \Auth_VisitorAdapter::getIdentity()->id;
            $visitor = \Zend_Registry::get('emLocale')->find("\KC\Entity\Visitor", $visitorId);
            $visitor->weeklyNewsLetter = $visitorInformation['weeklyNewsLetter'];
            $visitor->codeAlert = $visitorInformation['codealert'];
        } else {
            $visitor = new \KC\Entity\Visitor();
            $visitor->weeklyNewsLetter = '1';
            $visitor->codeAlert = '1';
            $visitor->travelNewsLetter = '0';
            $visitor->fashionNewsLetter =  '0';
            $visitor->currentLogIn = new \DateTime('now');
            $visitor->lastLogIn = new \DateTime('now');
            $visitor->status = '1';
            $visitor->deleted = '0';
            $visitor->created_at = new \DateTime('now');
            $visitor->updated_at = new \DateTime('now');
            $visitor->active_codeid =  'test';
            $visitor->active =  '1';
            $visitor->changepasswordrequest =  '1';
            $visitor->email = \FrontEnd_Helper_viewHelper::sanitize($visitorInformation['emailAddress']);
            if (\KC\Repository\Signupmaxaccount::getemailConfirmationStatus()) {
                $visitor->active = false;
            } else {
                $visitor->active = true;
            }
            $shopIdNameSpace = new \Zend_Session_Namespace('shopId');
            isset($shopIdNameSpace->shopId) ? $shopIdNameSpace->shopId = '' : '';
            $emailAddressSpace = new \Zend_Session_Namespace('emailAddressSignup');
            isset($emailAddressSpace->emailAddressSignup) ? $emailAddressSpace->emailAddressSignup = '' : '';
        }
        $visitor->firstName = \FrontEnd_Helper_viewHelper::sanitize($visitorInformation['firstName']);
        $visitor->lastName = \FrontEnd_Helper_viewHelper::sanitize($visitorInformation['lastName']);
        $visitor->gender = \FrontEnd_Helper_viewHelper::sanitize($visitorInformation['gender'] == 'M' ? 0 : 1);
        if ($profileUpdate != '') {
            $datetime = new \DateTime(
                $visitorInformation['dateOfBirthYear']
                .'-'
                .$visitorInformation['dateOfBirthMonth']
                .'-'
                .$visitorInformation['dateOfBirthDay']
            );
            $visitor->dateOfBirth = $datetime;
            $visitor->updated_at = new \DateTime('now');
            $visitor->postalCode =
                \FrontEnd_Helper_viewHelper::sanitize(
                    isset($visitorInformation['postCode']) ? $visitorInformation['postCode'] : ''
                );
        }
        if (!empty($visitorInformation['password'])) {
            $visitor->password = \FrontEnd_Helper_viewHelper::sanitize(md5($visitorInformation['password']));
        }
        $entityManagerLocale->persist($visitor);
        $entityManagerLocale->flush();
        return $visitor->id;
    }

    public static function updatePasswordRequest($visitorId, $changePasswordStatus)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->update('\KC\Entity\Visitor', 'v')
            ->set('v.changepasswordrequest', $changePasswordStatus)
            ->where('v.id=' . \FrontEnd_Helper_viewHelper::sanitize($visitorId));
        $query->getQuery()->execute();
    }

    public static function updateVisitorPassword($visitorId, $password)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $visitor  = $entityManagerLocale->find('\KC\Entity\Visitor', \FrontEnd_Helper_viewHelper::sanitize($visitorId));
        if ($visitor) {
            $visitor->password = \FrontEnd_Helper_viewHelper::sanitize(md5($password));
            $entityManagerLocale->persist($visitor);
            $entityManagerLocale->flush();
            return true;
        } else {
            return false;
        }
    }

    public static function getVisitorDetails($visitorId)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        return $entityManagerLocale->find('\KC\Entity\Visitor', $visitorId);
    }

    public static function getUserDetails($visitorId)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v, i')
            ->from('\KC\Entity\Visitor', 'v')
            ->leftJoin('v.visitorimage', 'i')
            ->where('v.id ='. $visitorId)
            ->andWhere('v.deleted = 0');
        $userDetails = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }

    public static function getUserFirstName($visitorId)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('v.firstName')
            ->from('KC\Entity\Visitor', 'v')
            ->where('v.id='.$visitorId);
        $userDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }

    public static function getVisitorDetailsByEmail($visitorEmail)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\KC\Entity\Visitor', 'v');
        if (!ctype_digit($visitorEmail)) {
            $query = $query->where("v.email=".$queryBuilder->expr()->literal($visitorEmail));
        } else {
            $query = $query->where("v.id=".$queryBuilder->expr()->literal($visitorEmail));
        }
        $visitorDetails = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $visitorDetails;
    }
    
    public static function updateVisitorStatus($visitorId)
    {
        $visitorConrmationStatus = false;
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $visitor = $entityManagerLocale->find('\KC\Entity\Visitor', $visitorId);
        if ($visitor->active==false) {
            $visitor->active  = true;

            $entityManagerLocale->persist($visitor);
            $entityManagerLocale->flush();

            $visitorConrmationStatus = true;
        }
        return $visitorConrmationStatus;
    }
    
    public static function setVisitorLoggedIn($visitorId)
    {
        $visitorLoginStatus = false;
        $visitorDetails = self::getUserDetails($visitorId);
        $dataAdapter = new \Auth_VisitorAdapter($visitorDetails["email"], $visitorDetails["password"]);
        $visitorZendAuth = \Zend_Auth::getInstance();
        $visitorZendAuth->setStorage(new \Zend_Auth_Storage_Session('front_login'));
        $visitorZendAuth->authenticate($dataAdapter);
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $visitorId = \Auth_VisitorAdapter::getIdentity()->id;
            self::updateLoginTime($visitorId);
            setcookie('kc_unique_user_id', $visitorId, time() + (Session_Helper_Session::getSessionTimeout()), '/');
            $visitorLoginStatus = true;
        }
        return $visitorLoginStatus;
    }

    public static function updateVisitorActiveStatus($email)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query= $queryBuilder->update('\KC\Entity\Visitor', 'v')
            ->set('active', 0)
            ->where("email = '".$email."'");
        $query->getQuery()->execute();
        return true;
    }

    public static function getFavoriteShops($visitorId)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select(
            "s.name as name,s.permaLink,s.id as id,
            l.path as imgpath, l.name as imgname"
        )
        ->addSelect(
            "(SELECT COUNT(active.id) FROM \KC\Entity\Offer active WHERE
            (active.shopOffers = s.id AND active.endDate >= '$currentDate' AND active.deleted=0)) as activeCount"
        )
        ->from("\KC\Entity\FavoriteShop", "fv")
        ->leftJoin("fv.shop", "s")
        ->leftJoin('s.logo', 'l')
        ->where('fv.visitor='.$visitorId);
        $favouriteShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $favouriteShops;
    }
    
    public static function getFavoriteShopsOffers($limit = 40)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select(
            'fv,s,o,l,v'
        )
        ->addSelect(
            "(SELECT COUNT(active) FROM \KC\Entity\Offer active WHERE
            (active.shopOffers = s.id AND active.endDate >= '$currentDate' AND active.deleted=0)) as activeCount"
        )
        ->from('\KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.favoriteshops', 'fv')
        ->leftJoin('fv.visitor', 'v')
        ->leftJoin('s.logo', 'l')
        ->where('fv.visitor='. \Auth_VisitorAdapter::getIdentity()->id)
        ->andWhere('s.deleted=0')
        ->andWhere('o.deleted=0')
        ->andWhere($queryBuilder->expr()->gt('o.endDate', $queryBuilder->expr()->literal($currentDate)))
        ->andWhere($queryBuilder->expr()->lte('o.startDate', $queryBuilder->expr()->literal($currentDate)))
        ->andWhere($queryBuilder->expr()->eq('o.discountType', $queryBuilder->expr()->literal('CD')))
        ->andWhere($queryBuilder->expr()->neq('o.Visability', $queryBuilder->expr()->literal('MEM')))
        ->andWhere('o.userGenerated=0')
        ->setMaxResults($limit);
        $favouriteShopsOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $traversedOffers = self::favouriteShopsOffersTraverse($favouriteShopsOffers);
        return $traversedOffers;
    }

    public static function favouriteShopsOffersTraverse($favouriteShopsOffers)
    {
        $traversedOffers = array();
        foreach ($favouriteShopsOffers as $key => $favouriteShopsOffer) {
            if (isset($favouriteShopsOffer[0])) {
                $traversedOffers[$key] = $favouriteShopsOffer[0];
                $traversedOffers[$key]['activeCount'] = $favouriteShopsOffer['activeCount'];
            }
        }
        return $traversedOffers;
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

    public static function getFavorite($visitorId)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("fv, s, l")
            ->from("\KC\Entity\FavoriteShop", "fv")
            ->leftJoin("fv.shop", "s")
            ->leftJoin('s.logo', 'l')
            ->where('fv.visitor='. \FrontEnd_Helper_viewHelper::sanitize($visitorId));
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $newArray = array();
        foreach ($data as $fav) {
            $newArray[$fav['id']] = $fav['name'];
        }
        return $data;
    }
   
    public static function updateVisitor($params, $isUpdatedByAdmin = false)
    {
        //echo "<pre>";
        //print_r($params); die;
        $toSetActive = false ;
        # set property value for profile in case update from admin pannel
        if ($isUpdatedByAdmin) {
            $id =  $params['id'] ;
            $dob = $params['date_year'].'-'.$params['date_month'].'-'.$params['date_day'];
            if (isset($params['postalCode'])) {
                $postalCode = $params['postalCode'];
            }
            $toSetActive = true ;
        } else {
            $id = \Auth_VisitorAdapter::getIdentity()->id;
            $dob = $params['birthYear'].'-'.$params['birthMonth'].'-'.$params['birthDay'];
            if (isset($params['postCode'])) {
                $postalCode = $params['postCode'];
            }

        }

        $dateOfBirth = new \DateTime($dob);
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $visitor = $entityManagerLocale->find('\KC\Entity\Visitor', $id);

        $visitor->firstName = \FrontEnd_Helper_viewHelper::sanitize($params['firstName']);
        $visitor->lastName = \FrontEnd_Helper_viewHelper::sanitize($params['lastName']);
        $visitor->dateOfBirth = $dateOfBirth;
        $visitor->deleted = $visitor->deleted;
        $visitor->created_at = $visitor->created_at;
        $visitor->updated_at = new \DateTime('now');

        if ($isUpdatedByAdmin) {
            if (self::validateEmail($params['email'])) {
                $visitor->email = \FrontEnd_Helper_viewHelper::sanitize($params['email']);
            } else {
                return false ;
            }

        }
        # check postal code
        if (isset($postalCode)) {
            $visitor->postalCode = \FrontEnd_Helper_viewHelper::sanitize($postalCode);
        }
        if ($toSetActive) {
            $visitor->active = \FrontEnd_Helper_viewHelper::sanitize($params['active']);
        }

        if (isset($params['weekly'])) {
            $visitor->weeklyNewsLetter = \FrontEnd_Helper_viewHelper::sanitize(($params['weekly'] == 'on' || $params['weekly'] == 1) ? 1 : 0);
        } else {
            $visitor->weeklyNewsLetter = 0;
        }

        if (isset($params['travel']) && $params['travel'] != '') {
            $visitor->travelNewsLetter = \FrontEnd_Helper_viewHelper::sanitize($params['travel']);
        } else {
            $visitor->travelNewsLetter = 0;
        }

        if (isset($params['fashion']) && $params['fashion'] != '') {
            $visitor->fashionNewsLetter = \FrontEnd_Helper_viewHelper::sanitize($params['fashion']);
        } else {
            $visitor->fashionNewsLetter = 0;
        }

        if (isset($params['code']) && $params['code'] != '') {
            $visitor->codeAlert = \FrontEnd_Helper_viewHelper::sanitize($params['code']);
        } else {
            $visitor->codeAlert = 0;
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('KC\Entity\VisitorKeyword', 'kw')
            ->where("kw.visitor=" .$params['id'])
            ->getQuery()->execute();

        # check keyword in  requets array $params
        if (!empty($params['visitorKeywords']) && count($params['visitorKeywords']) > 0) {
            # set visitor  keywords
            foreach ($params['visitorKeywords'] as $keyword) {
                $keywordObj = new \KC\Entity\VisitorKeyword();
                $keywordObj->keyword = \FrontEnd_Helper_viewHelper::sanitize($keyword);
                $keywordObj->visitor = $entityManagerLocale->find('\KC\Entity\Visitor', $params['id']);
                $entityManagerLocale->persist($keywordObj);
                $entityManagerLocale->flush();
            }
        }

        if (isset($params['gender']) && $params['gender']=='1') {
            $visitor->gender = 1;
        }

        if (isset($params['gender']) && $params['gender']=='0') {
            $visitor->gender = 0;
        }

        if (
            (isset($params['newPassword']) && $params['newPassword'] != '') &&
            (isset($params['confirmNewPassword']) && $params['confirmNewPassword'] != '')
        ) {
            if ($params['newPassword'] == $params['confirmNewPassword']) {
                $visitor->password = \FrontEnd_Helper_viewHelper::sanitize(md5($params['newPassword']));
            }
        }

        $entityManagerLocale->persist($visitor);
        $entityManagerLocale->flush();

        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('visitor_'.\Auth_VisitorAdapter::getIdentity()->id.'_details');
        return array(
            "ret" => $visitor->id ,
            "status" => self::SUCCESS
        );

    }
    
    public static function searchKeyword($for, $keyword)
    {
        $keyword = \FrontEnd_Helper_viewHelper::sanitize($keyword);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v.firstName')
            ->from('\KC\Entity\Visitor', 'v')
            ->where($queryBuilder->expr()->like('v.firstName', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere('v.deleted ='. $for)
            ->orderBy("v.firstName", "ASC")
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
   
    public static function searchEmails($for, $keyword)
    {
        $keyword =  \FrontEnd_Helper_viewHelper::sanitize($keyword);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v.email')
            ->from('\KC\Entity\Visitor', 'v')
            ->where($queryBuilder->expr()->like('v.email', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere('v.deleted ='. $for)
            ->orderBy("v.email", "ASC")
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
    
    public static function VisitorList($params)
    {
        // print_r($params);die;
        $srh = isset($params["searchtext"]) ? $params["searchtext"] : '';
        $email = isset($params["email"]) ? $params["email"] : '';

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $qb = $queryBuilder
            ->from('KC\Entity\Visitor', 'v')
            ->leftJoin('v.visitorimage', 'p')
            ->where("v.deleted=".$params['for']);

        if (isset($params["searchtext"]) && $params["searchtext"] != 'null' && $params["searchtext"] != 'undefined') {

            $qb->andWhere($queryBuilder->expr()->like('v.firstName', $queryBuilder->expr()->literal($srh.'%')));
        }
        if (isset($params["email"]) && $params["email"] != 'null' &&  $params["email"] != 'undefined') {

            $qb->andWhere($queryBuilder->expr()->like('v.email', $queryBuilder->expr()->literal($email.'%')));
        }

        //$qb->orderBy("v.id", "DESC");
       
        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array('id, firstName','lastName','email','active','weeklyNewsLetter', 'created_at')
        );

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('number', 'v.id')
            ->add('text', 'v.firstName')
            ->add('text', 'v.lastName')
            ->add('text', 'v.email')
            ->add('text', 'v.active')
            ->add('text', 'v.weeklyNewsLetter')
            ->add('number', 'v.created_at');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }

    
    public static function editVisitor($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v, fs, fvs, k')
            ->from('\KC\Entity\Visitor', 'v')
            ->leftJoin("v.favoritevisitorshops", "fs")
            ->leftJoin("fs.shop", "fvs")
            ->leftJoin("v.visitorKeyword", "k")
            ->where('v.id ='. $id);
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function delelteFav($params)
    {
        if ($params['shops']) {
            for ($i=0; $i < count(@$params['shops']); $i++) {
                if ($params['visitorId']) {
                    $u = Doctrine_Core::getTable("FavoriteShop")->find($params['visitorId']);
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    $query = $queryBuilder->delete('KC\Entity\FavoriteShop', 'fv')
                        ->where('fv.visitor='.$params['visitorId'])
                        ->andWhere('fv.shop='.@$params['shops'][$i])
                        ->getQuery()->execute();
                }
            }
            return $params['shops'];
        } else {
            return null;
        }
    }

    public static function updatefrontVisitor($params, $userid)
    {
       
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $visitor = $entityManagerLocale->find('\KC\Entity\Visitor', $userid);

        $visitor->firstName = $params['fname'];
        $visitor->lastName = $params['lname'];
        $visitor->username = $params['uname'];
        $visitor->gender = $params['gender'];
        $visitor->interested = implode(",", $params["intereseted"]);

        $visitor->deleted = $visitor->deleted;
        $visitor->created_at = $visitor->updated_at;
        $visitor->updated_at = new \DateTime('now');

        if ($params['datepicker']!='') {
            $ndate = explode("-", $params['datepicker']);
            $visitor->dateOfBirth = $ndate[2].'-'.$ndate[1].'-'.$ndate[0];
        }

        $visitor->postalCode = $params['postcode'];
        if (isset($params['repwd']) && $params['repwd']!='') {
            $visitor->password = md5($params['repwd']);
        }

        if (isset($_FILES['file1'])) {
            $result = self::uploadImage('file1');
            if ($result['status'] == '200') {
                $viewHelper = new \BackEnd_Helper_viewHelper();
                $ext = $viewHelper->getImageExtension($result['fileName']);
                $seenIn->logo->ext = $ext;
                $seenIn->logo->path = $result['path'];
                $seenIn->logo->name = $result['fileName'];
            }
        }
        $entityManagerLocale->persist($visitor);
        $entityManagerLocale->flush();
    }

    public static function update_profileimage($userid, $profile_img)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->update('\KC\Entity\Visitor', 'v')
            ->set('v.profile_img', $changePasswordStatus)
            ->where('v.id=' . $userid);
        $query->getQuery()->execute();
    }

    public static function deleteprev_profileimage($userid)
    {
       
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.id ='. $userid);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
       
        if ($data[0]['profile_img']!="" && $data[0]['profile_img']!="no.jpg") {
            $img_dir=$_SERVER['DOCUMENT_ROOT']."/public/images/upload/profile/".$data[0]['profile_img'];
            $thumbimg_dir=$_SERVER['DOCUMENT_ROOT']."/public/images/upload/profile/thumb/".$data[0]['profile_img'];
            @unlink($img_dir);
            @unlink($thumbimg_dir);
        }
    }

    public static function get_profileimage($userid)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.id ='. $userid);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data[0]['profile_img'];
    }

    public static function calculate_percentage($id)
    {
        
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.id ='. $id);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $percentage=0;
        if ($data[0]["username"]!="") {
            $percentage = $percentage+10;
        }
        if ($data[0]["email"]!="") {
            $percentage = $percentage+10;
        }
        if ($data[0]["firstName"]!="") {
            $percentage = $percentage+5;
        }
        if ($data[0]["lastName"]!="") {
            $percentage=$percentage+5;
        }
        if ($data[0]["gender"] != "") {
            $percentage=$percentage+10;
        }
        if ($data[0]["dateOfBirth"] != "") {
            $percentage = $percentage+10;
        }
        if ($data[0]["postalCode"] != "") {
            $percentage = $percentage+10;
        }
        return $percentage;
    }
    
    public static function Visitortotal_acc()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.active_codeid = 0')
            ->orderBy("v.id", "DESC");
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $numberofrecords = count($data);
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

   
    public static function getAmountSubscribersLastWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        $nowDate = $date;

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('count(v.id) as amountsubs')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.created_at BETWEEN "'.$past7Days.'" AND "'.$date.'"');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $data;
    }


    public static function getTotalAmountSubscribers()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('count(v.id) as amountsubs')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.active_codeid = 0');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public function getVisitorsToSendNewsletter($visitorId = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v.email,v.password,v.firstName,v.lastName,k.keyword')
            ->from('\KC\Entity\Visitor', 'v')
            ->leftJoin("v.visitorKeyword k")
            ->where('v.status = 1')
            ->andWhere('v.active = 1');
        if ($visitorId != '') {
            $query = $query->andWhere('v.id IN('.$visitorId.')');
        } else {
            $query = $query->andWhere('weeklyNewsLetter = 1');
        }
        $visitorsToSendNewsletter = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $visitorsToSendNewsletter;
    }

    public static function addCodeAlertTimeStampForVisitor($visitorIds)
    {
        if (!empty($visitorIds)) {
            $visitorIds = explode(',', $visitorIds);
            foreach ($visitorIds as $visitorIdValue) {
                $entityManagerLocale = \Zend_Registry::get('emLocale');
                $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                $query = $queryBuilder->update('\KC\Entity\Visitor', 'v')
                    ->set('v.code_alert_send_date', "'".  date('Y-m-d 00:00:00') ."'")
                    ->where('v.id='. \FrontEnd_Helper_viewHelper::sanitize($visitorIdValue));
                $query->getQuery()->execute();
            }
        }
        return true;
    }

    public static function getVisitorCodeAlertSendDate($visitorId = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v.code_alert_send_date')
            ->from('\KC\Entity\Visitor', 'v')
            ->where('v.id='. \FrontEnd_Helper_viewHelper::sanitize($visitorId));
        $codeAlertSendDate = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($codeAlertSendDate) ?  $codeAlertSendDate[0]['code_alert_send_date'] : 0;
    }
}
