<?php
namespace Core\Domain\Factory;

use Core\Domain\Service\Translator;
use Core\Domain\Service\Zend_Translator;

class TranslationsFactory
{
    public static function keyValueTranslation($locale, $languageLocale, $addTranslations)
    {
        return new Translator(new Zend_Translator($locale, $languageLocale, $addTranslations));
    }
}
