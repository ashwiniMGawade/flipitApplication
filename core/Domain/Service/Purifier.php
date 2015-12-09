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
        // Set some HTML5 properties
        $config->set('HTML.DefinitionID', 'html5-definitions');
        $config->set('HTML.DefinitionRev', 1);
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('nav',     'Block', 'Flow', 'Common');
            $def->addElement('article', 'Block', 'Flow', 'Common');
            $def->addElement('i', 'Block', 'Flow', 'Common');
            $def->addElement('aside',   'Block', 'Flow', 'Common');
            $def->addElement('header',  'Block', 'Flow', 'Common');
            $def->addElement('footer',  'Block', 'Flow', 'Common');
        }
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
