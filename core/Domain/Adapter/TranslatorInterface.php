<?php
namespace Core\Domain\Adapter;

interface TranslatorInterface
{
    public function addTranslation($type);

    public function translate($params);
}
