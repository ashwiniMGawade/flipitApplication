<?php
namespace KC\Repository;

class FavoriteShop extends \Core\Domain\Entity\FavoriteShop
{
    ####################### refactored code ################
    public static function getVisitorsCountByFavoriteShopId($shopId)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('v.id as visitorId, s.id as shopId')
            ->from('\Core\Domain\Entity\FavoriteShop', 'p')
            ->leftJoin("p.visitor", 'v')
            ->leftJoin("p.shop", 's')
            ->leftJoin("s.logo", 'l')
            ->andWhere("p.shop = s.id")
            ->andWhere("s.status = 1")
            ->andWhere("s.deleted = 0")
            ->andWhere("p.shop = ".$shopId)
            ->andWhere("v.status = 1")
            ->andWhere("v.codeAlert = 1")
            ->orderBy("s.name", "ASC");
        $shopVisitorInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($shopVisitorInformation) ? count($shopVisitorInformation) : 0;
    }

    public static function filterAlreadyFavouriteShops($popularShops)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('fv')
            ->from('\Core\Domain\Entity\FavoriteShop', 'fv')
            ->where('fv.visitor ='. \Auth_VisitorAdapter::getIdentity()->id);
        $visitorFavouriteShops = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
               
        $favouriteShops = array();
        foreach ($visitorFavouriteShops as $visitorFavouriteShop) {
            $favouriteShops[] = $visitorFavouriteShop['shopId'];
        }
        $removeAlreayAddedFavouriteShops = array();
        foreach ($popularShops as $popularShop) {
            if (!in_array($popularShop['id'], $favouriteShops)) {
                $removeAlreayAddedFavouriteShops[] = $popularShop;
            }
        }
        return $removeAlreayAddedFavouriteShops;
    }
    ###################### END REFACTORED CODE #############

    public static function get_suggestionshops($userid, $flag)
    {
        $lastdata = self::getShopsByVisitorId($userid);
        if (sizeof($lastdata) > 0) {
            for ($i=0; $i < sizeof($lastdata); $i++) {
                $shopdata[$i]=$lastdata[$i]['shopId'];
            }
            $shopvalues=implode(",", $shopdata);
            
            $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s.name as name,s.id as id,fav.store_id')
            ->from("\Core\Domain\Entity\Signupfavoriteshop", "fav")
            ->leftJoin('fav.signupfavoriteshop', 's')
            ->where('s.deleted=' . $flag)
            ->andWhere("s.status= 1")
            ->andWhere("fav.store_id = s.id")
            ->andWhere($queryBuilder->expr()->notIn('fav.store_id', $shopvalues))
            ->orderBy("s.name", "ASC")
            ->setMaxResults(10);

            $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        } else {
            
            $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s.name as name,s.id as id,fav.store_id')
            ->from("\Core\Domain\Entity\Signupfavoriteshop", "fav")
            ->leftJoin('fav.signupfavoriteshop', 's')
            ->where('s.deleted=' . $flag)
            ->andWhere("s.status= 1")
            ->andWhere("fav.store_id = s.id")
            ->andWhere($queryBuilder->expr()->notIn('fav.store_id', $shopvalues))
            ->orderBy("s.name", "ASC")
            ->setMaxResults(10);
            $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }

        return $data;
    }

    public static function delete_favshop($id)
    {
        if ($id) {
            //delete particular code from list
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\FavoriteShop', 'fv')
                    ->where("fv.id=" .$id)
                    ->getQuery()->execute();
        }
    }

    public static function getShopsByVisitorId($userid)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder  = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('p, s.name, s.id as shopId, l.path,l.name as imageName,v.id as visitorId')
        ->from("\Core\Domain\Entity\FavoriteShop", "p")
        ->leftJoin('p.visitor', 'v')
        ->leftJoin('p.shop', 's')
        ->leftJoin('s.logo", "l')
        ->andWhere("p.shop = s.id")
        ->andWhere("s.status= 1")
        ->andWhere("s.deleted= 0")
        ->andWhere("p.visitor= $userid")
        ->orderBy("s.name", "ASC");
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchallToptenFavshops($keyword, $flag, $userid)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.name as name,s.id as id')
        ->from("\Core\Domain\Entity\Shop", "s")
        ->where('s.deleted='. $flag)
        ->andWhere("s.status= 1")
        ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
        ->orderBy("s.name", "ASC")
        ->setMaxResults(10);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchToptenFavshops($keyword, $flag, $userid)
    {
        $suggestiondata=FavoriteShop::get_suggestionshops($userid, 0);
        if (sizeof($suggestiondata) > 0) {
            for ($i=0; $i < sizeof($suggestiondata); $i++) {
                $shopsuggestiondata[$i]=$suggestiondata[$i]['store_id'];
            }
            $shopsuggestionvalues=$shopsuggestiondata;
        } else {
            $shopsuggestionvalues="";
        }
        $lastdata = self::getShopsByVisitorId($userid);
        if (sizeof($lastdata)>0) {
            for ($i=0; $i < sizeof($lastdata); $i++) {
                $shopdata[$i]=$lastdata[$i]['shopId'];
            }
            if ($shopsuggestionvalues!='') {
                $shopdata=array_merge($shopdata, $shopsuggestionvalues);
            }
            $shopvalues=implode(",", $shopdata);
        
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s.name as name,s.id as id')
                ->from("\Core\Domain\Entity\Shop", "s")
                ->where('s.deleted='. $flag)
                ->andWhere("s.status= 1")
                ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
                ->andWhere($queryBuilder->expr()->notIn('s.id', $shopvalues))
                ->orderBy("s.name", "ASC")
                ->setMaxResults(10);
            $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        } else {
            if ($shopsuggestionvalues!="") {
                $shopsuggestionvalues=implode(",", $shopsuggestionvalues);
            } else {
                $shopsuggestionvalues=0;
            }

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s.name as name,s.id as id')
                ->from("\Core\Domain\Entity\Shop", "s")
                ->where('s.deleted='. $flag)
                ->andWhere("s.status= 1")
                ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
                ->andWhere($queryBuilder->expr()->notIn('s.id', $shopsuggestionvalues))
                ->orderBy("s.name", "ASC")
                ->setMaxResults(10);
            $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }

        return $data;
    }

    public static function addshop($userid, $shopid)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $pc = new \Core\Domain\Entity\FavoriteShop();
        $pc->visitor = $entityManagerLocale->find('\Core\Domain\Entity\Visitor', $userid);
        $pc->shop = $entityManagerLocale->find('\Core\Domain\Entity\Shop', $shopid);
        $pc->deleted = 0;
        $pc->created_at = new \DateTime('now');
        $entityManagerLocale->persist($pc);
        $entityManagerLocale->flush();

        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s')
            ->from('\Core\Domain\Entity\Shop', 's')
            ->where('s.id ='. $shopid);
        $shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        
        $key = 'shopDetails_'  . $shopid . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'offerDetails_'  . $shopid . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('alreadyFavourite_'.$userid.'_shops');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_'.$userid.'_favouriteShops');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('visitor_'.$userid.'_favouriteShopOffers');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');

        return $shop;

        //call cache function
        //FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_signupfavoriteshop_list');
    }

    public static function voucher_percentage($id)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->where('o.authorId ='. $id);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $percentage = 0;
        $calc_records = count($data);
        if ($calc_records>0) {
            $percentage=5;
        }
        return $percentage;
    }

    public static function calculate_percentage($id)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('\Core\Domain\Entity\FavoriteShop', 'v')
            ->where('v.visitor ='. $id);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $percentage=0;
        $calc_records=count($data);
        if ($calc_records == 0) {
            $percentage=0;
        } else if ($calc_records == 1) {
            $percentage = 5;
        } else if ($calc_records == 2) {
            $percentage = 10;
        } else if ($calc_records==3) {
            $percentage = 15;
        } else if ($calc_records == 4) {
            $percentage=20;
        } else if ($calc_records>=5) {
            $percentage=30;
        }
        return $percentage;
    }

    public static function delFavoriteShops($shopid, $userid)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s')
            ->from('\Core\Domain\Entity\Shop', 's')
            ->where('s.id ='. $shopid);
        $shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete("\Core\Domain\Entity\FavoriteShop", "fv")
            ->where('fv.visitor='.$userid)
            ->andWhere('fv.shop='.$shopid)->execute();

        $key = 'shopDetails_'  . $shopid . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'offerDetails_'  . $shopid . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('alreadyFavourite_'.$userid.'_shops');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('visitor_'.$userid.'_favouriteShopOffers');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_'.$userid.'_favouriteShops');
        return $shop;
    }

    public static function getShopsById($shopId)
    {
        $queryBuilder  = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from("\Core\Domain\Entity\FavoriteShop", "p")
            ->leftJoin('p.visitor', 'v')
            ->leftJoin('p.shop', 's')
            ->leftJoin('s.logo', 'l')
            ->andWhere("p.shop = s.id")
            ->andWhere("s.status = 1")
            ->andWhere("s.deleted= 0")
            ->andWhere("p.shop =" .$shopId)
            ->andWhere("v.status = 1")
            ->andWhere("v.codeAlert = 1")
            ->orderBy("s.name", "ASC");
        $shopVisitorInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopVisitorInformation;
    }
}
