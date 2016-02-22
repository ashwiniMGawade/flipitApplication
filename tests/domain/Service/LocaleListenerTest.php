<?php
namespace Service;

use Core\Service\LocaleLister;

class LocaleListenerTest extends \Codeception\TestCase\Test
{
    public function testLocaleListenerReturnsNonEmptyValue()
    {
        $translator = new LocaleLister();
        $this->assertNotEmpty($translator->getAllLocals());
    }
}
