<?php
namespace Core\Domain\Factory;

use \Core\Service\Translator;

class TranslationsFactory
{
    public static function translator($locale, $languageLocale)
    {
        return new Translator(
            new \Zend_Translate(array('adapter' => 'gettext', 'disableNotices' => true)),
            $locale,
            $languageLocale
        );
    }
}
