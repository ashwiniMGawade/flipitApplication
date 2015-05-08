<?php
namespace KC\Repository;

class Rights extends \KC\Entity\Rights
{
    public function addRights()
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $rights = new \KC\Entity\Rights();
        $rights->name = 'administration';
        $rights->rights = 1;
        $rights->role = $entityManagerUser->find('KC\Entity\Role', 1);
        $rights->created_at = new \DateTime('now');
        $rights->updated_at = new \DateTime('now');
        $entityManagerUser->persist($rights);
        $entityManagerUser->flush();
    }
}
