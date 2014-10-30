<?php
namespace KC\Repository;

class EmailSubscribe extends \KC\Entity\EmailSubscribe
{
    public static function add_emailsubscriber($email)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $data = new Kc\Entity\Emailsubscribe();
        $data->email = $email;
        $data->send = 0;
        $entityManagerLocale->persist($data);
        $entityManagerLocale->flush();
    }
}