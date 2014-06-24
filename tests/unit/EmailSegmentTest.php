<?php
use Codeception\Util\Stub;

class EmailSegmentTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeTester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        $campainModel = new EmailCampain();
        $campain = array('sender' => 'd@d.com','subject' => 'koek');
        $save = $campainModel->saveForm($campain);
        $this->assertGreaterThan(0, $save);
    }


}