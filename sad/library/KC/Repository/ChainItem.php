<?php
namespace KC\Repository;

class ChainItem extends \KC\Entity\User\ChainItem
{
    public function saveChain($request, $locale)
    {
        $shopId = $request->getParam('searchShopId', false);
        if (! $shopId) {
            return false ;
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('s')
            ->from('KC\Entity\Shop', 's')
            ->where('s.id ='.$shopId);
        $shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if ($shop[0]['id'] > 0) {
            $shopName = $shop[0]['name'];
            $shopPermalink = $shop[0]['permaLink'];
            $website = $request->getParam('locale', false);
            $chainId = $request->getParam('chain', false);
            if ($shopName && $website && $chainId) {
                try {
                    $entityManagerUser  = \Zend_Registry::get('emUser');
                    $chain = new \KC\Entity\User\ChainItem();
                    $chain->website = $entityManagerUser->find('KC\Entity\User\Website', $website);
                    $chain->chainItem = $entityManagerUser->find('KC\Entity\User\Chain', $chainId);
                    $chain->shopName = $shopName;
                    $chain->permalink = $shopPermalink;
                    $chain->shopId = $shopId;
                    $chain->locale = $locale;
                    $chain->created_at = new \DateTime('now');
                    $chain->updated_at = new \DateTime('now');
                    # check if shop is online and also show chian status
                    if ($shop[0]['status'] == 1 && $shop[0]['showChains'] == 1) {
                        $chain->status = 1 ;
                    } else {
                        $chain->status = 0 ;
                    }
                    $entityManagerUser->persist($chain);
                    $entityManagerUser->flush();
                    # update chain id in the shop model
                    $chainId = $chain->__get('id');
                    if ($chainId) {
                        \KC\Repository\Shop::addChain($chainId, $shop[0]['id']);
                    }
                    return  $chainId;
                } catch (Exception $e) {
                    return  false ;
                }
            }
        } else {
            return false ;
        }
    }

    public static function returnChainItemList($params)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->from('KC\Entity\User\ChainItem', 'c')
            ->leftJoin('c.website', 'w')
            ->where('c.chainItem ='.$params['id']);
        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array('c.shopName', 'w.name', 'c.locale', 'c.status')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emUser'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('text', 'c.shopName')
            ->add('text', 'w.name')
            ->add('text', 'c.locale')
            ->add('number', 'c.status');
        $list = $builder->getTable()->getResponseArray();
        return $list;
    }

    public static function getChainItemDetail($id)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'c.shopName as shopName,c.permalink,w.name as website,w.url as websiteUrl,c.locale as locale,w.id as websiteId'
            )
            ->from('KC\Entity\User\ChainItem', 'c')
            ->leftJoin('c.website', 'w')
            ->where('c.id ='.$id);
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function deleteChainItem($id)
    {
        try {
            $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\User\ChainItem', 'c')
                    ->where('c.id ='.$id)
                    ->getQuery();
            $query->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function fetchAllChainItems($id)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'c.shopName as shopName,c.permalink,w.name as website,w.url as websiteUrl,c.locale as locale,w.id as websiteId'
            )
            ->from('KC\Entity\User\ChainItem', 'c')
            ->leftJoin('c.website', 'w')
            ->where('c.chainItem ='.$id);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public function update($data = false, $shop)
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $chain = new \KC\Entity\User\ChainItem();
        # if show chain is on then modify status based on hsop status
        if ($shop['showChains'] == 1) {
            if (isset($data['status'])) {
                $chain->status = $data['status'];
            } else {
                # check offline status before update chian status
                if ($shop['status']) {
                    $chain->status = 1;
                }
            }
        } else {
            $chain->status = 0;
        }
        # check deletd status
        if (isset($data['deleted'])) {
            if ($data['deleted'] == 1) {
                $chain->status = 0 ;
            } else {
                # chekc show chains in case of shop is beiong restore
                if ($shop['showChains'] == 1 && $shop['status'] == 1) {
                    $chain->status = 1;
                }
            }
        }
        # check if shop name is modified
        if (isset($data['name'])) {
            $chain->shopName = $data['name'];
        }
        #check if shop permalink is modified
        if (isset($data['permaLink'])) {
            $chain->permalink = $data['permaLink'];
        }
        $entityManagerUser->persist($chain);
        $entityManagerUser->flush();
    }

    public function postDelete($event)
    {
        self::updateVarnish();
    }

    public function preDelete($event)
    {
        self::updateVarnish();
    }

    public function updateVarnish($chainId = false)
    {
        if (! $chainId) {
            $chainItem = new \KC\Entity\User\ChainItem();
            $chainId = $chainItem->__get('id');
        }
        $items = static::fetchAllChainItems($chainId);
        if ($items) {
            foreach ($items as $data) {
                $localeData = explode('/', $data['website']);
                $locale = isset($localeData[1]) ?  $localeData[1] : "en" ;
                # connect to select locale database
                $connObj = \BackEnd_Helper_DatabaseManager::addConnection($locale);
                # add urls to refresh in Varnish
                $varnishObj = new Varnish($connObj['connName']);
                $varnishObj->addUrl(trim($data['websiteUrl'], '/'). '/' . $data['permalink']);
                $connObj = \BackEnd_Helper_DatabaseManager::closeConnection($connObj['adapter']);
            }
        }
    }
}