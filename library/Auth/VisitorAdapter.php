<?php
class Auth_VisitorAdapter implements Zend_Auth_Adapter_Interface {
    #############################################################
    ############# REFACTORED CODE ###############################
    #############################################################
    protected $email = "";
    protected $password = "";

    public function __construct($email, $password, $loginMode = null) {
        $this->email = FrontEnd_Helper_viewHelper::sanitize($email);
        $this->password = FrontEnd_Helper_viewHelper::sanitize($password);
    }

    public function authenticate() {
        $visitor = Doctrine_Query::create()->from("Visitor u")
            ->where("u.email="."'".$this->email."'")
            ->andWhere("u.deleted=0")->fetchOne();
        if ($visitor) {
            if ($visitor->validatePassword($this->password)) {
                return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $visitor);
            } else {
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $visitor, array("Invalid Credentials"));
            }
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array("User Does Not Exist"));
        }
    }

    public static function hasIdentity() {
        $visitoSession = new Zend_Auth_Storage_Session('front_login');
        if ($visitoSession->read()) {
            $visitor = $visitoSession->read();
            $visitorDetails = Doctrine_Core::getTable("Visitor")->find($visitor->id);
            if ($visitorDetails) {
                return true;
            }
        }
        return false;
    }

    public static function getIdentity() {
        $visitoSession = new Zend_Auth_Storage_Session('front_login');
        if ($visitoSession->read()) {
            $visitor = $visitoSession->read();
            $visitorDetails = Doctrine_Core::getTable("Visitor")->find($visitor->id);
            return $visitorDetails;
        }
        return false;
    }

    public static function clearIdentity() {
        $visitoSession= new Zend_Auth_Storage_Session('front_login');
        return $visitoSession->clear();
    }
    #############################################################
    ############# END REFACTORED CODE ###########################
    #############################################################
	/**
	 * forget password check by email from the database
	 * @param $eMail string       	
	 */
	public static function forgotPassword($eMail) {
		$result = Doctrine_Core::getTable ( 'Visitor' )->findOneByemail ( FrontEnd_Helper_viewHelper::sanitize($eMail ) );
		if ($result) {
			
			return array ('id' => $result ['id'], 'username' => $result ['firstName'] );
		} else {
			return false;
		
		}
	}
	/**
	 * generate new password for user
	 * @param $length string       	
	 */
	public static function generateRandomString($length) {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
		$string = "";
		for($p = 0; $p < $length; $p ++) {
			$string .= $characters [mt_rand ( 0, strlen ( $characters ) - 1 )];
		}
		return $string;
	}
	
	public function checkToken($token) {
		$Obj = Doctrine_Core::getTable ( 'VisitorSession' )->findOneBy ( 'sessionid', $token );
		$q = Doctrine_Query::create ()->select ()->from ( 'Visitor u' )->leftJoin ( 'u.usersession us' )->Where ( 'us.sessionId = "' . $token . '"' )->fetchArray ();
		if (count ( $q )) {
			if (! Auth_StaffAdapter::hasIdentity ()) {
				$data_adapter = new Auth_StaffAdapter ( $q ['0'] ['email'], $q ['0'] ['password'], 1 );
				$auth = Zend_Auth::getInstance ();
				$result = $auth->authenticate ( $data_adapter );
				if (Auth_StaffAdapter::hasIdentity ()) {
					$Obj = new Visitor();
					$Obj->updateLoginTime ( Auth_StaffAdapter::getIdentity ()->id );
					$Obj = Doctrine_Core::getTable ( 'Visitor' )->findOneBy ( 'id', Auth_StaffAdapter::getIdentity ()->id );
					
					$user = new Zend_Session_Namespace ( 'Visitor' );
					$user->user_data = $Obj;
					$sessionNamespace = new Zend_Session_Namespace ();
					$sessionNamespace->settings = $Obj->permissions;
				}
			}
		} else {
			
			header ( 'Location:' . PARENT_PATH . 'admin/auth' );
		
		}
	
	}

}
?>