<?php
/**
 * Check frontend visitor is authenticated or not 
 * @author cbhopal
 * @version 1.0
 */
class Auth_VisitorAdapter implements Zend_Auth_Adapter_Interface {
	protected $email = "";
	protected $password = "";
	
	public function __construct($email, $password, $loginMode = null) {
		$this->email = FrontEnd_Helper_viewHelper::sanitize( $email );
		$this->password = FrontEnd_Helper_viewHelper::sanitize( $password );
	
	}
	/**
	 * (non-PHPdoc)
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate() {
		
	//	echo "<pre>";
	//	print_r($this); die;
		// echo $this->email;
		// echo $password;
		
		$user = Doctrine_Query::create()->from("Visitor u" )->where("u.email="."'".$this->email."'")->andWhere("u.deleted=0")->fetchOne();
		
		if ($user) {
			
			

			if ($user->validatePassword ($this->password)) {
				
				return new Zend_Auth_Result ( Zend_Auth_Result::SUCCESS, $user );
			
			} else {
				
				return new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $user, array ("Invalid Credentials" ) );
				// throw new Zend_Auth_Adapter_Exception("Invalid Credentials",
				// Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID );
			}
		} else {
			return new Zend_Auth_Result ( Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array ("User Does Not Exist" ) );
			// throw new Zend_Auth_Adapter_Exception("User Does Not Exist",
			// Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND );
		}
	}
	
	/**
	 * 
	 */
	/**function __destruct() {
		// TODO - Insert your code here
	}
	/**
	 * Check Identity of the user in zend auth
	 * 
	 * @return boolean
	 */
	public static function hasIdentity() {
		$sess = new Zend_Auth_Storage_Session('front_login');
		//echo '<pre>'; print_r($sess->read()); die;
		if ($sess->read()) {
			$u = $sess->read();
			$member = Doctrine_Core::getTable ( "Visitor" )->find ( $u->id );
			if ($member) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Get Identity of the user in zend auth
	 * $retunr object $member
	 */
	public static function getIdentity() {
		$sess = new Zend_Auth_Storage_Session('front_login');
		if ($sess->read()) {
			$u = $sess->read();
			$member = Doctrine_Core::getTable ( "Visitor" )->find ( $u->id );
			return $member;
		}
		return false;
	}
	/**
	 * clear the Identity fron the zend auth
	 */
	public static function clearIdentity() {
		$sess= new Zend_Auth_Storage_Session('front_login');
		return $sess->clear();   //Zend_Auth::getInstance ()->clearIdentity ();
	}
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