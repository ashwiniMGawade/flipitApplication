<?php
class Auth_VisitorAdapter implements Zend_Auth_Adapter_Interface {
    #############################################################
    ############# REFACTORED CODE ###############################
    #############################################################
    protected $email = "";
    protected $password = "";

    public function __construct($email, $password, $loginMode = null)
    {
        $this->email = FrontEnd_Helper_viewHelper::sanitize($email);
        $this->password = FrontEnd_Helper_viewHelper::sanitize($password);
    }

    public function authenticate()
    {
        $queryBuilder = Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('u')
            ->from("\KC\Entity\Visitor", "u")
            ->where("u.email="."'".$this->email."'")
            ->andWhere('u.active = 1')
            ->andWhere("u.deleted = 0");
        $visitor =  (object) $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if ($visitor) {
            if ($this->validatePassword($this->password, $visitor->password)) {
                return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $visitor);
            } else {
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $visitor, array("Invalid Credentials"));
            }
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array("User Does Not Exist"));
        }
    }

    public static function hasIdentity()
    {
        $visitoSession = new Zend_Auth_Storage_Session('front_login');
        if ($visitoSession->read()) {
            $visitor = $visitoSession->read();
            $visitorDetails = Zend_Registry::get('emLocale')->find('\KC\Entity\Visitor', $visitor->id);
            if ($visitorDetails) {
                return true;
            }
        }
        return false;
    }

    public static function getIdentity()
    {
        $visitoSession = new Zend_Auth_Storage_Session('front_login');
        if ($visitoSession->read()) {
            $visitor = $visitoSession->read();
            $visitorDetails = Zend_Registry::get('emLocale')->find('\KC\Entity\Visitor', $visitor->id);
         //   print_r($visitorDetails);
            return $visitorDetails;
        }
        return false;
    }

    public static function clearIdentity()
    {
        $visitoSession= new Zend_Auth_Storage_Session('front_login');
        return $visitoSession->clear();
    }

    public static function forgotPassword($visitorEmail)
    {
        $visitorDetails = Doctrine_Core::getTable('Visitor')->findOneByemail(FrontEnd_Helper_viewHelper::sanitize($visitorEmail));
        $visitor = false;
        if ($visitorDetails) {
            $visitor = array('id' => $visitorDetails['id'], 'username' => $visitorDetails['firstName']);
        }
        return $visitor;
    }

    public function validatePassword($passwordToBeVerified, $dbPassword)
    {
        if ($dbPassword == $passwordToBeVerified) {
            return true;
        }
        return false;
    }
    #############################################################
    ############# END REFACTORED CODE ###########################
    #############################################################
    /**
     * generate new password for user
     * @param $length string        
     */
    public static function generateRandomString($length)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $string = "";
        for ($p = 0; $p < $length; $p ++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
    
    public function checkToken($token)
    {
        $Obj = Doctrine_Core::getTable('VisitorSession')->findOneBy('sessionid', $token);
        $q = Doctrine_Query::create()
                ->select()->from('Visitor u')
                ->leftJoin('u.usersession us')
                ->Where('us.sessionId = "' . $token . '"')
                ->fetchArray();
        if (count($q)) {
            if (!Auth_StaffAdapter::hasIdentity()) {
                $data_adapter = new Auth_StaffAdapter($q ['0']['email'], $q['0']['password'], 1);
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($data_adapter);
                if (Auth_StaffAdapter::hasIdentity()) {
                    $Obj = new Visitor();
                    $Obj->updateLoginTime(Auth_StaffAdapter::getIdentity()->id);
                    $Obj = Doctrine_Core::getTable('Visitor')->findOneBy('id', Auth_StaffAdapter::getIdentity()->id);
                    
                    $user = new Zend_Session_Namespace('Visitor');
                    $user->user_data = $Obj;
                    $sessionNamespace = new Zend_Session_Namespace();
                    $sessionNamespace->settings = $Obj->permissions;
                }
            }
        } else {
            header('Location:' . PARENT_PATH . 'admin/auth');
        }
    }
}
?>