<?php
namespace KC\Repository;

class Signupfavoriteshop extends \KC\Entity\Signupfavoriteshop
{
    public static function getalladdstore()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('p.id,p.entered_uid,p.store_id,p.created_at,s.name,s.id,l.path,l.name as image')
        ->from("\KC\Entity\Signupfavoriteshop", "p")
        ->leftJoin('p.signupfavoriteshop', 's')
        ->leftJoin('s.logo', 'l')
        ->where("p.store_id = s.id")
        ->andwhere('s.deleted=0')
        ->andwhere('s.status=1')
        ->orderBy("s.name", "ASC");
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function deletestorebyid($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Signupfavoriteshop', 'sc')
                ->where("sc.id=" .$id)
                ->getQuery()->execute();

            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupfavoriteshop_list');
        }
    }

    public static function addcode($codetext)
    {
        if ($codetext) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $code = new \KC\Entity\AccountsettingCode();
            $code->entered_uid = 2;
            $code->code = "$codetext";
            $code->created_at = new \DateTime('now');
            $code->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($code);
            $entityManagerLocale->flush();

            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupfavoriteshop_list');
        }
    }

    public static function addshop($name, $userid, $shopid)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $pc = new \KC\Entity\Signupfavoriteshop();
        $pc->entered_uid = $userid;
        $pc->signupfavoriteshop = $entityManagerLocale->find('KC\Entity\Shop', $shopid);
        $pc->created_at = new \DateTime('now');
        $pc->updated_at = new \DateTime('now');
       
        try {

            $entityManagerLocale->persist($pc);
            $entityManagerLocale->flush();

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('p')
                ->from("\KC\Entity\Signupfavoriteshop", "p")
                ->where("p.id =". $pc->id);
            $flag = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        } catch (Exception $e) {
            return false;
        }
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupfavoriteshop_list');
    }

    public static function searchTopTenShops($keyword, $flag)
    {
        $lastdata = \KC\Entity\Signupfavoriteshop::getalladdstore();
        if (sizeof($lastdata) > 0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $shopdata[$i] = $lastdata[$i]['store_id'];
            }
            $shopvalues = implode(",", $shopdata);
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('s.name, s.id')
                ->from("Shop", "s")
                ->where('s.deleted=', $flag)
                ->andWhere("s.status=", 1)
                ->where($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
                ->andWhere($queryBuilder->expr()->notIn('s.id', $shopvalues))
                ->orderBy("s.name", "ASC")
                ->setMaxResults(10);
            $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        } else {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('s.name, s.id')
                ->from("Shop", "s")
                ->where('s.deleted=', $flag)
                ->andWhere("s.status=", 1)
                ->where($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
                ->orderBy("s.name", "ASC")
                ->setMaxResults(10);
            $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $data;
    }
}
