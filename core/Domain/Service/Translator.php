<?php
namespace Core\Domain\Service;

use Core\Domain\Adapter\TranslatorInterface;

class Translator
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function addTranslation($params)
    {
        return $this->translator->addTranslation($params);
    }

    public function translate($variable)
    {
        return $this->translator->translate($variable);
    }
} 