<?php
namespace Phpingguo\Tests\Phpingguo\Core;

use Phpingguo\System\Core\AuraDIWrapper;
use Phpingguo\System\Enums\Variable;

class AuraDIWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function providerGetClass()
    {
        return [
            [ 'Phpingguo\System\Request\Request' ],
            [ 'Phpingguo\System\Request\RequestParser' ],
            [ Variable::INTEGER ],
            [ Variable::UNSIGNED_INT ],
            [ Variable::FLOAT ],
            [ Variable::UNSIGNED_FLOAT ],
            [ Variable::STRING ],
            [ Variable::TEXT ],
            [ 'Phpingguo\System\Validator\Options' ],
            [ 'Phpingguo\System\Filter\Pre\FilterHost' ],
            [ 'Phpingguo\System\Filter\Post\FilterHost' ],
            [ 'Phpingguo\System\Filter\Input\FilterHost' ],
            [ 'Phpingguo\System\Filter\Output\FilterHost' ],
        ];
    }
    
    /**
     * @dataProvider providerGetClass
     */
    public function testGetClass($class_name)
    {
        $this->assertInstanceOf($class_name, AuraDIWrapper::init()->get($class_name));
    }
}
