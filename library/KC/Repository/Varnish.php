<?php

namespace KC\Repository;

class Varnish extends \KC\Entity\Varnish
{
    public function __contruct($connName = false)
    {
        if (! $connName) {
            $connName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connName, $connName);
    }

    // add an url to the queue
    public function addUrl($url, $refreshTime = '')
    {
        # add url if it is not queued
        $validateRefreshTime = $refreshTime;
        if (empty($refreshTime)) {
            $validateRefreshTime = new \DateTime('now');
            $validateRefreshTime = $validateRefreshTime->format('Y-m-d h:i:s');
        }
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

    // preform the refresh on the varnish server
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

    // process all the urls waiting to refresh
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

    // set the status for this record to 'processed'
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

    // remove the record from the Queue
    private function removeFromQueue($id)
    {
        $varnish = \Zend_Registry::get('emLocale')->find('KC\Entity\Varnish', $id);
        $entityManagerLocale->remove($varnish);
        $entityManagerLocale->flush();
    }

    // check a url is already in queue or not
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
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return  $data;
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
