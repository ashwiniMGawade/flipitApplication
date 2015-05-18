<?php

namespace KC\Repository;

class Varnish extends \KC\Entity\Varnish
{
    public function __contruct($connectionName = false)
    {
        if (! $connectionName) {
            $connectionName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }

    public function addUrl($url, $refreshTime = '')
    {
        $currentTime = new \DateTime('now');
        $validateRefreshTime = empty($refreshTime) ? $currentTime->format('Y-m-d h:i:s') : $refreshTime;
        $existedRecord = self::checkQueuedUrl($url, $validateRefreshTime);
        if (empty($existedRecord)) {
            $varnish = new \KC\Entity\Varnish();
            $varnish->url = rtrim($url, '/');
            $varnish->status = 'queue';
            $varnish->created_at = new \DateTime('now');
            $varnish->updated_at = new \DateTime('now');
            if (!empty($refreshTime)) {
                $varnish->refresh_time = new \DateTime($refreshTime);
            } else {
                $varnish->refresh_time = new \DateTime('now');
            }
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($varnish);
            $entityManagerLocale->flush();
            return $varnish->getId();
        }
    }

    private function refreshVarnish($url)
    {
        $curl = curl_init(rtrim($url, '/'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "REFRESH");
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_exec($curl);
        if (!curl_errno($curl)) {
            $info = curl_getinfo($curl);
            echo "URL: " . $info['url'] . "\n";
            echo "Time: " . $info['total_time'] . "\n\n";
        }
        curl_close($curl);
    }

    public function processQueue()
    {
        $queue = self::getAllUrlsByRefreshTime();
        if (!empty($queue)) {
            foreach ($queue as $page) {
                self::refreshVarnish($page['url']);
                self::removeFromQueue($page['id']);
            }
        }
    }

    private function processed($id)
    {
        $page = \Zend_Registry::get('emLocale')->find('KC\Entity\Varnish', $id);
        if (!empty($page)) {
            $varnish = \Zend_Registry::get('emLocale')->find('KC\Entity\Varnish', $id);
            $varnish->status = 'processed';
            $entityManagerLocale->persist($varnish);
            $entityManagerLocale->flush();
        }
    }

    private function removeFromQueue($id)
    {
        $varnish = \Zend_Registry::get('emLocale')->find('KC\Entity\Varnish', $id);
        $entityManagerLocale->remove($varnish);
        $entityManagerLocale->flush();
    }

    public static function checkQueuedUrl($url, $refreshTime)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v.id')
            ->from('KC\Entity\Varnish', 'v')
            ->where($queryBuilder->expr()->eq('v.url', $queryBuilder->expr()->literal(rtrim($url, '/'))))
            ->andWhere("v.status = 'queue'")
            ->andWhere(
                $queryBuilder->expr()->eq('v.refresh_time', $queryBuilder->expr()->literal($refreshTime))
            );
        $query = $query->setMaxResults(1);
        $varnishUrls = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $varnishUrls;
    }

    public static function getVarnishUrlsCount()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('count(v.id) as Vcount')
            ->from('KC\Entity\Varnish', 'v');
        $varnishUrlsCount = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($varnishUrlsCount) ? $varnishUrlsCount[0]['Vcount'] : 0;
    }

    public static function getAllUrlsByRefreshTime()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentTime = FrontEnd_Helper_viewHelper::convertCurrentTimeToServerTime();
        $refreshUrls = $queryBuilder
            ->select('v')
            ->from('KC\Entity\Varnish', 'v')
            ->andWhere(
                $queryBuilder->expr()->lte('v.refresh_time', $queryBuilder->expr()->literal($currentTime))
            )
            ->orWhere('v.refresh_time is NULL')
            ->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $refreshUrls;
    }
}
