<?php
namespace Core\Domain\Service;

use \Core\Domain\Adapter\PurifierInterface;
use \HTMLPurifier;

class Purifier implements PurifierInterface
{
    protected $purifier;

    public function __construct()
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', '/tmp');
        $this->purifier = new HTMLPurifier($config);
    }

    public function purify($params)
    {
        if (!is_array($params)) {
            if (!is_object($params)) {
                return $this->purifier->purify($params);
            } else {
                return $params;
            }
        }

        $filteredParams = array();

        foreach ($params as $key => $value) {
            if (!is_object($value)) {
                $filteredParams[$key] = $this->purifier->purify($value);
            } else {
                $filteredParams[$key] = $value;
            }
        }
        return $filteredParams;
    }
}
