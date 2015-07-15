<?php
use \Aws\Ec2\Ec2Client;

class Varnish extends BaseVarnish
{

    public function __contruct($connectionName = false)
    {
        if (!$connectionName) {
            $connectionName = "doctrine_site" ;
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }

    public function addUrl($url, $refreshTime = '')
    {
        $validateRefreshTime = empty($refreshTime) ? date('Y-m-d h:i:s') : $refreshTime;
        $existedRecord = self::checkQueuedUrl($url, $validateRefreshTime);
        if (empty($existedRecord)) {
            $varnish = new Varnish();
            $varnish->url = rtrim($url, '/');
            $varnish->status = 'queue';
            if (!empty($refreshTime)) {
                $varnish->refresh_time = $refreshTime;
            } else {
                $varnish->refresh_time = date('Y-m-d h:i:s');
            }
            $varnish->save();
            return $varnish->id;
        }
    }

    private function getEc2InstanceIpAddresses()
    {
        $ec2Client = Ec2Client::factory(
            array(
                'key'    => 'AKIAIYSHHZL73F2VVZWA',
                'secret' => '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',
                'region' => 'eu-west-1',
                'version'=> '2015-04-15'
            )
        );

        $result = $ec2Client->DescribeInstances(
            array('Filters' => array(
                array(
                    'Name' => 'tag:aws:autoscaling:groupName',
                    'Values' => array('flipit-production-VarnishAutoscalingGroup-11QGPT21DBMSP')
                )
            ))
        );
        $reservations = $result['Reservations'];
        $ipAddresses = array();
        foreach ($reservations as $reservation) {
            $instances = $reservation['Instances'];
            foreach ($instances as $instance) {
                $ipAddresses[] = $instance['PrivateIpAddress'];
            }
        }
        return $ipAddresses;
    }

    private function refreshVarnish($url)
    {
        $ipAddresses = $this->getEc2InstanceIpAddresses();
        if (!empty($ipAddresses)) {
            foreach ($ipAddresses as $ipAddress) {
                $curl = curl_init(rtrim($url, '/'));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "REFRESH");
                curl_setopt($curl, CURLOPT_NOBODY, true);
                curl_setopt($curl, CURLOPT_INTERFACE, $ipAddress);
                curl_exec($curl);
                if (!curl_errno($curl)) {
                    $info = curl_getinfo($curl);
                    echo "URL: " . $info['url'] . "\n";
                    echo "Time: " . $info['total_time'] . "\n\n";
                }
                curl_close($curl);
            }
        }
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
        $page = Doctrine_core::getTable('Varnish')->find($id)->toArray();
        if (!empty($page)) {
            $update = Doctrine_Query::create()
                ->update('Varnish')
                ->set('status', "'processed'")
                ->where('id = "'.$id.'"')
                ->execute();
        }
    }

    private function removeFromQueue($id)
    {
        Doctrine_Query::create()->delete()->from('Varnish')->where("id = ".$id)->execute();
    }

    public static function checkQueuedUrl($url, $refreshTime)
    {
        $query = Doctrine_Query::create()->select('id')
            ->from("Varnish")
            ->where("url = ? ", rtrim($url, '/'))
            ->andWhere("status = 'queue'")
            ->andWhere("refresh_time = '".$refreshTime."'")
            ->limit(1)
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
