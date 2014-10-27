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
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
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
            ->addSelect('DATE_DIFF(NOW(), u.created_at) as sinceDays')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin("u.profileimage", "pi")
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
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
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('a,s.id as sid,s.name as name,s.permaLink as permalink, img')
            ->from('KC\Entity\Adminfavoriteshp', 'a')
            ->leftJoin("a.shops", "s")
            ->leftJoin('s.logo', 'img')
            ->setParameter(1, $userId)
            ->where('a.userId = ?1')
            ->setParameter(2, '0')
            ->andWhere("s.deleted = ?2")
            ->setParameter(3, '1')
            ->andWhere('s.status = ?3');
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
                 $Q= $queryBuilder->select('u, refW, w')
                    ->from('KC\Entity\User', 'u')
                    ->leftJoin('u.refUserWebsite', 'refW')
                    ->leftJoin('refW.refUsersWebsite', 'w')
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
        $websites = \BackEnd_Helper_viewHelper::msort($websites, 'name', 'kortingscode.nl');
        foreach ($websites as $website) {
            $newArray[] = array('id' => $website['id'], 'name' => $website['name']);
        }
        //var_dump($newArray);
        return $newArray;
    }

    public function addUser($params, $imageName)
    {
        $addUser = new \KC\Entity\User();
        $entityManagerUser  = \Zend_Registry::get('emUser');

        $addtosearch = '0';
        $addto = isset($params['addtosearch']) ? $params['addtosearch'] : 0;
        if ($addto == 'on') {
            $addtosearch = 1;
        } else {
            $addtosearch = 0;
        }

        $ext = \BackEnd_Helper_viewHelper::getImageExtension($params['imageName']);
        $addUser->firstName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['firstName']);
        $addUser->email = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['email']);
        $addUser->lastName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['lastName']);
        $addUser->countryLocale = isset($params['locale']) ? $params['locale'] : '';
        
        $addUser->mainText = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            isset($params['maintext']) ? $params['maintext'] : ''
        );
        $addUser->currentLogIn = new \DateTime('now');
        $addUser->lastLogIn = new \DateTime('now');
        $addUser->deleted = '0';
        

        if ($this->isValidPassword($params['password'])) {
            $addUser = self::setPassword($addUser, $params['password']);
        } else {
            return  array(
                'error' => true,
                'message' => 'New password must contain a number, capital letter and a special character'
            );
        }

        $addUser->users = $entityManagerUser->find('KC\Entity\Role', \BackEnd_Helper_viewHelper::stripSlashesFromString($params['role']));
        $addUser->showInAboutListing = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['nameStatus']);
        $addUser->addtosearch = $addtosearch;
        $addUser->google = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['google']);
        $addUser->twitter = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['twitter']);
        $addUser->pinterest = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pintrest']);
        $addUser->likes = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['likes']);
        $addUser->dislike = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['dislike']);
        $addUser->editorText = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['editortext']);
        $addUser->popularKortingscode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['popularKortingscode']);

        $addUser->createdBy = isset(\Auth_StaffAdapter::getIdentity()->id) ? \Auth_StaffAdapter::getIdentity()->id : 0;
        $fname = str_replace(' ', '-', $params['firstName']);
        $lname = str_replace(' ', '-', $params['lastName']);
        $addUser->slug = \BackEnd_Helper_viewHelper::stripSlashesFromString(strtolower($fname ."-". $lname));

        //  if(isset($params['imageName']))
        $pattern = '/^[0-9]{10}_(.+)/i' ;
        preg_match($pattern, $imageName, $matches);
        if (@$matches[1]) {
            $ext =  \BackEnd_Helper_viewHelper::getImageExtension($imageName);
            $pImage  = new \KC\Entity\ProfileImage();
            $pImage->ext = $ext;
            $pImage->created_at = new \DateTime('now');
            $pImage->deleted_at = new \DateTime('now');
            $pImage->deleted = '0';
            $pImage->path ='images/upload/';
            $pImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($imageName);
            $entityManagerUser->persist($pImage);
            $entityManagerUser->flush();
            $addUser->profileImageId =  $entityManagerUser->find('KC\Entity\ProfileImage', $pImage->getId());
        }

        $entityManagerUser->persist($addUser);
        $entityManagerUser->flush();

        //save user website access
        if (isset($params['websites'])) {
            foreach ($params['websites'] as $web) {
                $website = new \KC\Entity\refUserWebsite();
                $website->created_at = new \DateTime('now');
                $website->updated_at = new \DateTime('now');
                $website->refUsersWebsite = $entityManagerUser->find('KC\Entity\Website', $web);
                $website->websiteUsers = $entityManagerUser->find('KC\Entity\User', $addUser->getId());
                $entityManagerUser->persist($website);
                $entityManagerUser->flush();
            }
        }

        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        if (isset($params['selectedCategoryies'])) {
            foreach ($params['selectedCategoryies'] as $categories) {
                $cat = new \KC\Entity\Interestingcategory();
                $cat->category  = $entityManagerLocale->find('KC\Entity\Category', $categories);
                $cat->userId = $addUser->getId();
                $entityManagerLocale->persist($cat);
                $entityManagerLocale->flush();
            }
        }
        //end code of enteresting category in database
        //save favorite store in database
        if (!empty($params['fevoriteStore'])) {
            $splitStore  =explode(",", $params['fevoriteStore']);
            foreach ($splitStore as $str) {
                $store = new  \KC\Entity\Adminfavoriteshp();
                $store->shops  = $entityManagerLocale->find('KC\Entity\Shop', $str);
                $store->userId = $addUser->getId();
                $entityManagerLocale->persist($store);
                $entityManagerLocale->flush();
            }
        }
        //call cache function
        $key = 'user_'.$addUser->getId().'_details';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
        return $addUser->getId();
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
    
    public function update($params, $imageName = '', $normalUser = '')
    {
        
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $entityManagerLocale  =\Zend_Registry::get('emLocale');

        $entityManagerUser  = \Zend_Registry::get('emUser');
        $repo = $entityManagerUser->getRepository('KC\Entity\User');
        $updateUser = $repo->find($params['id']);

        $addtosearch = 0;
        $addto = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            isset($params['addtosearch'])
            ? $params['addtosearch']
            : ''
        );
        if ($addto == 'on') {
            $addtosearch = 1;
        } else {
            $addtosearch = 0;
        }

        $updateUser->firstName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['firstName']);
        $updateUser->lastName = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['lastName']);
        $updateUser->users =  $entityManagerUser->find('KC\Entity\Role', $params['role']);
        $updateUser->showInAboutListing = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['nameStatus']);
        $updateUser->addtosearch =$addtosearch;
        $updateUser->google = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['google']);
        $updateUser->twitter = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['twitter']);
        $updateUser->pinterest = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['pintrest']);
        $updateUser->likes = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['likes']);
        $updateUser->dislike = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['dislike']);
        $updateUser->mainText =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maintext']);
        $updateUser->editorText = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['editortext']);
        $updateUser->popularKortingscode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['popularKortingscode']);
        $updateUser->countryLocale = isset($params['locale']) ? $params['locale'] : '';

        $fname = str_replace(' ', '-', $params['firstName']);
        $lname = str_replace(' ', '-', $params['lastName']);
        $updateUser->slug = \BackEnd_Helper_viewHelper::stripSlashesFromString(strtolower($fname ."-". $lname));

        if (strlen($imageName) > 0) {
            $pattern = '/^[0-9]{10}_(.+)/i' ;
            preg_match($pattern, $imageName, $matches);
            if (@$matches[1]) {
                $ext =  \BackEnd_Helper_viewHelper::getImageExtension($imageName);
                if (intval($params['pImageId']) > 0) {
                    $pImage = $entityManagerUser->find('KC\Entity\ProfileImage', $params['pImageId']);
                } else {
                    $pImage  = new \KC\Entity\ProfileImage();
                    $pImage->created_at = new \DateTime('now');
                    $pImage->updated_at = new \DateTime('now');
                    $pImage->deleted = '0';
                }
                $pImage->ext = $ext;
                $pImage->path ='images/upload/';
                $pImage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($imageName);
                $entityManagerUser->persist($pImage);
                $entityManagerUser->flush();
                $updateUser->profileimage =  $entityManagerUser->find('KC\Entity\ProfileImage', $pImage->getId());
            }
        }
        // check user want to update password or not based upon old password
        if (isset($params['confirmNewPassword']) && !empty($params['confirmNewPassword'])) {
            # apply validation on password like it should strong enough and not same as previous one
            if (! $updateUser->isPasswordDifferent($params['confirmNewPassword'])) {
                return  array('error' => true, 'message' => 'New password can\'t be same as previous password');
            }
            if ($updateUser->isValidPassword($params['confirmNewPassword'])) {
                $updateUser = self::setPassword($updateUser, $params['confirmNewPassword']);
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

            if ($params['id'] != \Auth_StaffAdapter::getIdentity()->id) {

                if (isset($params['role'])) {
                    $updateUser->users =  $entityManagerUser->find('KC\Entity\Role', $params['role']);
                }

                $updateUser->createdBy = \Auth_StaffAdapter::getIdentity()->id;
               
                
                $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\refUserWebsite', 'rf')
                    ->where("rf.websiteUsers=" . $params['id'])
                    ->getQuery()->execute();

                if (isset($params['websites'])) {
                    foreach ($params['websites'] as $web) {
                        $website = new \KC\Entity\refUserWebsite();
                        $website->created_at = new \DateTime('now');
                        $website->updated_at = new \DateTime('now');
                        $website->refUsersWebsite = $entityManagerUser->find('KC\Entity\Website', $web);
                        $website->websiteUsers = $entityManagerUser->find('KC\Entity\User', $params['id']);
                        $entityManagerUser->persist($website);
                        $entityManagerUser->flush();
                    }
                }
            }
        }

        $entityManagerUser->persist($updateUser);
        $entityManagerUser->flush();

        $fullName = $params['firstName'] . " " . $params['lastName'];
        // update session if profile is being updated
        if ($updateUser->getId() == \Auth_StaffAdapter::getIdentity()->id) {
            new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $updateUser);
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
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Interestingcategory', 'i')
                ->where("i.userId=" . $updateUser->getId())
                ->getQuery()->execute();

            foreach ($params['selectedCategoryies'] as $categories) {
                $cat = new \KC\Entity\Interestingcategory();
                $cat->category  = $entityManagerLocale->find('KC\Entity\Category', $categories);
                $cat->userId = $updateUser->getId();
                $entityManagerLocale->persist($cat);
                $entityManagerLocale->flush();
            }
        }
        //end code of enteresting category in database
        //save favorite store in database
        if (!empty($params['fevoriteStore'])) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Adminfavoriteshp', 'i')
                ->where("i.userId=" . $updateUser->getId())
                ->getQuery()->execute();

            $splitStore = explode(",", $params['fevoriteStore']);
            foreach ($splitStore as $str) {
                $store = new  \KC\Entity\Adminfavoriteshp();
                $store->shops  = $entityManagerLocale->find('KC\Entity\Shop', $str);
                $store->userId = $updateUser->getId();
                $entityManagerLocale->persist($store);
                $entityManagerLocale->flush();
            }
        }

        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
        //die("test");
        //$alluserkey ="all_". "users". $params['firstName']. $params['lastName'] ."_list";
        //FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($alluserkey);

        $alluserIdkey ="user_". $updateUser->getId() ."_data";
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($alluserIdkey);

        $key = 'user_'. $updateUser->getId().'_details';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        $interestkey ="all_". "interesting". $updateUser->getId()."_list";
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($interestkey);

        $favouriteShopkey ="user_". "favouriteShop". $updateUser->getId() ."_data";
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($favouriteShopkey);
        //self::updateInDatabase($updateUser->getId(), $fullName, 0);//change name of the author etc
        return array(
          "ret" =>  $updateUser->getId(),
          "status" => self::SUCCESS,
          "message" => "Record has been updated successfully"
        );
    }
    // not migrated now
    public function updateInDatabase($id, $fullName, $flag)
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $application = new \Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        $connections = $application->getOption('doctrine');
        foreach ($connections as $key => $connection) {

            // check database is being must be site
            if ($key != 'imbull' && isset($connection ['dsn'])) {

                # create a run tiem connection to all site to update editor data
                $connObj = \BackEnd_Helper_DatabaseManager::addConnection($key);
                $conn = $connObj['adapter'];


                if ($flag==0) {

                    $repo = $entityManagerLocale->getRepository('KC\Entity\Offer');
                    $offer = $repo->findBy(array('authorId' =>  $id));
                    $offer->authorName = "'$fullName'";
                    $offer->remove($offer);
                    $entityManagerLocale->flush();

                    $repo = $entityManagerLocale->getRepository('KC\Entity\Page');
                    $page = $repo->findBy(array('contentManagerId' =>  $id));
                    $page->contentManagerName = "'$fullName'";
                    $page->remove($page);
                    $entityManagerLocale->flush();

                    $repo = $entityManagerLocale->getRepository('KC\Entity\Articles');
                    $articles = $repo->findBy(array('authorid' =>  $id));
                    $articles->authorname = "'$fullName'";
                    $articles->remove($articles);
                    $entityManagerLocale->flush();

                    $repo = $entityManagerLocale->getRepository('KC\Entity\Shop');
                    $shop = $repo->findBy(array('accoutManagerId' =>  $id));
                    $shop->accountManagerName = "'$fullName'";
                    $shop->remove($shop);
                    $entityManagerLocale->flush();


                    $repo = $entityManagerLocale->getRepository('KC\Entity\Shop');
                    $shop = $repo->findBy(array('contentManagerId' =>  $id));
                    $shop->contentManagerName = "'$fullName'";
                    $shop->remove($shop);
                    $entityManagerLocale->flush();

                } else if ($flag==1) {


                    //update offer
                    $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                    $query = $queryBuilder->select('o.id')
                        ->from('\KC\Entity\Offer', 'o')
                        ->where('o.authorId=' . $id);
                    $offers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    # check if there is atleast one offer exists in the array
                    if (count($offers) > 0) {

                        $ids = array();

                        if(!empty($offers)):
                            foreach($offers as $arr):
                                $ids[] = $arr['id'];
                            endforeach;
                        endif;
                        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                        $query= $queryBuilder->update('\KC\Entity\Offer')
                            ->set('authorName', "'$fullName'")
                            ->set('authorName', "'$fullName'")
                            ->set('authorId', 0)
                            ->where($entityManagerUser->expr()->in('id', $ids));
                        $query->getQuery()->execute();
                    }

                    //update page
                    $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                    $query = $queryBuilder->select('p.id')
                        ->from('\KC\Entity\Page', 'p')
                        ->where('p.contentManagerId=' . $id);
                    $page = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    # check if there is atleast one page exists in the array
                    if (count($page) > 0) {
                        $ids = array();
                        if(!empty($page)):
                            foreach($page as $arr):
                                $ids[] = $arr['id'];
                            endforeach;
                        endif;

                        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                        $query= $queryBuilder->update('\KC\Entity\Page')
                            ->set('contentManagerName', "'$fullName'")
                            ->set('contentManagerId', 0)
                            ->where($entityManagerUser->expr()->in('id', $ids));
                        $query->getQuery()->execute();
                    }

                    //update articles
                    $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                    $query = $queryBuilder->select('a.id')
                        ->from('\KC\Entity\Articles', 'a')
                        ->where('a.authorid=' . $id);
                    $art = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                    # check if there is atleast one page exists in the array
                    if (count($art) > 0) {
                        $ids = array();
                        if(!empty($art)):
                            foreach($art as $arr):
                                $ids[] = $arr['id'];
                            endforeach;
                        endif;

                        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                        $query= $queryBuilder->update('\KC\Entity\Articles')
                            ->set('authorname', "'$fullName'")
                            ->set('authorid', 0)
                            ->where($entityManagerUser->expr()->in('id', $ids));
                        $query->getQuery()->execute();
                    }

                    //update shops
                    $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                    $query = $queryBuilder->select('a.id, a.name')
                        ->from('\KC\Entity\Shop', 'a')
                        ->where('a.contentManagerId=' . $id);
                    $shops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                    # check if there is atleast one shop exists in the array
                    if (count($shops) > 0) {
                        $ids = array();
                        if(!empty($shops)):
                            foreach($shops as $arr):
                                $ids[] = $arr['id'];
                            endforeach;
                        endif;

                        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
                        $query= $queryBuilder->update('\KC\Entity\Shop')
                            ->set('contentManagerName', "'$fullName'")
                            ->set('contentManagerId', 0)
                            ->where($entityManagerUser->expr()->in('id', $ids));
                        $query->getQuery()->execute();
                    }
                }
                $connObj = \BackEnd_Helper_DatabaseManager::closeConnection($connObj['adapter']);
            }
        }
    }

    public function setUserSession($uId, $token)
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $queryBuilder  = $entityManagerUser->createQueryBuilder();
        $query = $queryBuilder->select('u.id')
            ->from('\KC\Entity\UserSession', 'u')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(1);
        $q = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $id = 1;
        if (count($q) > 0) {
            $id = $q[0]['id'] + 1 ;
        }
        $usersession = new KC\Entity\UserSession();
        $usersession->id = $id;
        $usersession->userId = $uId;
        $usersession->sessionId = $token;
        $entityManagerUser->persist($usersession);
        $entityManagerUser->flush();
    }
   
    public static function getRoles()
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        //return $data =  Doctrine::getTable("Role")->findAll()->toArray();
        $queryBuilder  = $entityManagerUser->createQueryBuilder();
        $query = $queryBuilder->select('r')
            ->from('KC\Entity\Role', 'r')
            ->setParameter(1, \Auth_StaffAdapter::getIdentity()->users->id)
            ->where('r.id >= ?1');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }


    public static function getManagersLists($site_name)
    {
        //connection generate with second database
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
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $queryBuilder  = $entityManagerUser->createQueryBuilder();
        $query = $queryBuilder->select('u.id, u.firstName as fname,u.lastName as lname, r.id as role')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->leftJoin("u.users", "r")
            ->setParameter(1, '0')
            ->where('u.deleted = ?1')
            ->setParameter(2, 4)
            ->andWhere('r.id = ?2')
            ->setParameter(3, $site_name)
            ->andWhere("w.name = ?3")
            ->orderBy('fname', 'ASC');
        $editors = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $managers['editors'] = $editors;
        return $managers;
    }

    public static function getTopFiveForAutoComp($for, $param)
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $queryBuilder  = $entityManagerUser->createQueryBuilder();
        $query = $queryBuilder->select('u.firstName as firstName')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin("u.users", "r")
            ->where('u.deleted ='. $for)
            ->andWhere('r.id >='. \Auth_StaffAdapter::getIdentity()->users->id)
            ->andWhere("u.id <>". \Auth_StaffAdapter::getIdentity()->id)
            ->andWhere($queryBuilder->expr()->like('u.firstName', $queryBuilder->expr()->literal($param.'%')))
            ->orderBy('u.firstName', 'ASC')
            ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $ar =  array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] =  $d['firstName'];
            }
        } else {
            $ar[] =  'No Record Found';
        }
        return $ar;
    }

    public static function getUserList($params)
    {
        $role = $params['role'];
        $srh = $params['searchtext'];
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $qb = $queryBuilder
            ->from('KC\Entity\User', 'u')
            ->leftJoin("u.users", "r")
            ->leftJoin('u.profileimage', 'p')
            ->where('u.deleted = 0')
            ->andWhere('r.id >='. \Auth_StaffAdapter::getIdentity()->users->id);
        if ((intval($role)) > 0) {
            $qb->andWhere('r.id='. $role);
        }
        if ($srh!='undefined') {
            $qb->andWhere($queryBuilder->expr()->like('u.firstName', $queryBuilder->expr()->literal($srh.'%')));
        }
        $qb->andWhere('u.id <>'. \Auth_StaffAdapter::getIdentity()->id);

        $request  = \DataTable_Helper::createSearchRequest($params, array('id', 'firstName', 'email'));

        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emUser'), $request);
        $builder
            ->setQueryBuilder($qb)
            ->add('number', 'u.id')
            ->add('text', 'u.firstName, u.lastName')
            ->add('text', 'u.email')
            ->add('text', 'r.name')
            ->add('text', 'p.path')
            ->add('text', 'p.name');

        $data = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $data = \DataTable_Helper::getResponse($data, $request);
        return \Zend_Json::encode($data);
    }
   
    public static function getTrashUserList($params)
    {
        $role = $params['role'];
        $srh = $params['searchtext'];
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $data = $queryBuilder->select('u,r.name as role,p.path as path,p.name as ppname')
            ->from('KC\Entity\User', 'u')
            ->leftJoin("u.users", "r")
            ->leftJoin('u.profileimage', 'p')
            ->setParameter(1, '1')
            ->where('u.deleted = ?1')
            ->setParameter(2, \Auth_StaffAdapter::getIdentity()->users->id)
            ->andWhere('r.id >=', '?2');
        if ((intval($role)) > 0) {
            $data->setParameter(3, $role)
                ->addWhere('r.id= ?3');
        }
        if ($srh!='undefined') {
            $data->setParameter(4, $srh.'%')
            ->andWhere($queryBuilder->expr()->like('u.firstName', '?4'));
        }
        $data->setParameter(5, \Auth_StaffAdapter::getIdentity()->id)
            ->andWhere('u.id <>', '?5')
            ->orderBy("u.id", "DESC")->getQuery();

        return Zend_Json::encode(
            \DataTable_Helper::generateDataTableResponse(
                $data,
                $params,
                array("__identifier" => "u.id, r.id, p.id",'u.id','u.firstName','u.email','role'),
                array(),
                array()
            )
        );
    }

    public function getPageAutor($site_name)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u.id,u.firstName as fname,u.lastName as lname')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->where($queryBuilder->expr()->eq('u.deleted', '0'))
            ->andWhere($queryBuilder->expr()->eq('w.url', $site_name))
            ->orderBy('fname', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $data;
    }

    public static function addStoreInList($name)
    {
        //find shop by name
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('s')
            ->from('KC\Entity\Shop', 's')
            ->where($queryBuilder->expr()->eq('s.name', $name))
            ->setMaxResults(1);
        $Shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (sizeof($Shop) > 0) {
            //check store exist or not
            $repo = $entityManagerLocale->getRepository('KC\Entity\Adminfavoriteshop');
            $pc = $repo->findOneBy(array('shopId' => $Shop[0]['id']));
            if (sizeof($pc) > 0) {
                $flag = '2';
            } else {
                $flag = '1';
                //add new store if not exist in datbase
                $pc = new KC\Entity\Adminfavoriteshop();
                $pc->shopId = $Shop[0]['id'];
                $pc->userId = Auth_StaffAdapter::getIdentity()->id;//get current user(admin) id
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
                $flag = $pc->toArray();
            }

        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
        return $flag;
    }
   
    public static function searchTopTenStore($keyword, $selctedshop)
    {
        $SP = $selctedshop!='' ? $selctedshop: 0;
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.name as name,s.id as id')
            ->from('\KC\Entity\Shop', 's')
            ->where($queryBuilder->expr()->eq('s.deleted', '0'))
            ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword . '%')))
            ->andWhere($queryBuilder->expr()->notIn('s.id', $SP))
            ->andWhere($queryBuilder->expr()->eq('s.status', '1'))
            ->orderBy('s.name', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
    
    public static function checkStoreExistOrNot($name)
    {
        //$Shop = Doctrine_query::create()->from('Shop')
        //->where('name=' . "'$name'")->limit(1)->fetchArray();
        $Shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $name);
       
        $flag = 0;
        if ($Shop) {

             $flag = 1;
        }

        return $flag;
    }
   
    public static function getUserInterestingCat($id)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('o, c')
            ->from('KC\Entity\Interestingcategory', 'o')
            ->leftJoin('o.category', 'c')
            ->where("o.userId=".$id);
        $userFevoriteCat = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userFevoriteCat;
    }
   
    public static function getUserDetail($uId)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('Count(o.id) as MaxOffers, o.authorId, o.authorName')
            ->from('\KC\Entity\Offer', 'o')
            ->groupBy("o.authorName")
            ->orderBy('MaxOffers', 'DESC');
        $Userdata = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u, rf, w, pi')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->leftJoin("u.profileimage", "pi")
            ->where($queryBuilder->expr()->eq('u.id', $Userdata[0]['authorId']));
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }


    public static function getFamousUserDetail($eId)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u, rf, w, pi')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->leftJoin("u.profileimage", "pi")
            ->where($queryBuilder->expr()->eq('u.id', $eId));
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getUserIntcategory($uId)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('c.name, c.permaLink, ic.id')
            ->from('\KC\Entity\Interestingcategory', 'ic')
            ->leftJoin("ic.category", "c")
            ->where($queryBuilder->expr()->eq('ic.userId', $uId))
            ->andWhere($queryBuilder->expr()->eq('c.deleted', '0'));
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
   
    public static function returnEditorUrl($id)
    {
        # check for valid user id
        if (intval($id) > 0) {
            $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->select('u.slug')
                ->from('\KC\Entity\User', 'u')
                ->leftJoin("u.profileimage", "pi")
                ->where($queryBuilder->expr()->eq('u.id', $id));
            $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $editor = \FrontEnd_Helper_viewHelper::__link("link_redactie"). "/" ;
            $url = HTTP_PATH. $editor . $data['slug'];
            return array('url' => $url , 'permalink' => $editor . $data['slug'] );
        }
        return false;
    }


    public static function getAllUserPermalinks($site_name)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u.slug')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->where($queryBuilder->expr()->eq('u.deleted', '0'))
            ->andWhere($queryBuilder->expr()->eq('w.url', $site_name))
            ->andWhere($queryBuilder->expr()->eq('u.showInAboutListing', '1'));
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getUserName($uId)
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u.firstName, u.lastName')
            ->from('\KC\Entity\User', 'u')
            ->where($queryBuilder->expr()->eq('u.id', $uId));
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($data)) {
            $name = $data[0]['firstName'].' '.$data[0]['lastName'];
        } else {
            $name = '';
        }
        return $name;
    }
    
    public static function getAllUser()
    {
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u.id, u.firstName, u.lastName, u.deleted')
            ->from('\KC\Entity\User', 'u');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public function updatePassword($params = null)
    {
        if ($this->validatePassword($params['curPassword'])) {
            // check user want to update password or not based upon old password
            if (isset($params['newPassword']) && isset($params['confirmPassword'])) {
                if ($params['newPassword'] !== $params['confirmPassword']) {
                    return  'New password and confrim don\'t matched';
                }
                if (! $this->isPasswordDifferent($params['confirmPassword'])) {
                    return  'New password can\'t be same as previous password';
                }
                if ($this->isValidPassword($params['confirmPassword'])) {
                            // encrypt new passsword
                    self::setPassword($params['confirmPassword']) ;
                    $this->save();

                    # reeturn false to ensure password changed
                    return false;
                } else {
                    return  'New password must contain a number, capital letter and a special character';
                }
            } else {
                return "Please enter new password an scurrent password" ;
            }
        } else {
            return  'Please enter valid current password' ;
        }
    }

    public function setPassword($classObject, $password)
    {
        $classObject->password = md5($password);
        $classObject->passwordChangeTime = new \DateTime('now');
        return $classObject;
    }

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
        foreach ($rules as $rule) {
            $valid = $valid && preg_match($rule, $password);
            if (!$valid) {
                break;
            }
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
