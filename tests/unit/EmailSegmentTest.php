<?php
use Codeception\Util\Stub;

class EmailSegmentTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $tester;

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
        //$this->assertGreaterThan(0, $save);
        //$this->assertEquals('2', $save);
        $this->tester->seeInTable('EmailCampain', array('sender' => 'd@d.com','subject' => 'koek'));
    }
}
