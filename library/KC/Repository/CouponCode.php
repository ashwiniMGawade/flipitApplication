<?php
namespace KC\Repository;

class CouponCode extends \KC\Entity\CouponCode
{
    public static function returnAvailableCoupon($id, $pageType = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $availableCoupon = $queryBuilder
        ->select('c.code')
        ->from('KC\Entity\CouponCode', 'c')
        ->where("c.offer = " . $id)
        ->andWhere('c.status=1')
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $availableCoupon;
    }

    public static function updateCodeStatus($id, $code, $status = 0)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder->update('KC\Entity\CouponCode', 'c')
            ->set('c.status', $status)
            ->where("c.code = '" . $code ."'")
            ->andWhere('c.offer ='.  $id)
            ->getQuery()
            ->execute();

        $totalAvailCode  = \Zend_Registry::get('emLocale')->createQueryBuilder()
            ->select('count(cc.id) as couponcount')
            ->from('KC\Entity\CouponCode', 'cc')
            ->where("cc.offer = " . $id)
            ->andWhere('cc.status=1')
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if ($totalAvailCode['couponcount'] == 0) {
            \KC\Repository\Offer::updateCache($id);
            $varnishObj = new \KC\Repository\Varnish();
            $varnishObj->addUrl(HTTP_PATH);
            $varnishObj->addUrl(HTTP_PATH . \FrontEnd_Helper_viewHelper::__link('link_nieuw'));
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_top-20'));
            if (LOCALE == '') {
                if (defined(HTTP_PATH_FRONTEND)) {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
                } else {
                    $varnishObj->addUrl(HTTP_PATH  . 'marktplaatsfeed');
                    $varnishObj->addUrl(HTTP_PATH . 'marktplaatsmobilefeed');
                }
            }
            $varnishUrls = \KC\Repository\Offer::getAllUrls($id);

            if (isset($varnishUrls) && count($varnishUrls) > 0) {
                foreach ($varnishUrls as $varnishValue) {
                    $varnishObj->addUrl(HTTP_PATH . $varnishValue);
                }
            }
        }
    }

    public static function exportCodeList($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $codeList = $queryBuilder
            ->select('c.code,c.status')
            ->from('KC\Entity\CouponCode', 'c')
            ->where("c.offer = ". $id)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $codeList;
    }

    public static function returnCodesDetail($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('count(c.id) as total')
            ->from('KC\Entity\CouponCode', 'c')
            ->addSelect(
                "(SELECT count(cc.status) FROM \KC\Entity\CouponCode cc WHERE cc.offer = c.offer and cc.status = 0) as used"
            )
            ->addSelect(
                "(SELECT count(ccc.status) FROM \KC\Entity\CouponCode ccc WHERE ccc.offer = c.offer and ccc.status = 1) as available"
            )
            ->where("c.offer = " . $id)
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }
}
