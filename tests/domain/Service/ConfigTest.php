<?php
namespace Service;

use Core\Service\Config;

class ConfigTest extends \Codeception\TestCase\Test
{
    public function testConfigGetEnvironmentReturnsNonEmpty()
    {
        $config = new Config();
        $this->assertNotEmpty($config->getEnvironment());
    }

    public function testConfigGetEnvironmentReturnsDefinedValue()
    {
        define('APPLICATION_ENV', 'testing');
        $config = new Config();
        $this->assertNotEmpty($config->getEnvironment());
    }
}
