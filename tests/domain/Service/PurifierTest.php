<?php
namespace Service;

use Core\Domain\Service\Purifier;

class PurifierTest extends \Codeception\TestCase\Test
{
    public function testPurifierWithInvalidInputAsAString()
    {
        $purifiedData = ((new Purifier())->purify('This is dummy input.<script>Do some hacking.</script>'));
        $this->assertEquals('This is dummy input.', $purifiedData);
    }

    public function testPurifierWithInputAsAnObject()
    {
        $purifiedData = ((new Purifier())->purify(new \stdClass()));
        $this->assertEquals(new \stdClass(), $purifiedData);
    }

    public function testPurifierWithInputAsAnArray()
    {
        $params = array(
            'id'    => 12,
            'name'  => 'This is dummy input.<script>Do some hacking.</script>',
            'optional' => new \stdClass()
        );
        $purifiedData = ((new Purifier())->purify($params));
        $this->assertEquals(array('id'=>12, 'name'=>'This is dummy input.', 'optional' => new \stdClass()), $purifiedData);
    }
}
