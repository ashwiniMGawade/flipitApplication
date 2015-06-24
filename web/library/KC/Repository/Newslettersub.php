<?php
namespace KC\Repository;

class Newslettersub extends \Core\Domain\Entity\Newslettersub
{
    public static function checkDuplicateUser($email)
    {
        $cnt  = \Zend_Registry::get('emLocale')
            ->getRepository('\Core\Domain\Entity\Newslettersub')
            ->findBy(array('email' => $email));
        return count($cnt->id);
    }

    public static function registerUser($email)
    {
        $cnt  = new \Core\Domain\Entity\Newslettersub();
        $cnt->email = $email;
        $cnt->deleted = 0;
        $cnt->created_at = new \DateTime('now');
        $cnt->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($cnt);
        \Zend_Registry::get('emLocale')->flush();
    }
}
