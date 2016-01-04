<?php
namespace Core\Domain\Service;

use Core\Domain\Adapter\TranslatorInterface;

class Zend_Translator implements TranslatorInterface
{
    private $translator;
    private $locale;
    private $languageLocale;

    public function __construct($locale, $languageLocale, $addTranslations = false)
    {
        $this->locale = $locale;
        $this->languageLocale = $languageLocale;
        $this->translator = new \Zend_Translate(array(
            'adapter' => 'gettext',
            'disableNotices' => true)
        );
        if (true === $addTranslations) {
            $this->addTranslations();
        }
    }

    public function addTranslation($type = null)
    {
        $fileSuffix = (false === empty($this->locale) && 'en' != $this->locale) ? '_'.$this->locale : '';
        $pathSuffix = (false === empty($this->locale) && 'en' != $this->locale) ? '/'.$this->locale : '';

        $fileName = $type . strtoupper($fileSuffix) .'.mo';
        $this->translator->addTranslation(
            array(
                'content'   => __DIR__.'/../../../web/public'.$pathSuffix.'/language/'.$fileName,
                'locale'    => $this->languageLocale
            )
        );
    }

    public function addTranslations()
    {
        $this->addTranslation('frontend_php');
        $this->addTranslation('email');
        $this->addTranslation('form');
        $this->addTranslation('po_links');
    }

    public function translate($variable)
    {
        return $this->translator->translate($variable);
    }
} 