<?php
namespace Service;

use Core\Service\Translator;

class TranslatorTest extends \Codeception\TestCase\Test
{
    public function testTranslator()
    {
        $translator = new Translator(
            new \Zend_Translate(array('adapter' => 'gettext', 'disableNotices' => true)),
            'en',
            'en_NL'
        );
        $this->assertEquals('Some text', $translator->translate('Some text'));
    }
}
