<?php
namespace Api\Controller\Helper;

use \Core\Domain\Factory\SystemFactory;

class Authenticate
{
    public function authenticate($apiKey)
    {
        $apiKey = SystemFactory::getApiKey()->execute(array('api_key'=>$apiKey));
        if (false === is_object($apiKey)) {
            return false;
        }
        return true;
    }
}
