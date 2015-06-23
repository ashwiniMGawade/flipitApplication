<?php
namespace KC\Repository;

class EmailLightBox extends \Core\Domain\Entity\EmailLightBox
{
    
    public static function getLigthBoxContent()
    {
        $retVal = self::checkLightBoxSetting() ;
        if ($retVal) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('e')
                ->from('KC\Entity\EmailLightBox', 'e')
                ->where('e.id='. $retVal);
            $lightBox = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $lightBox[0];
        }
        return false ;
    }

    public static function update($params)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $retVal = self::checkLightBoxSetting() ;
        # check if it has integer id of email light box
        if ($retVal) {
            # create object of previous data
            $lightBox = $entityManagerLocale->find('KC\Entity\EmailLightBox', $retVal);
            $lightBox->created_at = $lightBox->created_at;
        } else {
            # new object
            $lightBox = new \KC\Entity\EmailLightBox();
            $lightBox->created_at = new \DateTime('now');
        }
        if (isset( $params['status'])) {

            if ($params['status'] == 1) {
                $lightBox->status = 1;
            } else {
                $lightBox->status = 0;
            }
        } else {
            $this->status = 1 ;
        }
        $lightBox->title = $params['title'] ;
        $lightBox->content = $params['content'] ;
        $lightBox->updated_at = new \DateTime('now');
        $lightBox->deleted = 0;
        $entityManagerLocale->persist($lightBox);
        $entityManagerLocale->flush();

        if (! $retVal) {
            self::newLightBboxSetting($lightBox->id);
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_emaillightbox_list');
        return $lightBox->id ;
    }

    public static function checkLightBoxSetting()
    {
        return Settings::getSettings(Settings::EMAIL_LIGHT_BOX);
    }

    public static function newLightBboxSetting($id)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $setting = new \KC\Entity\Settings();
        $setting->name = Settings::EMAIL_LIGHT_BOX;
        $setting->value = $id;
        $setting->created_at = new \DateTime('now');
        $setting->updated_at = new \DateTime('now');
        $setting->deleted = 0;
        $entityManagerLocale->persist($setting);
        $entityManagerLocale->flush();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_emaillightbox_list');
    }

    public static function changeStatus($params)
    {
        $status = $params['status'] == 'offline' ? '0' : '1';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\EmailLightBox', 'e')
            ->set('e.status', $status)
            ->where('e.id=', $params['id'])
            ->getQuery();
        $query->execute();
    }
}
