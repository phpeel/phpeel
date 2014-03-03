<?php
namespace Phpingguo\Tests\Phpingguo\Core;

use Phpingguo\CitronDI\AuraDIWrapper;

class AuraDIWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function providerGetClass()
    {
        return [
            [ 'system', 'Phpingguo\System\Request\Request' ],
            [ 'system', 'Phpingguo\System\Request\RequestParser' ],
            [ 'system', 'Phpingguo\System\Validator\Options' ],
            [ 'system', 'Phpingguo\System\Filter\Pre\FilterHost' ],
            [ 'system', 'Phpingguo\System\Filter\Post\FilterHost' ],
            [ 'system', 'Phpingguo\System\Filter\Input\FilterHost' ],
            [ 'system', 'Phpingguo\System\Filter\Output\FilterHost' ],
        ];
    }
    
    /**
     * @dataProvider providerGetClass
     */
    public function testGetClass($group_name, $class_name)
    {
        $this->assertInstanceOf($class_name, AuraDIWrapper::init($group_name)->get($class_name));
    }
}
