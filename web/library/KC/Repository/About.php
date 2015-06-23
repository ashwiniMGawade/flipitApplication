<?php
namespace KC\Repository;
class About extends \Core\Domain\Entity\About
{
    #####################################################
    ############ REFECTORED CODE ########################
    public static function getAboutContent($aboutStatus = "")
    {
        $aboutContent =  false;
        $aboutDetail = self::getAboutContentFromSettings();
        if ($aboutDetail) {
            if ($aboutStatus == 1) {
                $aboutStatus = array("1");
            } else {
                $aboutStatus = array("1", "0");
            }
            $aboutPageContentIds = array();
            foreach ($aboutDetail as $aboutPageContent) {
                $aboutPageContentIds[] = $aboutPageContent['value'];
            }
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser->select('about')
            ->from('KC\Entity\About', 'about')
            ->setParameter(1, $aboutStatus)
            ->where($entityManagerUser->expr()->in('about.status', '?1'))
            ->setParameter(2, $aboutPageContentIds)
            ->andWhere($entityManagerUser->expr()->in('about.id', '?2'));
            $aboutContent = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $aboutContent;
        }
        return $aboutContent;
    }

    public static function getAboutContentFromSettings()
    {
        return \KC\Repository\Settings::getAboutSettings("about_");
    }

    #####################################################
    ######### END  REFACTORED CODE ######################
    #####################################################
    public static function removeeabouttab($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\About', 'about')
                ->setParameter(1, $id)
                ->where('about.id = ?1')
                ->getQuery();
            $query->execute();
            return true;
        } else {
            return false;
        }
    }

    public static function checkAboutContent($name)
    {
        return \KC\Repository\Settings::getSettings($name) ;
    }

    public static function newAboutSetting($id, $name)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $settings =  new \KC\Entity\Settings();

        //$settings->name =  constant(  "Settings::" . $name ) ;
        $settings->name = $name;
        $settings->value = $id;
        $settings->created_at = new \DateTime('now');
        $settings->updated_at = new \DateTime('now');
        $settings->deleted = 0;
        $entityManagerLocale->persist($settings);
        $entityManagerLocale->flush();
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_about_page');
    }

    public static function update($params)
    {
        for ($a=0; $a < count($params['title']); $a++) {
            $i = $a+1;
            $retVal = self::checkAboutContent("about_". $i);

            # check if it has integer id of footer

            if ($retVal) {

                # create object of previous data
                $entityManagerLocale  =\Zend_Registry::get('emLocale');
                $about =  $entityManagerLocale->find('KC\Entity\About', $retVal);
            } else {

                # new object
                $about = new \KC\Entity\About();
            }
            $about->title = @$params['title'][$a] ?
                                \BackEnd_Helper_viewHelper::stripSlashesFromString($params['title'][$a]) : null;
            $about->content = @$params['content'][$a] ?
                                \BackEnd_Helper_viewHelper::stripSlashesFromString($params['content'][$a]) : null;
            $about->status = @$params['status'][$a] ? 1 : 0;
            $about->created_at = new \DateTime('now');
            $about->updated_at = new \DateTime('now');
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($about);
            $entityManagerLocale->flush();
            if (! $retVal) {
                self::newAboutSetting($about->id, "about_".$i);
            }
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_about_page');
    }
}
