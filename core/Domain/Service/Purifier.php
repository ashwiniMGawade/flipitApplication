<?php
namespace Core\Domain\Service;

use \Core\Domain\Adapter\HTMLPurifierInterface;
use \HTMLPurifier;

class Purifier implements HTMLPurifierInterface
{
    protected $purifier;

    public function __construct()
    {
        $this->purifier = new HTMLPurifier();
    }

    public function purify($params)
    {
        if (!is_array($params)) {
            return $this->purifier->purify($params);
        }

        $filteredParams = array();

        foreach ($params as $key=>$value ) {
            $filteredParams[$key] = $this->purifier->purify($value);
        }
        return $filteredParams;
    }
}
