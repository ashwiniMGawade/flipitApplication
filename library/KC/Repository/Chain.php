<?php
namespace KC\Repository;

class Chain extends \KC\Entity\Chain
{
    ######### refactored code #################
    public static function updateChainItemLocale($newLocale, $oldLocale)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\ChainItem', 'c')
                ->set("c.locale", $queryBuilder->expr()->literal($newLocale))
                ->setParameter(1, $queryBuilder->expr()->literal($oldLocale))
                ->where('c.locale = ?1')
                ->getQuery();
        $query->execute();
        return true;
    }
    ######### end refactored code #################
    public function saveChain($request)
    {
        $name = $request->getParam('name', false);
        if ($name) {
            try {
                $entityManagerUser  = \Zend_Registry::get('emUser');
                $chain = new \KC\Entity\Chain();
                $chain->name = $name ;
                $chain->created_at = new \DateTime('now');
                $chain->updated_at = new \DateTime('now');
                $entityManagerUser->persist($chain);
                $entityManagerUser->flush();
                return true ;
            } catch (Exception $e) {
                return  false ;
            }
        } else {
            return false ;
        }
    }
    public static function returnChainList($params)
    {
        $srh =  @$params["searchText"] != 'undefined' ? @$params["searchText"] : '';
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();

        $query = $queryBuilder
            ->from('KC\Entity\Chain', 'c')
            ->where("c.name LIKE '$srh%'");
        $request  = \DataTable_Helper::createSearchRequest(
            $params,
            array('c.name')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emUser'), $request);
        $builder
            ->setQueryBuilder($query)
            ->add('number', 'c.id as id')
            ->add('text', 'c.name as name')
            ->add('number', '(SELECT count(ci.id) FROM KC\Entity\ChainItem ci WHERE ci.chainItem = c.id) as totalShops');
        $list = $builder->getTable()->getResponseArray();
        return $list;
    }

    public static function deleteChain($id)
    {
        try {
            $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Chain', 'c')
                    ->where("c.id=" . $id)
                    ->getQuery();
            $query->execute();
            return true ;
        } catch (Exception $e) {
            return false;
        }
    }

    public function preDelete($event)
    {
        $chainItem = new \KC\Entity\ChainItem();
        $chainItem->updateVarnish($chainItem->__get('id'));
        $chainItem->free(true);
    }

    public static function returnChainDetail($id)
    {
        try {
            $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->select('c')
            ->from('KC\Entity\Chain', 'c')
            ->where("c.id=" . $id);
            $chainDetail = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            return $chainDetail;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function searchChain($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
        ->select('c.name as name')
        ->from('KC\Entity\Chain', 'c')
        ->where("c.name LIKE '$keyword%'")
        ->orderBy('c.name', 'ASC')
        ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function returnChainData($chainItemId, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select("ci.id")
            ->from('KC\Entity\ChainItem', 'c')
            ->leftJoin('c.chainItem', 'ci')
            ->where('c.id = '.$chainItemId);
        $chainItem = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      
        $pattern = "~((?:.+://|)(?:www.|))(flipit.com/[a-z]{2}|kortingscode.nl)~";
        $permalink = trim(HTTP_PATH_LOCALE, '/');
        $replacement = '$2';
        $currentSite = preg_replace($pattern, $replacement, $permalink);
        $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder
            ->select("c.name,ci.shopName,ci.permalink,w.name,w.url,ci.locale as locale,ci.shopId as shopId,w.chain")
            ->from('KC\Entity\Chain', 'c')
            ->leftJoin('c.chainItem', 'ci')
            ->leftJoin('ci.website', 'w')
            ->where("ci.chainItem =".$chainItem['id'])
            ->andWhere('ci.status = 1')
            ->orderBy('w.name', 'ASC');
        $chainInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $chain = array();
        if (!isset($chainInformation)) {
            return false;
        }

        if (isset($chainInformation)) {
            foreach ($chainInformation as $chainValue) {
                $locale = explode('_', $chainValue['locale']);
                $locale = isset($locale[1]) ? $locale[1] : $locale[0];
                $hrefLocale = isset($chainValue['locale']) ? $chainValue['locale'] : 'nl_NL';
                $websiteUrl  = $chainValue['url'] . '/' . $chainValue['permalink'] ;
                $hrefLang = isset($chainValue['chain']) && $chainValue['chain'] != '' ?
                    $chainValue['chain'] : preg_replace('~_~', '-', $hrefLocale);
                $headLink = sprintf(
                    '<link rel="alternate" hreflang="%s" href="%s"/>',
                    $hrefLang,
                    $websiteUrl
                );
                $shop = array();

                if ($chainValue['name'] != $currentSite || $shopId != $chainValue['shopId']) {
                    $shop = array(
                        'name' => $chainInformation['name'],
                        'shop' => $chainValue['shopName'],
                        'locale' => strtoupper($locale),
                        'url' => $websiteUrl
                    );
                }
                $chain[] = array('shops' => $shop, 'headLink' => $headLink);
            }
        }
        return $chain;
    }
}