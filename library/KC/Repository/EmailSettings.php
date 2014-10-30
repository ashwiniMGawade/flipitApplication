<?php
namespace KC\Repository;

class EmailSettings extends \KC\Entity\EmailSettings
{
    public static function getEmailSettingsContent()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('es.email, es.name, es.locale, es.timezone, es.id')
        ->from('KC\Entity\EmailSettings', 'es');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
}