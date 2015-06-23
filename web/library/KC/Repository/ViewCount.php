<?php
namespace KC\Repository;

class ViewCount extends \Core\Domain\Entity\ViewCount
{
    public static function getOfferClick($offerId, $clientIp)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offerClick = $queryBuilder
            ->select('count(v.id) as countExists')
            ->addSelect("(SELECT  click.id FROM KC\Entity\ViewCount click WHERE click.id = v.id) as clickId")
            ->from('KC\Entity\ViewCount', 'v')
            ->where('v.onClick!=0')
            ->andWhere('v.viewcount='.$offerId)
            ->andWhere('v.IP='.$clientIp)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerClick[0]['countExists'];
    }

    public static function getOfferViewCountBasedOnDate($offerId, $offsetDate, $currentDate, $offsetType)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('count(v.id) as viewCount')
            ->from('KC\Entity\ViewCount', 'v')
            ->where(
                $queryBuilder->expr()->between(
                    'v.created_at',
                    $queryBuilder->expr()->literal($offsetDate),
                    $queryBuilder->expr()->literal($currentDate)
                )
            )
            ->andWhere('v.onClick!=0')
            ->andWhere('v.viewcount='.$offerId);
        $offerViewCount = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return array('viewCount'=>$offerViewCount['viewCount'], 'offsetType'=>$offsetType);
    }

    public static function saveOfferClick($offerId, $clientIp)
    {
        $offerClick  = new \KC\Entity\ViewCount();
        $offerClick->viewcount = \Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $offerId);
        $offerClick->onClick = 1;
        $offerClick->onLoad = 0;
        $offerClick->IP = $clientIp;
        $offerClick->created_at = new \DateTime('now');
        $offerClick->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($offerClick);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function getOfferOnload($offerId, $clientIp)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offerOnload = $queryBuilder
            ->select('count(v.id) as countExists')
            ->from('KC\Entity\ViewCount', 'v')
            ->where('v.onLoad!=0')
            ->andWhere('v.viewcount='.$offerId)
            ->andWhere('v.IP='.$clientIp)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerOnload[0]['countExists'];
    }

    public static function saveOfferOnload($offerId, $clientIp)
    {
        $offerOnload  = new \KC\Entity\ViewCount();
        $offerOnload->viewcount = \Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $offerId);
        $offerOnload->onLoad = 1;
        $offerOnload->onClick = 0;
        $offerOnload->IP = $clientIp;
        $offerOnload->created_at = new \DateTime('now');
        $offerOnload->updated_at = new \DateTime('now');
        \Zend_Registry::get('emLocale')->persist($offerOnload);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function getOfferForFrontEnd($offerType, $limit = 10)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = '';
        switch(strtolower($offerType)) {
            case 'popular':
                $format = 'Y-m-j H:m:s';
                $date = date($format);
                $past4Days = date($format, strtotime('-4 day' . $date));
                $nowDate = $date;
                $data = $queryBuilder
                ->select(
                    'vc,vc.id, sum(vc.onClick) as views, o.id, o.title, o.Visability as visability,
                    o.couponCode as couponcode, o.refOfferUrl as refofferurl,
                    o.startDate as startdate, o.endDate as enddate, o.exclusiveCode as exclusivecode,
                    o.editorPicks as editorpicks,o.extendedOffer as extendedoffer,o.discount, o.authorId,
                    o.authorName, IDENTITY(o.shopOffers) as shopid, IDENTITY(o.logo) as offerlogoid,
                    o.userGenerated, o.approved,img'
                )
                ->from('KC\Entity\ViewCount', 'vc')
                ->leftJoin('vc.viewcount', 'o')
                ->leftJoin('o.logo', 'img')
                ->where('vc.updated_at <=' . "'$nowDate' AND vc.updated_at >=". "'$past4Days'")
                ->groupby('vc.viewcount')
                ->orderBy('vc.id', 'desc')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                break;
            default:
                break;
        }
        return $data;
    }

    public static function generatePopularCode()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j 00:00:00';
        $date = date($format);
        $past4Days = date($format, strtotime('-4 day' . $date));
        $nowDate = $date;
        $NewpapularCode = $queryBuilder
            ->select(
                'v.id,IDENTITY(v.viewcount) as offerId,
                ((sum(v.onClick)) / (DATE_DIFF(CURRENT_TIMESTAMP(),o.startDate))) as pop, o.startDate as startdate'
            )
            ->from('KC\Entity\ViewCount', 'v')
            ->where(
                'v.updated_at <=' . "'$nowDate' AND v.updated_at >="
                . "'$past4Days'"
            )
            ->leftJoin('v.viewcount', 'o')
            ->groupBy('v.viewcount')
            ->orderBy('v.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $lastPostionOffer = \Zend_Registry::get('emLocale')->createQueryBuilder()
            ->select('p.position')
            ->from('KC\Entity\PopularCode', 'p')
            ->orderBy('p.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (sizeof($lastPostionOffer) > 0) {
            $lastPos = intval($lastPostionOffer[0]['position']) + 1;
        } else {
            $lastPos = 1;
        }

        $allExistingOffer = \Zend_Registry::get('emLocale')->createQueryBuilder()
            ->select('p.id,o.id as offerId,p.type,p.position')
            ->from('KC\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->orderBy('p.position')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $temp = array();

        foreach ($NewpapularCode as $popular) {
            $temp[$popular['offerId']] = $popular;
        }

        $newArray = array();
        foreach ($temp as $key => $t) {
            if (sizeof($allExistingOffer) > 0) {
                foreach ($allExistingOffer as $exist) {
                    if ($key == $exist['offerId']) {
                        $Ar = array(
                            'type' => $exist['type'],
                            'offerId' => $exist['offerId'],
                            'position' => $exist['position']
                        );
                        $newArray[$key] = $Ar;
                    } else {
                        if (!array_key_exists($key, $newArray)) {
                            $Ar = array(
                                'type' => 'AT',
                                'offerId' => $key,
                                'position' => $lastPos
                            );
                            $newArray[$key] = $Ar;
                            
                            if (!array_key_exists($exist['offerId'], $temp)) {
                                $lastPos++;
                            }
                        }
                    }
                }
            } else {
                $Ar = array(
                    'type' => 'AT',
                    'offerId' => $key,
                    'position' => $lastPos
                );
                $newArray[$key] = $Ar;
                $lastPos++;
            }
        }

        foreach ($newArray as $p) {
            $pc = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\PopularCode')
                ->findBy(array('popularcode' => $p['offerId']));
            if (sizeof($pc) > 0) {
            } else {
                $pc = new \KC\Entity\PopularCode();
                $pc->type = 'AT';
                $pc->popularcode = \Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $p['offerId']);
                $pc->position = $p['position'];
                $pc->deleted = 0;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($pc);
                \Zend_Registry::get('emLocale')->flush();
            }
        }
    }

    public static function frontendGetPopularCode()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select(
                'p.id,o.title,p.type,p.position,IDENTITY(p.popularcode) as offerId, o.id as offersId, o.title,
                o.Visability as visability, o.couponCode as couponcode, o.refOfferUrl as refofferurl,
                o.startDate as startdate, o.endDate as enddate, o.exclusiveCode as exclusivecode,
                o.editorPicks as editorpicks,o.extendedOffer as extendedoffer,o.discount, o.authorId,
                o.authorName, IDENTITY(o.shopOffers) as shopid, IDENTITY(o.logo) as offerlogoid,
                o.userGenerated, o.approved,img.id, img.path, img.name'
            )
            ->from('KC\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.logo', 'img')
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getClickId($offerId, $ip)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return  $queryBuilder
            ->select("v.id, v.memberId")
            ->from('KC\Entity\ViewCount', 'v')
            ->where("v.IP = ". $ip)
            ->andWhere("v.viewcount = ". $offerId)
            ->andWhere("v.onClick = 1")
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function getAmountClickoutsLastWeek()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $past7Days = date($format, strtotime('-7 day' . $date));
        $data = $queryBuilder
            ->select("count(v.id) as amountclickouts")
            ->from('KC\Entity\ViewCount', 'v')
            ->where(
                $queryBuilder->expr()->between(
                    'v.created_at',
                    $queryBuilder->expr()->literal($past7Days),
                    $queryBuilder->expr()->literal($date)
                )
            )
            ->andWhere("v.onClick = 1")
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function processViewCount($id = null)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->update('KC\Entity\ViewCount', 'v')
            ->set('v.counted', 1)
            ->where('v.counted = 0');

        if ($id) {
            $query = $query->andWhere('v.viewcount = '. $id);
        }

        $query->getQuery()->execute();
    }
}
