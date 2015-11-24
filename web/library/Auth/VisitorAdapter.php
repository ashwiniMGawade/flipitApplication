<?php
class Auth_VisitorAdapter implements Zend_Auth_Adapter_Interface
{

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
            ->from("\Core\Domain\Entity\Visitor", "u")
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
            $visitorDetails = Zend_Registry::get('emLocale')->getRepository('\Core\Domain\Entity\Visitor')->findOneBy(array('id' => $visitor->id, 'email' => $visitor->email));

            if ($visitorDetails) {
                return true;
            }
        }
        return false;
    }//

    public static function getIdentity()
    {
        $visitoSession = new Zend_Auth_Storage_Session('front_login');
        if ($visitoSession->read()) {
            $visitor = $visitoSession->read();
            $visitorDetails = Zend_Registry::get('emLocale')->getRepository('\Core\Domain\Entity\Visitor')->findOneBy(array('id' => $visitor->id, 'email' => $visitor->email));
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
    public static function generateRandomString($length)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $string = "";
        for ($p = 0; $p < $length; $p ++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
}
