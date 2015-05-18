<?php
/**
 * Varnish
 *
 * This class contains methods to refresh cached url's in our varnish server
 * @author Daniel
 */

class Varnish extends BaseVarnish
{

    public function __contruct($connName = false)
    {
        if(! $connName) {
            $connName = "doctrine_site" ;
        }


        Doctrine_Manager::getInstance()->bindComponent($connName, $connName);
    }

    // add an url to the queue
    public function addUrl($url, $refreshTime = '')
    {
        # add url if it is not queued
        if (!self::checkQueuedUrl($url, $refreshTime)) {
            $v = new Varnish();
            $v->url = rtrim($url, '/');
            $v->status = 'queue';
            $v->refresh_time = $refreshTime;
            $v->save();
            return $v->id;
        }
    }

    // preform the refresh on the varnish server
    private function refreshVarnish($url)
    {
        $curl = curl_init(rtrim($url, '/'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "REFRESH");
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_exec($curl);
        if(!curl_errno($curl)) {
                $info = curl_getinfo($curl);
                echo "URL: " . $info['url'] . "\n";
                echo "Time: " . $info['total_time'] . "\n\n";
        }
        curl_close($curl);
    }

    // process all the urls waiting to refresh
    public function processQueue()
    {
        $queue = Doctrine_core::getTable('Varnish')->findBy('status', 'queue')->toArray();
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
        $page = Doctrine_core::getTable('Varnish')->find($id)->toArray();
        if (!empty($page)) {
            $update = Doctrine_Query::create()->update('Varnish')
            ->set('status', "'processed'")
            ->where('id = "'.$id.'"')
            ->execute();
        }
    }

    // remove the record from the Queue
    private function removeFromQueue($id)
    {
        Doctrine_Query::create()->delete()->from('Varnish')->where("id = ".$id)->execute();
    }

    // check a url is already in queue or not
    public static function checkQueuedUrl($url, $refreshTime ='')
    {
        $query = Doctrine_Query::create()->select('id')
            ->from("Varnish")
            ->where("url = ? ", rtrim($url, '/'))
            ->andWhere("status = 'queue'");

        if (!empty($refreshTime)) {
            $query = $query->andWhere("refresh_time = '".$refreshTime."'");
        }

        $query = $query->limit(1)
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $query;
    }

    public static function getVarnishUrlsCount()
    {
        $varnishUrlsCount = Doctrine_Query::create()->select('count(*)')
            ->from("Varnish")
            ->fetchArray();
        return !empty($varnishUrlsCount) ? $varnishUrlsCount[0]['count'] : 0;
    }

    public static function getAllUrlsByRefreshTime()
    {
        $currentTime = FrontEnd_Helper_viewHelper::convertCurrentTimeToServerTime();
        $refreshUrls = Doctrine_Query::create()
            ->select('v.*')
            ->from("Varnish v")
            ->where('v.refresh_time <='."'".$currentTime."'")
            ->orWhere('v.refresh_time is NULL')
            ->fetchArray(null, Doctrine::HYDRATE_ARRAY);
        return $refreshUrls;
    }

    public static function refreshVarnishUrlsByCron($refreshUrls)
    {
        $varnish = new Varnish();
        foreach ($refreshUrls as $refreshUrl) {
            if (!empty($refreshUrl['id'])) {
                $varnish->refreshVarnish($refreshUrl['url']);
                $varnish->removeFromQueue($refreshUrl['id']);
            }
        }
    }
}
