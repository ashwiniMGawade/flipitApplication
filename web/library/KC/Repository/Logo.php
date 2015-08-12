<?php
namespace KC\Repository;

class Logo extends \Core\Domain\Entity\Logo
{
    public static function getPageLogo($logoId)
    {
        if(empty($logoId)) {
           return '';
        }
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale->select("l")
            ->from("\Core\Domain\Entity\Logo", "l")
            ->where("l.id=".$logoId);
        $pageLogo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  !empty($pageLogo[0]) ? $pageLogo[0] : "";
    }
}
