<?php
namespace Phpingguo\System\Tests\Request;

use Phpingguo\System\Enums\HttpMethod;
use Phpingguo\System\Request\RequestData;

class RequestDataTest extends \PHPUnit_Framework_TestCase
{
    public function providerGetProperties()
    {
        return [
            [ HttpMethod::GET, 'top', null, 'index', [] ],
            [ HttpMethod::GET, 'top', 1.0, 'index', [] ],
            [ HttpMethod::GET, 'top', null, 'index', [ 'name' => '緒方 智絵里' ] ],
            [ null, null, null, null, [] ]
        ];
    }
    
    /**
     * @dataProvider providerGetProperties
     */
    public function testGetProperties($method, $module, $version, $scene, $params)
    {
        $instance = new RequestData($method, $module, $version, $scene, $params);
        
        $this->assertInstanceOf('Phpingguo\System\Request\RequestData', $instance);
        
        $this->assertSame($method ?: HttpMethod::GET, $instance->getMethod()->getValue());
        $this->assertSame($module ?: 'top', $instance->getModuleName());
        $this->assertSame($version ?: 1.0, $instance->getApiVersion());
        $this->assertSame($scene ?: 'index', $instance->getSceneName());
        $this->assertSame($params, $instance->getParameters());
        
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                $this->assertSame($value, $instance->getParameter($key));
            }
        }
    }
    
    public function providerSetParameter()
    {
        return [
            [ 'name', '緒方 智絵里', null ],
            [ null, '安部 菜々', 'InvalidArgumentException' ],
            [ '', '前川 みく', 'InvalidArgumentException' ],
            [ [], '佐久間 まゆ', 'InvalidArgumentException' ],
            [ new \stdClass(), '島村 卯月', 'InvalidArgumentException' ]
        ];
    }
    
    /**
     * @dataProvider providerSetParameter
     */
    public function testSetParameter($key, $value, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $instance = new RequestData(HttpMethod::GET, 'top', null, 'index', []);
        $instance->setParameter($key, $value);
    }
    
    public function providerIsExistParameter()
    {
        return [
            [ [ 'name' => '緒方 智絵里' ], 'name', true, null ],
            [ [ 'name' => '安部 菜々' ], 'age', false, null ],
            [ [ 'name' => '前川 みく' ], null, false, 'InvalidArgumentException' ],
            [ [ 'name' => '佐久間 まゆ' ], 0, false, 'InvalidArgumentException' ],
            [ [ 'name' => '島村 卯月' ], '', false, 'InvalidArgumentException' ],
            [ [ 'name' => '緒方 智絵里' ], true, false, 'InvalidArgumentException' ],
            [ [ 'name' => '緒方 智絵里' ], false, false, 'InvalidArgumentException' ],
            [ [ 'name' => '安部 菜々' ], [], false, 'InvalidArgumentException' ],
            [ [ 'name' => '前川 みく' ], '0', false, null ],
            [ [ 'name' => '佐久間 まゆ' ], 0.0, false, 'InvalidArgumentException' ],
        ];
    }
    
    /**
     * @dataProvider providerIsExistParameter
     */
    public function testIsExistParameter($set_params, $name, $value, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $instance = new RequestData(HttpMethod::GET, 'top', null, 'index', $set_params);
        $this->assertEquals($value, $instance->isExistParameter($name));
    }
}
