<?php

namespace Core\Service;

class LocaleLister
{
    public function getAllLocals()
    {
        $config = (new Config)->getConfig();
        $locales = array();
        foreach ($config->doctrine as $locale => $dsn) {
            if (is_object($dsn)) {
                $locales[] = $locale;
            }
        }
        return $locales;
    }
}
