<?php
class FrontEnd_Helper_TestEntitiesFunctions
{
    public $entityManager = array();

    public function __construct()
    {
        $this->entityManager = \Zend_Registry::get('emUser');
    }
    
    public function saveUser($profileId, $websiteId)
    {
        $user  =  new KC\Entity\User();
        $user->firstname = 'Test';
        $user->lastname = "By Dev";
        $user->mainText = 'aasdasd';
        $user->profileimage = $this->entityManager->find('KC\Entity\ProfileImage', $profileId);
        $user->deleted = 0;
        $user->addtosearch = '0';
        $user->website[] = $this->entityManager->find('KC\Entity\Website', $websiteId);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return 'Record has been successfully saved! - '.$user->id;
    }

    public function getUser($userId)
    {
        $user = $this->entityManager->find('KC\Entity\User', $userId);
        return $user;
    }

    public function getUserWithRelation($userId)
    {
        $entityManager = $this->entityManager->createQueryBuilder();
        $query = $entityManager->select('user.firstname, website.name')
            ->from('KC\Entity\User', 'user')
            ->leftJoin('user.website', 'website')
            ->setParameter(1, $userId)
            ->where('user.id = ?1');
        $userDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userDetails;
    }
}
