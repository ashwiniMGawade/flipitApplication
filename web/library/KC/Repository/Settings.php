<?php
namespace KC\Repository;
class Settings extends \Core\Domain\Entity\Settings
{
    const EMAIL_LIGHT_BOX = "email_light_box";
    const FOOTER = "footer";
    const SPECIAL = "special";
    const ABOUT_1 = "about_1";
    const ABOUT_2 = "about_2";
    const ABOUT_3 = "about_3";
    const SEENIN_1 = "seenin_1";
    const SEENIN_2 = "seenin_2";
    const SEENIN_3 = "seenin_3";
    const SEENIN_4 = "seenin_4";
    const SEENIN_5 = "seenin_5";
    const SEENIN_6 = "seenin_6";
    #####################################################
    ######### REFACTORED CODE ###########################
    #####################################################
    public static function getAboutSettings($settingsName)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.name,s.value')
            ->from('\Core\Domain\Entity\Settings', 's')
            ->setParameter(1, $settingsName.'%')
            ->where($queryBuilder->expr()->like('s.name', '?1'));
        $aboutPageSettings = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $aboutPageSettings;
    }

    public static function getEmailSettings($sendersFieldName)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.value')
            ->from('\Core\Domain\Entity\Settings', 's')
            ->where($queryBuilder->expr()->eq('s.name', $queryBuilder->expr()->literal($sendersFieldName)));
        $emailSettings = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($emailSettings) ? $emailSettings[0]['value'] : '';
    }

    public static function updateSendersSettings($sendersFieldName, $sendersValue)
    {
        $getSettings = self::getEmailSettings($sendersFieldName);
        if (!empty($getSettings)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->update('KC\Entity\Settings', 's')
                ->set("s.value", $queryBuilder->expr()->literal($sendersValue))
                ->where($queryBuilder->expr()->eq('s.name', $queryBuilder->expr()->literal($sendersFieldName)))
                ->getQuery();
            $query->execute();
        } else {
            $entityManagerLocale  = \Zend_Registry::get('emLocale');
            $setting = new \KC\Entity\Settings();
            $setting->name = $sendersFieldName;
            $setting->value = $sendersValue;
            $setting->created_at = new \DateTime('now');
            $setting->updated_at = new \DateTime('now');
            $setting->deleted = 0;
            $entityManagerLocale->persist($setting);
            $entityManagerLocale->flush();
        }
        return true;
    }
 
    #####################################################
    ######### END REFACTORED CODE #######################
    #####################################################

    public static function getSettings($name)
    {
        $entityManagerLocale  =\Zend_Registry::get('emLocale');
        $repo = $entityManagerLocale->getRepository('KC\Entity\Settings');
        $menu = $repo->findOneBy(array('name' => $name));
        if ($menu) {
            return $menu->value;
        }
        // return $data ;

    }

    public static function removesettingabouttab($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Settings', 's')
            ->where('s.value = '.$id)
            ->getQuery();
            $query->execute();
            return true;
        } else {
            return false;
        }

    }

    public static function getAllSettings()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s')
            ->from('\Core\Domain\Entity\Settings', 's');
        $getAll = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $data = array() ;
        foreach ($getAll as $val) {
            $data[$val['name']] = $val['value'];
        }
        return $data ;
    }

    public static function setSettings($name, $value)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Settings', 's')
            ->set("s.value", $queryBuilder->expr()->literal($value))
            ->setParameter(1, $queryBuilder->expr()->literal($name))
            ->where('s.name', '?1')
            ->getQuery();
        $query->execute();
    }
}