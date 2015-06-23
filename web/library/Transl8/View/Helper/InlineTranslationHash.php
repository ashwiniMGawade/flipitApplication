<?php

class Transl8_View_Helper_InlineTranslationHash extends Zend_View_Helper_Abstract
{

    public function inlineTranslationHash()
    {
        date_default_timezone_set('Europe/Amsterdam');
        return md5(date('Y').'-'.date('m').'-'.date('d').':'.date('H'));
    }
}