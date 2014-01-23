<?php
/**
 * Check user is authenticated or not 
 * @author kraj
 * @version 1.0
 */
class Auth_StaffAdapter implements Zend_Auth_Adapter_Interface {
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


		$user = Doctrine_Query::create()->from("User u" )->where("u.email="."'".$this->email."'")->addWhere("u.deleted=0")->fetchOne();
		if ($user) {
			
			if ($user->validatePassword ( ($this->password) )) {
				
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
		if (Zend_Auth::getInstance ()->hasIdentity ()) {
			$u = Zend_Auth::getInstance ()->getIdentity ();
			$member = Doctrine_Core::getTable ( "User" )->find ( $u ["id"] );
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
		if (Zend_Auth::getInstance ()->hasIdentity ()) {
			$u = Zend_Auth::getInstance ()->getIdentity ();
			$member = Doctrine_Core::getTable ( "User" )->find ( $u ["id"] );
			return $member;
		}
		return false;
	}
	/**
	 * clear the Identity fron the zend auth
	 */
	public static function clearIdentity() {
		return Zend_Auth::getInstance ()->clearIdentity ();
	}
	/**
	 * forget password check by email from the database
	 * @param $eMail string       	
	 */
	public function forgotPassword($eMail) {
		$result = Doctrine_Core::getTable ( 'User' )->findOneByemail ( $eMail );
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
	public function genRandomString($length) {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
		$string = "";
		for($p = 0; $p < $length; $p ++) {
			$string .= $characters [mt_rand ( 0, strlen ( $characters ) - 1 )];
		}
		return $string;
	}
	
	public function checkToken($token) {
		$Obj = Doctrine_Core::getTable ( 'UserSession' )->findOneBy ( 'sessionid', $token );
		$q = Doctrine_Query::create ()->select ()->from ( 'User u' )->leftJoin ( 'u.usersession us' )->Where ( 'us.sessionId = "' . $token . '"' )->fetchArray ();
		if (count ( $q )) {
			if (! Auth_StaffAdapter::hasIdentity ()) {
				$data_adapter = new Auth_StaffAdapter ( $q ['0'] ['email'], $q ['0'] ['password'], 1 );
				$auth = Zend_Auth::getInstance ();
				$result = $auth->authenticate ( $data_adapter );
				if (Auth_StaffAdapter::hasIdentity ()) {
					$Obj = new User ();
					$Obj->updateLoginTime ( Auth_StaffAdapter::getIdentity ()->id );
					$Obj = Doctrine_Core::getTable ( 'User' )->findOneBy ( 'id', Auth_StaffAdapter::getIdentity ()->id );
					
					$user = new Zend_Session_Namespace ( 'user' );
					$user->user_data = $Obj;
					//$user->setExpirationSeconds(10);
					$sessionNamespace = new Zend_Session_Namespace ();
					$sessionNamespace->settings = $Obj->permissions;
					//$sessionNamespace->setExpirationSeconds(10);
				}
			}
		} else {
			
			header ( 'Location:' . PARENT_PATH . 'admin/auth' );
		
		}
	
	}


}
?>