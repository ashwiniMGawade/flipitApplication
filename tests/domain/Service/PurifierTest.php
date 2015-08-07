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

    public function testPurifierWithInputAsAnArray()
    {
        $params = array(
            'id'    => 12,
            'name'  => 'This is dummy input.<script>Do some hacking.</script>',
            'optional' => NULL
        );
        $purifiedData = ((new Purifier())->purify($params));
        $this->assertEquals(array('id'=>12, 'name'=>'This is dummy input.', 'optional' => NULL), $purifiedData);
    }

}
