<?php
namespace KC\Repository;

class User extends \KC\Entity\User
{

    ##########################################################
    ########### REFACTORED CODE ##############################
    ##########################################################
    public static function getAllUsersDetails($websiteName)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select(
            'u.firstName, u.lastName, u.slug, u.mainText, u.showInAboutListing,
            u.popularKortingscode, pi.name, pi.path'
        )
            ->from('\KC\Entity\User', 'u')
            ->leftJoin("u.profileimage", "pi")
            ->leftJoin('u.website', 'w')
            ->setParameter(1, '0')
            ->where('u.deleted = ?1')
            ->setParameter(2, '1')
            ->andWhere("u.showInAboutListing = ?2")
            ->setParameter(3, $websiteName)
            ->andWhere('w.url = ?3');
        $usersData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $usersData;
    }

    public static function getUserIdBySlugName($slug)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u.id')
            ->from('\KC\Entity\User', 'u')
            ->setParameter(1, $slug)
            ->where('u.slug = ?1');
        $userDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }

    public static function getUserProfileDetails($userId, $websiteName)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u, w.id, pi.name, pi.path')
            //->addSelect('DATEDIFF(NOW(), u.created_at) as sinceDays')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin("u.profileimage", "pi")
            ->leftJoin('u.website', 'w')
            ->setParameter(1, $userId)
            ->where('u.id = ?1')
            ->setParameter(2, '1')
            ->andWhere("u.showInAboutListing = ?2")
            ->setParameter(3, '0')
            ->andWhere('u.deleted = ?3')
            ->setParameter(4, $websiteName)
            ->andWhere('w.url = ?4');
        $userDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }

    public static function getUserFavouriteStores($userId)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('a,s.id as sid,s.name as name,s.permalink, img')
            ->from('\KC\Entity\Adminfavoriteshop', 'a')
            ->leftJoin("a.shops", "s")
            ->leftJoin('s.logo', 'img')
            ->setParameter(1, $userId)
            ->where('a.userId = ?1')
            ->setParameter(2, '0')
            ->andWhere("s.deleted = ?2")
            ->setParameter(3, '1')
            ->andWhere('s.status = ?1');
        $userFavouriteStores = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userFavouriteStores;
    }

    public static function getUserDetails($userId)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select(
            'u.id, u.firstName,u.lastName,u.addtosearch,
            u.mainText, u.editorText, u.slug,
            u.google, pi.name, pi.path'
        )
            ->from('\KC\Entity\User', 'u')
            ->leftJoin("u.profileimage", "pi")
            ->setParameter(1, $userId)
            ->where('u.id = ?1');
        $userDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }

    ##########################################################
    ########### END REFACTORED CODE ##########################
    ##########################################################

    //wwconst USER_SET_STATUS = 0;

    const INVALID_NEW_PASSWORD_STATUS = "-2";
    const INVALID_OLD_PASSWORD_STATUS = "-1";
    const SUCCESS = "200";

    public static function getWebsite($userId, $roleId)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        switch ($roleId) {
            case '1':
                $Q = $queryBuilder->select('w')->from('\KC\Entity\Website', 'w');
                break;
            case '2':
            case '3':
            case '4':
            case '5':
                 $Q= $queryBuilder->select('u')
                    ->from('\KC\Entity\User', 'u')
                    ->leftJoin("u.website", "refW")
                    ->setParameter(1, $userId)
                    ->where('u.id = ?1');
                break;
            default:
                break;
        }
        $ar = @$Q->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        //restructure manual array for user website
        $newArray = array();
        if ($roleId=='1') {
            $websites = $ar ;
        } else {
            $websites = $ar[0]['website'] ;
        }
        $websites = BackEnd_Helper_viewHelper::msort($websites, 'name', 'kortingscode.nl');
        foreach ($websites as $website) {
            $newArray[] = array('id' => $website['id'], 'name' => $website['name']);
        }
        //var_dump($newArray);
        return $newArray;
    }

    public function addUser($params, $imageName)
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $addto = isset($params['addtosearch']) ? $params['addtosearch'] : false;
        if ($addto == 'on') {
            $addtosearch = 1;
        } else {
            $addtosearch = 0;
        }
        $ext = \BackEnd_Helper_viewHelper::getImageExtension($params['imageName']);
        $this->firstName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['firstName']);
        $this->email = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['email']);
        $this->lastName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['lastName']);
        $this->countryLocale = $params['locale'];
        $this->mainText =BackEnd_Helper_viewHelper::stripSlashesFromString(
            isset($params['maintext']) ? $params['maintext'] : ''
        );
        $this->currentLogIn = date('Y-m-d');
        $this->lastLogIn = date('Y-m-d');
        if ($this->isValidPassword($params['password'])) {
            self::setPassword($params['password']);
            $entityManagerUser->persist($this);
            $entityManagerUser->flush();
        } else {
            return  array(
                'error' => true,
                'message' => 'New password must contain a number, capital letter and a special character'
            );
        }

        $this->roleId = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['role']);
        $this->showInAboutListing = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['nameStatus']);
        $this->addtosearch =$addtosearch;
        $this->google = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['google']);
        $this->twitter = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['twitter']);
        $this->pinterest = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pintrest']);
        $this->likes = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['likes']);
        $this->dislike = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['dislike']);
        $this->editorText = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['editortext']);
        $this->popularKortingscode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['popularKortingscode']);

        $this->createdBy = isset(Auth_StaffAdapter::getIdentity()->id) ? Auth_StaffAdapter::getIdentity()->id : '';
        $fname = str_replace(' ', '-', $params['firstName']);
        $lname = str_replace(' ', '-', $params['lastName']);
        $this->slug = BackEnd_Helper_viewHelper::stripSlashesFromString(strtolower($fname ."-". $lname));
        //  if(isset($params['imageName']))
        $pattern = '/^[0-9]{10}_(.+)/i' ;
        preg_match($pattern, $imageName, $matches);
        if (@$matches[1]) {
            $ext =  \BackEnd_Helper_viewHelper::getImageExtension($imageName);
            $pImage  = new KC\Entity\ProfileImage();
            $pImage->ext = $ext;
            $pImage->path ='images/upload/';
            $pImage->name = BackEnd_Helper_viewHelper::stripSlashesFromString($imageName);
            $entityManagerUser->persist($pImage);
            $entityManagerUser->flush();
            $this->profileImageId =  $pImage->getId();

        }
        //save user website access
        if (isset($params['websites'])) {
            foreach ($params['websites'] as $web) {
                $this->refUserWebsite[]->websiteId = $web ;
            }
        }

        $entityManagerUser->persist($this);
        $entityManagerUser->flush();
        //save interesting category in database
        if (isset($params['selectedCategoryies'])) {
            $entityManagerLocale  =\Zend_Registry::get('emLocale');
            foreach ($params['selectedCategoryies'] as $categories) {
                $cat = new KC\Entity\Interestingcategory();
                $cat->categoryId  =$categories;
                $cat->userId = $this->getId();
                $entityManagerLocale->persist($cat);
                $entityManagerLocale->flush();
            }
        }
        //end code of enteresting category in database
        //save favorite store in database
        if (!empty($params['fevoriteStore'])) {
            $splitStore  =explode(",", $params['fevoriteStore']);
            foreach ($splitStore as $str) {
                $store = new  KC\Entity\Adminfavoriteshop();
                $store->shopId  = $str;
                $store->userId = $this->getId();
                $entityManagerUser->persist($store);
                $entityManagerUser->flush();
            }
        }
        //call cache function
        $key = 'user_'.$this->getId().'_details';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
        return $this->getId();
    }

    public static function checkDuplicateUser($email)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u')
            ->from('KC\Entity\User', 'u')
            ->setParameter(1, $email)
            ->where('u.email = ?1')
            ->setParameter(2, '0')
            ->andWhere('u.deleted = ?2');
        $cnt = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return count($cnt);
    }

    public function getPermissions()
    {
        if (intval($this->id) > 0) {
             $perm = $genralPermission =  array();
             $perm['roles'] =    $this->role->toArray();

              unset($perm['roles']['created_at']);
              unset($perm['roles']['updated_at']);


             $perm['rights'] =   $this->role->rights->toArray();

            for ($i=0; $i < count($perm['rights']); $i++) {
                unset($perm['rights'][$i]['created_at']);
                unset($perm['rights'][$i]['updated_at']);
                unset($perm['rights'][$i]['id']);
                unset($perm['rights'][$i]['roleId']);
                $perm['rights'][$perm['rights'][$i]['name']]= $perm['rights'][$i];
                unset($perm['rights'][$i]);
            }

            $perm['webaccess']= $this->refUserWebsite->toArray();
            for ($i=0; $i < count($perm['webaccess']); $i++) {
                unset($perm['webaccess'][$i]['id']);
                unset($perm['webaccess'][$i]['userId']);
                unset($perm['webaccess'][$i]['created_at']);
                unset($perm['webaccess'][$i]['updated_at']);
                $q = Doctrine_Query::create()
                     ->select('w.name')
                     ->from('Website w')->where("id = ".$perm['webaccess'][$i]['websiteId']."")
                     ->orderBy("w.name")->fetchArray();
                $perm['webaccess'][$i]['websitename'] = $q['0']['name'];
            }
             # rearange websites based on website name and keep kortingscode at same place
             $data = $perm['webaccess'];
             $data = BackEnd_Helper_viewHelper::msort($data, array('websitename'), "kortingscode.nl");
             $perm['webaccess'] = $data;
             return $perm;
        }
        return null ;
    }

    public function update($params, $imageName = '', $normalUser = '')
    {
        $addto = BackEnd_Helper_viewHelper::stripSlashesFromString(
            isset($params['addtosearch'])
            ? $params['addtosearch']
            : ''
        );
        if ($addto == 'on') {
            $addtosearch = 1;
        } else {
            $addtosearch = 0;
        }
        $this->firstName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['firstName']);
        $this->lastName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['lastName']);
        $this->firstName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['firstName']);
        $this->lastName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['lastName']);
        $this->roleId =  $params['role'];
        $this->showInAboutListing = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['nameStatus']);
        $this->addtosearch =$addtosearch;
        $this->google = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['google']);
        $this->twitter = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['twitter']);
        $this->pinterest = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pintrest']);
        $this->likes = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['likes']);
        $this->dislike = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['dislike']);
        $this->mainText =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maintext']);
        $this->editorText = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['editortext']);
        $this->popularKortingscode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['popularKortingscode']);
        $this->countryLocale = isset($params['locale']) ? $params['locale'] : '';

        $fname = str_replace(' ', '-', $params['firstName']);
        $lname = str_replace(' ', '-', $params['lastName']);
        $this->slug = \BackEnd_Helper_viewHelper::stripSlashesFromString(strtolower($fname ."-". $lname));

        if (strlen($imageName) > 0) {
            $pattern = '/^[0-9]{10}_(.+)/i' ;
            preg_match($pattern, $imageName, $matches);
            if (@$matches[1]) {
                $ext =  BackEnd_Helper_viewHelper::getImageExtension($imageName);
                if (intval($params['pImageId']) > 0) {
                    $pImage = Doctrine_Core::getTable('ProfileImage')->find($params['pImageId']);
                } else {
                    $pImage  = new ProfileImage();
                }
                $pImage->ext = $ext;
                $pImage->path ='images/upload/';
                $pImage->name = BackEnd_Helper_viewHelper::stripSlashesFromString($imageName);
                $pImage->save();
                $this->profileImageId =  $pImage->id;
            }
        }
        // check user want to update password or not based upon old password
        if (isset($params['confirmNewPassword']) && !empty($params['confirmNewPassword'])) {
            # apply validation on password like it should strong enough and not same as previous one
            if (! $this->isPasswordDifferent($params['confirmNewPassword'])) {
                return  array('error' => true, 'message' => 'New password can\'t be same as previous password');
            }
            if ($this->isValidPassword($params['confirmNewPassword'])) {
                self::setPassword($params['confirmNewPassword']) ;
                $this->save();
            } else {
                return  array(
                  'error' => true,
                  'message' => 'New password must contain a number, capital letter and a special character'
                );
            }
        }
        // check logged in user or not
        // if yes then deleted reference websites otherwise skip
        if ($normalUser=='') {
            if ($this->id != Auth_StaffAdapter::getIdentity()->id) {
                if (isset($params['role'])) {
                    $this->roleId = $params['role'];
                }
                $this->createdBy = Auth_StaffAdapter::getIdentity()->id;
                $this->refUserWebsite->delete();
                if (isset($params['websites'])) {
                    foreach ($params['websites'] as $web) {
                        $this->refUserWebsite[]->websiteId = $web ;
                    }
                }
            }
        }
        $this->save();
        $fullName = $params['firstName'] . " " . $params['lastName'];
        // update session if profile is being updated
        if ($this->id == Auth_StaffAdapter::getIdentity()->id) {
            new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this);
        }

        if ($params['pImageName']!== @$params['prevImageName']) {
            if (@$matches[1]) {
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, @$params['prevImageName'], $matches);
                if (@$matches[1]) {
                    $uploadPath = "images/upload/";
                    $user_path = ROOT_PATH . $uploadPath;
                    $img =  @$params['prevImageName'];
                    if ($img) {
                        @unlink($user_path . $img);
                        @unlink($user_path . "thum_" . $img);
                        @unlink($user_path . "thum_large" . $img);
                    }
                }
            }
        }

        if (isset($params['selectedCategoryies'])) {
            $connUser = BackEnd_Helper_viewHelper::addConnection();
            BackEnd_Helper_viewHelper::closeConnection($connUser);
            $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
            Doctrine_Query::create()->delete()->from('Interestingcategory')->where("userId=".$this->id)->execute();
            foreach ($params['selectedCategoryies'] as $categories) {
                $cat = new Interestingcategory();
                $cat->categoryId  =$categories;
                $cat->userId = $this->id;
                $cat->save();
            }
            BackEnd_Helper_viewHelper::closeConnection($connSite);
            $connUser = BackEnd_Helper_viewHelper::addConnection();
        }
        //end code of enteresting category in database
        //save favorite store in database
        if (!empty($params['fevoriteStore'])) {
            $connUser = BackEnd_Helper_viewHelper::addConnection();
            BackEnd_Helper_viewHelper::closeConnection($connUser);
            $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
            Doctrine_Query::create()->delete()->from('Adminfavoriteshop')->where("userId=".$this->id)->execute();
            $splitStore  =explode(",", $params['fevoriteStore']);
            foreach ($splitStore as $str) {
                $store = new Adminfavoriteshop();
                $store->shopId  = $str;
                $store->userId = $this->id;
                $store->save();
            }
            BackEnd_Helper_viewHelper::closeConnection($connSite);
            $connUser = BackEnd_Helper_viewHelper::addConnection();
        }

        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
        //die("test");
        //$alluserkey ="all_". "users". $params['firstName']. $params['lastName'] ."_list";
        //FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($alluserkey);

        $alluserIdkey ="user_".$this->id ."_data";
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($alluserIdkey);

        $key = 'user_'.$this->id.'_details';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        $interestkey ="all_". "interesting".$this->id."_list";
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($interestkey);

        $favouriteShopkey ="user_". "favouriteShop".$this->id ."_data";
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($favouriteShopkey);
        self::updateInDatabase($this->id, $fullName, 0);//change name of the author etc
        return array(
          "ret" => $this->id ,
          "status" => self::SUCCESS,
          "message" => "Record has been updated successfully"
        );
    }
    /**
     * Change username or editor in all other databases
     * @param integer $id
     * @param string $fullName
     * @return boolean
     * @author kraj
     * @version 1.0
     */
    public function updateInDatabase($id, $fullName, $flag)
    {
        $application = new Zend_Application(APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');

        $connections = $application->getOption('doctrine');

        foreach ( $connections as $key => $connection ) {

            // check database is being must be site
            if ($key != 'imbull' && isset($connection ['dsn'])) {

                # create a run tiem connection to all site to update editor data
                $connObj = BackEnd_Helper_DatabaseManager::addConnection($key);
                $conn = $connObj['adapter'];


                if($flag==0){

                    $o = Doctrine_Query::create($conn)->update('Offer')->set('authorName',"'$fullName'")
                        ->where('authorId=' . $id);
                    $o->execute();

                    $p = Doctrine_Query::create($conn)->update('Page')->set('contentManagerName', "'$fullName'")
                        ->where('contentManagerId=' . $id);
                    $p->execute();


                    $a = Doctrine_Query::create($conn)->update('Articles')->set('authorname', "'$fullName'")
                        ->where('authorid=' . $id);
                    $a->execute();

                    $s = Doctrine_Query::create($conn)->update('Shop')->set('accountManagerName', "'$fullName'")
                        ->where('accoutManagerId=' . $id);
                    $s->execute();


                    $s1 = Doctrine_Query::create($conn)->update('Shop')->set('contentManagerName', "'$fullName'")
                        ->where('contentManagerId=' . $id);
                    $s1->execute();

                } else if($flag==1){


                    //update offer
                    $offers = Doctrine_Query::create()->select('id')->from('Offer')->where('authorId=' . $id)->fetchArray();

                    # check if there is atleast one offer exists in the array
                    if(count($offers) > 0){

                        $ids = array();

                        if(!empty($offers)):
                            foreach($offers as $arr):
                                        $ids[] = $arr['id'];
                                    endforeach;
                        endif;

                        $o = Doctrine_Query::create($conn)->update('Offer')->set('authorName',"'$fullName'")
                                ->set('authorName',"'$fullName'")
                                ->set('authorId',0)
                                ->whereIn('id', $ids)
                                ->execute();

                    }



                    //update page
                    $page = Doctrine_Query::create()->select('id')->from('Page')->where('contentManagerId=' . $id)->fetchArray();

                    # check if there is atleast one page exists in the array
                    if(count($page) > 0){

                        $ids = array();

                        if(!empty($page)):
                            foreach($page as $arr):
                                        $ids[] = $arr['id'];
                                    endforeach;
                        endif;

                        $p = Doctrine_Query::create()->update('Page')
                                ->set('contentManagerName', "'$fullName'")
                                ->set('contentManagerId', 0)
                                ->whereIn('id', $ids);
                                $p->execute();

                    }

                    //update articles
                    $art = Doctrine_Query::create()->select('id')->from('Articles')->where('authorid=' . $id)->fetchArray();

                    # check if there is atleast one page exists in the array
                    if(count($art) > 0){

                        $ids = array();

                        if(!empty($art)):
                            foreach($art as $arr):
                                        $ids[] = $arr['id'];
                                    endforeach;
                        endif;

                        $a = Doctrine_Query::create()->update('Articles')->set('authorname', "'$fullName'")
                                ->set('authorid', 0)
                                ->whereIn('id', $ids)
                                ->execute();

                    }

                    //update shops
                    $shops = Doctrine_Query::create($conn)
                        ->select('id,name')->from('Shop')
                        ->where('contentManagerId=' . $id)->fetchArray();

                    # check if there is atleast one shop exists in the array
                    if(count($shops) > 0){

                        $ids = array();

                        if(!empty($shops)):
                            foreach($shops as $arr):
                                        $ids[] = $arr['id'];
                                    endforeach;
                        endif;

                        $s = Doctrine_Query::create($conn)
                            ->update('Shop')
                            ->set('contentManagerName', "'$fullName'")
                            ->set('contentManagerId', 0)
                            ->whereIn('id', $ids)
                            ->execute();
                    }
                }
                $connObj = BackEnd_Helper_DatabaseManager::closeConnection($connObj['adapter']);
            }
        }
    }

   /**
    * set user session related SS0
    * @param integer $uId
    * @param string $token
    * @author kkumar
    * @version 1.0
    */
    public function setUserSession($uId,$token)
    {
        $q = Doctrine_Query::create()
        ->select('id')->from('UserSession')->orderBy('id desc')->limit(1)->fetchArray();
        $id = 1;
        if(count($q)>0){
        $id = $q[0]['id'] + 1 ;
        }
        $usersession = new UserSession();
        $usersession->id = $id;
        $usersession->userId= $uId;
        $usersession->sessionId= $token;
        $usersession->save();
  }
    /**
     * get roles of the users from database
     * @author kkumar
     * @version 1.0
     */
    public static function getRoles()
    {
        //return $data =  Doctrine::getTable("Role")->findAll()->toArray();
        return $data =  Doctrine_Query::create()->from("Role")->addWhere('id >='.Auth_StaffAdapter::getIdentity()->roleId)->fetchArray();

    }


    public static function getManagersLists($site_name)
    {
        $conn2=BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        /* Initialize action controller here */

        /*$managers = array();
        $managers['accountmanagers'] =  Doctrine_Query::create()
            ->select('u.id,u.firstName as fname,u.lastName as lname,u.roleId as role')
            ->from("User u")
            ->leftJoin("u.refUserWebsite rfu")
            ->leftJoin("rfu.Website w")
            ->where('u.deleted=0')
            ->andWhere('u.roleId=3')
            ->andWhere("w.name ='".$site_name."'")
            ->orderBy('fname')
            ->fetchArray(); */

        $managers['editors'] = Doctrine_Query::create()
            ->select('u.id,u.firstName as fname,u.lastName as lname,u.roleId as role')
            ->from("User u")
            ->leftJoin("u.refUserWebsite rfu")
            ->leftJoin("rfu.Website w")
            ->where('u.deleted=0')
            ->andWhere('u.roleId=4')
            ->andWhere("w.name ='".$site_name."'")
            ->orderBy('fname')
            ->fetchArray();

        BackEnd_Helper_viewHelper::closeConnection($conn2);
        return $managers;

    }
    /**
    * function return five search record according to search creteria
    * @param string $param
    * @return array $ar
    * @author kraj
    * @version 1.0
    */
  public static function getTopFiveForAutoComp($for,$param)
  {
        $data = Doctrine_Query::create()
        ->select('u.firstName as firstName')
        ->from("User u")
        ->where('u.deleted='.$for)
        ->addWhere('u.roleId >='.Auth_StaffAdapter::getIdentity()->roleId)
        ->addWhere("u.id <>".Auth_StaffAdapter::getIdentity()->id)
        ->andWhere("u.firstName LIKE ?", "$param%")
        ->orderBy("u.firstName ASC")->limit(5)->fetchArray();
        $ar =  array();
        if(sizeof($data) > 0){
        foreach ($data as $d) {
            $ar[] =  $d['firstName'];

        }

        }else {

            $ar[] =  'No Record Found';
        }
    return $ar;
  }
  /**
   * return list of the user according to search text and role
   * @param array $params
   * @return array Json
   * @author kraj
   * @version 1.0
   */
  public static function getUserList($params)
  {
    $role = $params['role'];
    $srh = $params['searchtext'];

    $data = Doctrine_Query::create()
        ->select('u.*,r.name as role,p.path as path,p.name as ppname')
        ->addSelect('(SELECT COUNT(us.createdby) FROM User us WHERE us.createdby = u.id)  as entries')
        ->from("User u")->leftJoin('u.profileimage p')
        ->where('u.deleted=0')
        ->addWhere('u.roleId >='.Auth_StaffAdapter::getIdentity()->roleId);
    if((intval($role)) > 0) {
        //add role search
        $data->addWhere('u.roleId='.$role);
    }
    if($srh!='undefined'){

        //add search for user name
        $data->andWhere("u.firstName LIKE ?", "$srh%");
    }
    $data->addWhere("u.id <>".Auth_StaffAdapter::getIdentity()->id)
    ->leftJoin('u.role r')->orderBy("u.id DESC");

    return Zend_Json::encode(
            DataTable_Helper::generateDataTableResponse($data,
                    $params,
                    array("__identifier" => "u.id, r.id, p.id",'u.id','u.firstName','u.email','role'),
                    array(),
                    array(
                    )));

  }
  /**
   * return trashed user list of the user according to search text and role
   * @param array $params
   * @return array Json
   * @author kraj
   * @version 1.0
   */
  public static function getTrashUserList($params)
  {
    $role = $params['role'];
    $srh = $params['searchtext'];

    $data = Doctrine_Query::create()
    ->select('u.*,r.name as role,p.path as path,p.name as ppname')
    ->from("User u")->leftJoin('u.profileimage p')
    ->where('u.deleted=1')
    ->addWhere('u.roleId >='.Auth_StaffAdapter::getIdentity()->roleId);
    if((intval($role)) > 0) {
        //add role search
        $data->addWhere('u.roleId='.$role);
    }
    if($srh!='undefined'){

        //add search for user name
        $data->andWhere("u.firstName LIKE ?", "$srh%");
    }
    $data->addWhere("u.id <>".Auth_StaffAdapter::getIdentity()->id)
    ->leftJoin('u.role r')->orderBy("u.id DESC");
    return Zend_Json::encode(
            DataTable_Helper::generateDataTableResponse($data,
                    $params,
                    array("__identifier" => "u.id, r.id, p.id",'u.id','u.firstName','u.email','role'),
                    array(),
                    array(
                    )));
 }
 function getPageAutor($site_name)
 {
    $data = Doctrine_Query::create()
    ->select('u.id,u.firstName as fname,u.lastName as lname')
    ->from("User u")
    ->leftJoin("u.refUserWebsite rfu")
    ->leftJoin("rfu.Website w")
    ->where('u.deleted=0')
    ->andWhere("w.url ='".$site_name."'")
    ->orderBy('fname')
    ->fetchArray();
    return  $data;

  }
  /**
   * add store in favorite list
   * @author kraj
   * @version 1.0
   * @return integer $flag
   */
  public static function addStoreInList($name)
  {
    $connUser = BackEnd_Helper_viewHelper::addConnection();
    BackEnd_Helper_viewHelper::closeConnection($connUser);
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();//connection generate with second database

    //find shop by name
    $Shop = Doctrine_query::create()->from('Shop')
    ->where('name=' . "'$name'")->limit(1)->fetchArray();
    $flag = '0';

    if (sizeof($Shop) > 0) {

        //check store exist or not
        $pc = Doctrine_Core::getTable('Adminfavoriteshop')
        ->findBy('shopId', $Shop[0]['id']);
        if (sizeof($pc) > 0) {

            $flag = '2';

        } else {

            $flag = '1';
            //add new store if not exist in datbase
            $pc = new Adminfavoriteshop();
            $pc->shopId = $Shop[0]['id'];

            BackEnd_Helper_viewHelper::closeConnection($connSite);
            $connUser = BackEnd_Helper_viewHelper::addConnection();

                $pc->userId = Auth_StaffAdapter::getIdentity()->id;//get current user(admin) id

            BackEnd_Helper_viewHelper::closeConnection($connUser);
            $connSite = BackEnd_Helper_viewHelper::addConnectionSite();

                $pc->save();

            BackEnd_Helper_viewHelper::closeConnection($connSite);
            $connUser = BackEnd_Helper_viewHelper::addConnection();
            $flag = $pc->toArray();
        }

    }
    //call cache function
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
    FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
    return $flag;

  }
   /**
   * Search top ten shops and shows in autocomplete
   * use in  normal list
   * @param string $keyword
   * @param $data
   * @author kraj
   * @version 1.0
   */
  public static function searchTopTenStore($keyword,$selctedshop)
  {
    $SP = $selctedshop!='' ? $selctedshop: 0;
    $data = Doctrine_Query::create()
        ->select('s.name as name,s.id as id')
        ->from("Shop s")
        ->where('s.deleted= 0')
        ->andWhere("s.name LIKE ?", "$keyword%")
        ->andWhere("s.id NOT IN ($SP)")
        ->andWhere('s.status=1')
        ->orderBy("s.name ASC")
        ->limit(10)->fetchArray();

    return $data;
  }
  /**
   * check shop in database base on name
   * use in  normal list
   * @param string $name
   * @param integer $flag
   * @author kraj
   * @version 1.0
   */
  public static function checkStoreExistOrNot($name)
  {
    $connUser = BackEnd_Helper_viewHelper::addConnection();
    BackEnd_Helper_viewHelper::closeConnection($connUser);
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();

        //$Shop = Doctrine_query::create()->from('Shop')
        //->where('name=' . "'$name'")->limit(1)->fetchArray();
    $Shop = Doctrine_Core::getTable('Shop')->find($name);
    BackEnd_Helper_viewHelper::closeConnection($connSite);
    $connUser = BackEnd_Helper_viewHelper::addConnection();
    $flag = 0;
    if ($Shop) {

         $flag = 1;
    }

    return $flag;
  }
  /**
   * get interesting category related currect user(admin)
   * use in  normal list
   * @param integer $id
   * @param array $userFevoriteCat
   * @author kraj
   * @version 1.0
   */
  public static function getUserInterestingCat($id)
  {
    $userFevoriteCat = Doctrine_Core::getTable('Interestingcategory')->findBy('userId', $id)->toArray();
    return $userFevoriteCat;
  }
  /**
   * get user detail
   * use in  normal list
   * @param integer $id
   * @return array userdetail
   * @author kkumar
   * @version 1.0
   */
  public static function getUserDetail($uId)
  {
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    BackEnd_Helper_viewHelper::closeConnection($connSite);
    $connUser = BackEnd_Helper_viewHelper::addConnection();
     $Userdata = Doctrine_Query::create()->select("Count(*) as Max,o.authorId,o.authorName")
    ->from('Offer o')
    ->groupBy("o.authorName")
    ->orderBy('Max DESC')
    ->fetchArray(null , Doctrine::HYDRATE_ARRAY);

     $data = Doctrine_Query::create()->select()
                                     ->from('User u')
                                     ->leftJoin("u.website w")
                                     ->leftJoin("u.profileimage pi")
                                     ->where("u.id = ?" , $Userdata[0]['authorId'])
                                     ->fetchArray(null , Doctrine::HYDRATE_ARRAY);

    BackEnd_Helper_viewHelper::closeConnection($connUser);
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    return $data;
  }


  public static function getFamousUserDetail($eId)
  {

      $data = Doctrine_Query::create()->select()
                                      ->from('User u')
                                      ->leftJoin("u.website w")
                                      ->leftJoin("u.profileimage pi")
                                      ->where("u.id = ?" , $eId)
                                      ->fetchArray(null , Doctrine::HYDRATE_ARRAY);

      return $data;
  }

  /**
   * get user Interesting category on ID basis
   * use in  normal list
   * @param integer $id
   * @return array userdetail
   * @author Raman
   * @version 1.0
   */
  public static function getUserIntcategory($uId)
  {
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    BackEnd_Helper_viewHelper::closeConnection($connSite);
    $connUser = BackEnd_Helper_viewHelper::addConnection();
    $data = Doctrine_Query::create()->select("c.name, c.permalink, ic.id")
    ->from('Interestingcategory ic')
    ->leftJoin("ic.category c")
    ->where("ic.userId = ?" , $uId)
    ->andWhere('c.deleted =0')
    ->fetchArray();
    BackEnd_Helper_viewHelper::closeConnection($connUser);
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();

    return $data;
  }
  //******font-end function ******//
  /**
   * get user detail
   * use in  normal list
   * @param integer $id
   * @return array profileImage
   * @author kraj
   * @version 1.0
   */


  /**
   * returnEditorUrl
   * returns the editor url and editor permalink
   * @author Surinderpal Singh
   * @param integer $id editor id
   * @return array
   * @version 1.0
   */
  public static function returnEditorUrl($id)
  {
        # check for valid user id
        if(intval($id) > 0) {
            $connUser = BackEnd_Helper_viewHelper::addConnection();
            $data = Doctrine_Query::create()->select("u.slug")
                    ->from('User u')
                    ->leftJoin("u.profileimage pi")
                    ->where("u.id = ?" , $id)
                    ->fetchOne(null , Doctrine::HYDRATE_ARRAY);

            BackEnd_Helper_viewHelper::closeConnection($connUser);

            $editor = FrontEnd_Helper_viewHelper::__link("link_redactie"). "/" ;
            $url = HTTP_PATH. $editor . $data['slug'];
            return array('url' => $url , 'permalink' => $editor . $data['slug'] );
        }

        return false;
  }

  /**
   * get users permalinks
   * use in  normal list
   * @return array userdetail
   * @author Raman
   * @version 1.0
   */
  public static function getAllUserPermalinks($site_name)
  {
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    BackEnd_Helper_viewHelper::closeConnection($connSite);
    $connUser = BackEnd_Helper_viewHelper::addConnection();
    $data = Doctrine_Query::create()->select("u.slug")
    ->from("User u")
    ->leftJoin("u.refUserWebsite rfu")
    ->leftJoin("rfu.Website w")
    ->where('u.deleted=0')
    ->andWhere("w.url ='".$site_name."'")
    ->andWhere("u.showInAboutListing = 1")
    ->fetchArray();

    BackEnd_Helper_viewHelper::closeConnection($connUser);
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    return $data;
  }

  /**
   * get user Name
   * use in  normal list
   * @param integer $id
   * @return array user name
   * @author Raman
   * @version 1.0
   */
  public static function getUserName($uId)
  {
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    BackEnd_Helper_viewHelper::closeConnection($connSite);
    $connUser = BackEnd_Helper_viewHelper::addConnection();

    $data = Doctrine_Query::create()->select('u.firstName, u.lastname')
    ->from('User u')
    ->where("u.id = ".$uId )
    ->fetchArray(null , Doctrine::HYDRATE_ARRAY);

    BackEnd_Helper_viewHelper::closeConnection($connUser);
    $connSite = BackEnd_Helper_viewHelper::addConnectionSite();
    if(!empty($data)){
        $name = $data[0]['firstName'].' '.$data[0]['lastName'];
    } else{
        $name = '';
    }
    return $name;
  }
  /**
   * get all user
   * use in  normal list
   * @author kraj
   * @version 1.0
   */
  public static function getAllUser()
  {
    $data =  Doctrine_Query::create()->select('id,firstName,lastName,deleted')->from('User')->fetchArray();
    return $data;
  }


  /**
   * updatePassword
   * update user password when it is expired
   *
   * @param array $params request params
   */
  public function updatePassword($params = null)
  {


        if($this->validatePassword($params['curPassword'])) {

            // check user want to update password or not based upon old password
            if(isset($params['newPassword']) && isset($params['confirmPassword'])){

                if($params['newPassword'] !== $params['confirmPassword']) {
                    return  'New password and confrim don\'t matched';
                }


                if(! $this->isPasswordDifferent($params['confirmPassword'])) {
                    return  'New password can\'t be same as previous password';
                }


                if($this->isValidPassword($params['confirmPassword'])) {
                            // encrypt new passsword
                    self::setPassword($params['confirmPassword']) ;
                    $this->save();

                    # reeturn false to ensure password changed
                    return false;
                }else {

                    return  'New password must contain a number, capital letter and a special character';
                }


            } else  {
                return "Please enter new password an scurrent password" ;
            }




        } else {
            return  'Please enter valid current password' ;
        }
  }

  /**
   * isValidPassword
   *
   * to make sure that nnew password must contain a capital leter,saml letter and a special character
   * @param string $password new password
   */
  public function isValidPassword($password)
  {

    $rules = array(
            'no_whitespace' => '/^\S*$/',
            'match_upper'   => '/[A-Z]/',
            'match_lower'   => '/[a-z]/',
            'match_number'  => '/\d/',
            'match_special' => '/[\W_]/',
            'length_abv_8'  => '/\S{8,}/'
    );

    $valid = true;
    foreach($rules as $rule) {
        $valid = $valid && preg_match($rule, $password);
        if(!$valid) break;
    }

    return (bool) $valid;


  }

    public function truncateTables()
    {
        $databaseConnection = Doctrine_Manager::getInstance()->getConnection('doctrine')->getDbh();
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 0;');
        $databaseConnection->query('TRUNCATE TABLE user');
        $databaseConnection->query('TRUNCATE TABLE role');
        $databaseConnection->query('TRUNCATE TABLE rights');
        $databaseConnection->query('SET FOREIGN_KEY_CHECKS = 1;');
        unset($databaseConnection);
        return true;
    }
}
