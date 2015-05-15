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
        $existedRecord = self::checkQueuedUrl($url, $refreshTime);
        if (empty($existedRecord)) {
            $v          = new \KC\Entity\Varnish();
            $v->url     = rtrim($url, '/');
            $v->status  = 'queue';
            $v->created_at = new \DateTime('now');
            $v->updated_at = new \DateTime('now');
            if (!empty($refreshTime)) {
                $v->refresh_time = new \DateTime($refreshTime);
            }else{
                $v->refresh_time = new \DateTime('now');
            }
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $entityManagerLocale->persist($v);
            $entityManagerLocale->flush();
            return $v->getId();
        }
    }

    // preform the refresh on the varnish server
    private function refreshVarnish($url)
    {
        $curl = curl_init(rtrim($url, '/'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "REFRESH");
        curl_exec($curl);
    }

    // process all the urls waiting to refresh
    public function processQueue()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v')
            ->from('KC\Entity\Varnish', 'v')
            ->andWhere($queryBuilder->expr()->eq('v.status', 'queue'));
        $queue = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($queue)) {
            foreach ($queue as $page) {
                sleep(1.5);
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
    public static function checkQueuedUrl($url, $refreshTime = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('v.id')
            ->from('KC\Entity\Varnish', 'v')
            ->where($queryBuilder->expr()->eq('v.url', $queryBuilder->expr()->literal(rtrim($url, '/'))))
            ->andWhere("v.status = 'queue'");
        if (!empty($refreshTime)) {
            $query = $query->andWhere(
                $queryBuilder->expr()->eq('v.refresh_time', $queryBuilder->expr()->literal($refreshTime))
            );
        }
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
}
