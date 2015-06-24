<?php
class FrontEnd_Helper_TestEntitiesFunctions
{
    public $entityManagerUser = array();
    public $entityManagerUserLocale = array();
    public function __construct()
    {
        $this->entityManagerUser = \Zend_Registry::get('emUser');
        $this->entityManagerUserLocale =\Zend_Registry::get('emLocale');
    }
    
    public function saveUser($profileId, $websiteId)
    {
        $user  =  new \Core\Domain\Entity\User();
        $user->firstname = 'Test';
        $user->lastname = "By Dev";
        $user->mainText = 'aasdasd';
        $user->profileimage = $this->entityManagerUser->find('\Core\Domain\Entity\User\ProfileImage', $profileId);
        $user->deleted = 0;
        $user->addtosearch = '0';
        $user->website[] = $this->entityManagerUser->find('\Core\Domain\Entity\User\Website', $websiteId);
        $this->entityManagerUser->persist($user);
        $this->entityManagerUser->flush();
        return 'Record has been successfully saved! - '.$user->id;
    }

    public function getUser($userId)
    {
        $user = $this->entityManagerUser->find('\Core\Domain\Entity\User\User', $userId);
        return $user;
    }

    public function getUserWithRelation($userId)
    {
        $entityManagerUser = $this->entityManagerUser->createQueryBuilder();
        $query = $entityManagerUser->select('user.firstname, website.name')
            ->from('\Core\Domain\Entity\User\User', 'user')
            ->leftJoin('user.website', 'website')
            ->setParameter(1, $userId)
            ->where('user.id = ?1');
        $userDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }

    public function getVarnish($varnishId = 35891)
    {
        return $this->entityManagerUserLocale->find('\Core\Domain\Entity\Varnish', $varnishId);
    }
}
