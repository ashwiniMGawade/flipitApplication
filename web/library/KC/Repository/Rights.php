<?php
namespace KC\Repository;

class Rights extends \Core\Domain\Entity\User\Rights
{
    public function addRights()
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $rights = new \Core\Domain\Entity\User\Rights();
        $rights->name = 'administration';
        $rights->rights = 1;
        $rights->role = $entityManagerUser->find('\Core\Domain\Entity\User\Role', 1);
        $rights->created_at = new \DateTime('now');
        $rights->updated_at = new \DateTime('now');
        $entityManagerUser->persist($rights);
        $entityManagerUser->flush();
    }
}
