<?php
namespace KC\Repository;

class CodeAlertSettings extends \KC\Entity\CodeAlertSettings
{
    public static function saveCodeAlertSettings($codeAlertEmailSubject, $codeAlertEmailHeader)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('c')
            ->from("KC\Entity\CodeAlertSettings", 'c')
            ->where('c.id = 1');
        $codeAlertInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
        if (empty($codeAlertInformation)) {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $codeAlertQueue = new \KC\Entity\CodeAlertSettings();
            $codeAlertQueue->email_subject = $codeAlertEmailSubject;
            $codeAlertQueue->email_header = $codeAlertEmailHeader;
            $entityManagerLocale->persist($codeAlertQueue);
            $entityManagerLocale->flush();
        }
        
        $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilderUpdate
            ->update('KC\Entity\CodeAlertSettings', 'cs')
            ->set('cs.email_subject', "'".$codeAlertEmailSubject."'")
            ->set('cs.email_header', "'".$codeAlertEmailHeader."'")
            ->where('cs.id = 1')
            ->getQuery()
            ->execute();

        return true;
    }

    public static function saveCodeAlertEmailHeader($codeAlertSettingsParameters)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('c')
            ->from("KC\Entity\CodeAlertSettings", 'c')
            ->where('c.id = 1');
        $codeAlertInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
        if (empty($codeAlertInformation)) {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $codeAlertQueue = new \KC\Entity\CodeAlertSettings();
            $codeAlertQueue->email_header = $codeAlertSettingsParameters['val'];
            $entityManagerLocale->persist($codeAlertQueue);
            $entityManagerLocale->flush();
        }

        $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilderUpdate
            ->update('KC\Entity\CodeAlertSettings', 'cs')
            ->set('cs.email_header', "'".$codeAlertSettingsParameters['data']."'")
            ->where('cs.id = 1')
            ->getQuery()
            ->execute();

        return true;
    }

    public static function getCodeAlertSettings()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('c')
            ->from("KC\Entity\CodeAlertSettings", 'c')
            ->where('c.id = 1');
        $codeAlertInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $codeAlertInformation;
    }
}
